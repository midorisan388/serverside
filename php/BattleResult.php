<?php
    ini_set('display_errors',"On");
    error_reporting(E_ALL);  
    define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."\serverside");
    require_once(ROOT_DIR."\php\battlePHP\BaseBattler.php");
    require_once(ROOT_DIR."\php\battlePHP\EnemyBaseClass.php");
    require_once(ROOT_DIR."\php\getDataMusic.php");

    session_start();
    
    if(isset($_POST["return"])){
        echo "return quest page";
        UnsetQuestSession();
        //クエストページに戻る
        header( "Location: /game/page?page-link=quest" );
        exit();
    }else{
        require_once(ROOT_DIR."\php\UpdateUserStageId.php");
        UpdateStageId(5,$_SESSION["userid"]);
        InitBattleData();
    }

    function InitBattleData(){
        //初期値設定
        //$uId=$_SESSION['userid'];
        $maxCmb=$score=$scoreBonus=$perfCnt=$greatCnt=$goodCnt=$badCnt=$missCnt=0;
        $dataMaxScore =$dataScoreBonus = $dataMaxCmb =0;
        $enemyData=array(); //殲滅率計算用
        $partyData=array();

        $uId=$_SESSION["userid"];
        $qId=$_SESSION['QUEST_ID'];


        //SQL接続(クリア状況獲得用)-------------------------------------------------
        require( ROOT_DIR."\datas\sql.php");
        $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
        $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
        $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //------------------------------------------------------------------------
        $resData = array(
            "partyDt" =>$_SESSION["partySt"],
            "enemyDt" =>$_SESSION["enemyStMst"]
        );

        foreach ($_SESSION["partySt"] as $pd) {
            array_push($partyData,[$pd->imgId,$pd->getParameta()]);
        }
        foreach ($_SESSION["enemyStMst"] as $ed) {
            array_push($enemyData,$ed->getParameta());
        }

        $maxCmb = $_SESSION["maxComb"];
        $score = $_SESSION["Score"];
        $perfCnt = $_SESSION["pointCnt"]["PARF"];
        $greatCnt = $_SESSION["pointCnt"]["GREAT"];
        $goodCnt = $_SESSION["pointCnt"]["GOOD"];
        $badCnt = $_SESSION["pointCnt"]["BAD"];
        $missCnt = $_SESSION["pointCnt"]["MISS"];

        $userScoreData = $sql_list->query("SELECT * FROM {$userscore} WHERE UserID='{$uId}' AND QuestID={$qId}");
        $userScoreData = $userScoreData->fetch();
        $dataMaxScore = (int)$userScoreData["BestScore"];
        $dataScoreBonus = (int)$userScoreData["BonusScore"];
        $dataMaxCmb =   $userScoreData["BestCombo"];
        $scoreBonus = ColBonusScore($enemyData);
        $enemyDestroy=ColEnemyDestroy($enemyData);

        //音楽データ取得
        $musicData=getMusicData($_SESSION["QUESTID_LIST"][$qId]);

        //クエスト状況更新
        UpdateQuestData($uId,$qId,$userstory,$sql_list);

        //スコア更新
        $scoreDt=array(
            "score"=>$score,
            "bonus"=>$scoreBonus,
            "cmb"=>$maxCmb,
            "perf"=>$perfCnt,
            "great"=>$greatCnt,
            "good"=>$goodCnt,
            "bad"=>$badCnt,
            "miss"=>$missCnt
        );

        //Exp計算
        $questFileName= ROOT_DIR."\datas\gameMasterData\questDataList.json";
        $questJson = file_get_contents($questFileName);
        $questJson = mb_convert_encoding($questJson, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $questArray = json_decode($questJson,true);

        $exp=(int)$questArray[$qId]["exp"];
        UpdateExp($uId,$sql_list,$exp,$userpartytable,$userplayable);

        UpdateQuestScore($_SESSION['userid'],$_SESSION['QUEST_ID'],$userscore,$scoreDt,$sql_list);
        //Respons();
       
        header('Content-Type: application/json; charset=utf-8');
        $resdata=array(
            "userId"=>$uId,
            "questData"=>[$_SESSION["QUESTID_LIST"][$qId]["title"],$musicData["musictitle"]],
            "partyData"=>$partyData,
            "enemyData"=>$enemyData,
            "enemyDestroy"=>$enemyDestroy,
            "ScoreData"=>[$dataMaxScore,$score],
            "BonusData"=>[$dataScoreBonus,$scoreBonus],
            "Combo"=>[$dataMaxCmb,$maxCmb],
            "pointCntMaxData"=>GetPointCntData($uId,$qId,$userscore,$sql_list),
            "pointCntData"=>array("Perf"=>$perfCnt,"Great"=>$goodCnt,"Good"=>$goodCnt,"Bad"=>$badCnt,"Miss"=>$missCnt)
        );
        $resjson = json_encode( $resdata, JSON_PRETTY_PRINT );

        echo $resjson;
    }

    function InitStetas($data){
        $enemyData= $data["enemyDt"];
        $partyData=$data["partyDt"];
    }

    function InitScoreData($uId, $sql_list){
        $maxCmb = $_SESSION["maxComb"];
        $score = $_SESSION["Score"];
        $perfCnt = $_SESSION["pointCnt"]["PARF"];
        $greatCnt = $_SESSION["pointCnt"]["GREAT"];
        $goodCnt = $_SESSION["pointCnt"]["GOOD"];
        $badCnt = $_SESSION["pointCnt"]["BAD"];
        $missCnt = $_SESSION["pointCnt"]["MISS"];

        $userScoreData = $sql_list->query("SELECT * FROM {$userscore} WHERE UserID='{$uId}' AND QuestID={$_SESSION['QUEST_ID']}");
        $userScoreData = $userScoreData->fetch();
        $dataMaxScore = $userScoreData["BestScore"];
        $dataScoreBonus = $userScoreData["BonusScore"];
        $dataMaxCmb =   $userScoreData["BestCombo"];
    }

    //最高記録時の判定表取得
    function GetPointCntData($uId,$qId,$userscore,$sql_list){
        $pointCnt=$sql_list->query("SELECT * FROM {$userscore} WHERE UserID='{$uId}' AND QuestID={$qId}");
        $pointCnt=$pointCnt->fetch();

        return array(
            "Perf"=>(int)$pointCnt["PerfectCnt"],
            "Great"=>(int)$pointCnt["GreatCnt"],
            "Good"=>(int)$pointCnt["GoodCnt"],
            "Bad"=>(int)$pointCnt["BadCnt"],
            "Miss"=>(int)$pointCnt["MissCnt"]
        );
    }

    function ColEnemyDestroy($eData){
        $totalHealth=0;
        $totalRestHealth=0;
        for($i=0; $i<count($eData);$i++){
            $totalHealth += $eData[$i]["hp"];
            $totalRestHealth +=$eData[$i]["currentDamage"];
        }

        return 100*($totalRestHealth/$totalHealth);//せん滅率
    }

    function ColBonusScore($eData){
        $totalHealth=0;
        $totalRestHealth=0;
        for($i=0; $i<count($eData);$i++){
            $totalHealth += $eData[$i]["hp"];
            $totalRestHealth +=$eData[$i]["hp"]-$eData[$i]["currentDamage"];
        }

        return $totalHealth-$totalRestHealth;//減らした体力文ボーナススコアにする
    }

    function UpdateQuestData($uId,$qId,$userstory,$sql_list){
        $questState = $sql_list->query("SELECT * FROM {$userstory} WHERE UserID ='{$uId}' AND QuestID={$qId}");
        $questState = $questState ->fetch();
        //初回クリアフラグ更新+クエスト解放処理
        if((int)$questState["State"] === 0){
            $sql_list->query("CALL UpdateQuestClearState('{$uId}',{$qId})");
            SetQuestData($uId,SetNestQuestIds($_SESSION["QUESTID_LIST"][$qId]["nextQuestId"]),$qId,$sql_list);
        }
    }

    function UpdateQuestScore($uId,$qId,$userscore,$scoreDt,$sql_list){
        $dataS = $sql_list->query("SELECT * FROM {$userscore} WHERE UserID='{$uId}' AND QuestID = {$qId}");
        $dataS=  $dataS->fetch();

        if( $dataS["BestScore"]+$dataS["BonusScore"] < $scoreDt["score"]+$scoreDt["bonus"]){
            $sql_list->query("CALL UpdateUserQuestScore('$uId',$qId,{$scoreDt['score']},{$scoreDt['bonus']},{$scoreDt['cmb']},{$scoreDt['perf']},{$scoreDt['great']},{$scoreDt['good']},{$scoreDt['bad']},{$scoreDt['miss']})");
        }
    }

    function UpdateExp($uId,$sql_list,$exp,$userpartytable,$userplayable){
        
        $partyIds = $sql_list->query("SELECT 1st,2nd,3rd,4th FROM {$userpartytable} WHERE UserID='{$uId}'");  
        $partyIds = $partyIds->fetch();
        $getExp=$exp;

        for($i=0; $i<4; $i++){
            //キャラのレベル取得
            $memberData = $sql_list->query("SELECT CharaLv,characterhaveExp FROM {$userplayable} WHERE UserID='{$uId}' AND CharaID={$partyIds[$i]}");
            $memberData = $memberData->fetch();
            $memberLv=$memberData["CharaLv"];
            $haveExp=$memberData["characterhaveExp"]+$getExp;
            $needExp = ($memberLv*1100+180*$memberLv/2)-$haveExp;

            while($haveExp > $needExp){
                $haveExp -= $needExp;
                $memberLv++;
                $needExp = ($memberLv*1100+180*$memberLv/2);
            }

            $sql_list->query("UPDATE {$userplayable} SET CharaLv={$memberLv}, characterhaveExp={$haveExp} WHERE UserID='{$uId}' AND CharaID={$partyIds[$i]}");
        }
    }

    function SetNestQuestIds($qData){
        $nextQuestIds=array();
        foreach ($qData as $id) {
            array_push($nextQuestIds, $id);
        }
        return $nextQuestIds;
    }

    //解放したクエストデータをデータベースに登録+クリアフラグ更新
    function SetQuestData( $uId,$qIds,$cId,$sql_list){
        foreach ($qIds as $id) {
            $sql_list->query("CALL setQuestData('{$uId}',{$id})");
        }
    }

    //クエストデータの初期化
    function UnsetQuestSession(){
        require_once(ROOT_DIR."\php\battlePHP\BattleSessionInit.php");

        unset($_SESSION["eventQuestParam"]);    
        unset($_SESSION["QUESTID_LIST"]);
        unset($_SESSION['QUEST_ID']);
    }
?>