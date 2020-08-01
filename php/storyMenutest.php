<?php
//クエストデータとアイテムIDの整合性チェック
error_reporting(E_ALL);
ini_set('error_log', '/tmp/php.log');
ini_set('log_errors', true);
ini_set('display_errors',"On" );

session_start();
define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."\serverside");
require_once(ROOT_DIR."\php\getDataMusic.php");

unset($_SESSION["eventQuestParam"]);
//unset($_SESSION["QUEST_ID"]);

function questDatagenerate($panelId_){
    //クエスト詳細情報生成
    $quest_data="クエスト詳細";//クエスト詳細情報
    $user_quest_stetas="ユーザの成績";//ユーザのクエスト成績

    $view_questdata = $_SESSION["QUESTID_LIST"][$panelId_];//クエストデータをパネルIDで取得
    $musicData = getMusicData($view_questdata);//音楽データ再取得

    //クエスト詳細情報　クエスト名とクリア状況
    $quest_data="<div id=quest_title>".$view_questdata["title"]."</div>
        <div id=clear_flag>未クリア</div>
        <div id=music_title>".$musicData["musictitle"]."</div>
        <div id=piece_count>ピース数/99</div>";

    //クエスト成績情報 仮表示
    $user_quest_stetas="
            クエスト成績
            <div class=nomal_stetas>
                <p class=nomal_title>ノーマル</p>
                <div id=max_score>ノーマル:20000</div>
                <div id=max_comb>ノーマル:20</div>
            </div>
            <div class=hard_stetas>
                <p class=hard_title>ハード</p>
                <div id=max_score>ハード:38000</div>
                <div id=max_comb>ハード:38</div>
            </div>
            <div class=extream_stetas>
                <p class=extream_title>エクストリーム</p>
                <div id=max_score>エクストリーム:120045</div>
                <div id=max_comb>エクストリーム:178</div>
            </div>";

    //クエスト情報データを返す
    return $returndata=array(
        "questviewdata"=>$quest_data,
        "userquestdata"=>$user_quest_stetas
    );
}

try{
    if(isset($_POST["quest_start"])){
        //3.クエスト開始が選択された
        $questid = $_SESSION["QUESTID_LIST"][$_POST["panelId"]];//[$_SESSION["PANEL_ID"]];//選択されているパネルIDからクエストデータ取得
        $_SESSION["QUEST_ID"] = $questid["ID"];//戦闘シーンで渡すクエストIDをセッションで保持

        UpdateEventParam($questid["ID"]);
        
        //unset($_SESSION["evnQuestIds"]);
        header("Location: /game/battle" );
        exit();

    }else if(isset($_POST["panelId"])){
        //2.クエストパネルが選択された いらなくなる？
        SelectQuestPanel();
    }else{
        //1.初回はパネルリスト生成
        require_once(ROOT_DIR."\php\UpdateUserStageId.php");
        UpdateStageId(3,$_SESSION["userid"]);
        InitQuestpanel();
    } 

}catch(PDOExeption $erro){
    echo "次のエラーが発生しました<br>";
    echo $erro->getmessage();
}

function InitQuestpanel(){
      //クエストリスト生成
      $quest_list_panels = questIdPanelGenerate();
      //クエスト一覧を返す
      $resdata=array(
          "questPages"=>$quest_list_panels[0],
          "questChapterPages"=>$quest_list_panels[1],
          "questData"=>$quest_list_panels[2],
          "questTitlePageNo"=>$quest_list_panels[3]
      );

      ResponsData($resdata);
}
   
function SelectQuestPanel(){
    
    $selected_panelId=(int)$_POST["panelId"];
    $_SESSION["PANEL_ID"] = $selected_panelId;//選択されているパネルIDを保持
    $_SESSION["QUEST_ID"] = $_SESSION["QUESTID_LIST"][$_SESSION["PANEL_ID"]]["ID"];

    //パネル生成
    //$quest_list_panels = questIdPanelGenerate();
    //クエスト表示情報取得
    $quest_dataview = questDatagenerate($selected_panelId);//パネルIDを渡してクエスト情報取得

    //クエスト一覧と詳細情報とユーザー成績を返す
    $resdata=array(
        "selectquestdata"=>[ $_SESSION["PANEL_ID"],  $_SESSION["QUEST_ID"] ],
        //"questpanel"=>$quest_list_panels,//クエスト一覧のパネル
        "questdata"=>$quest_dataview["questviewdata"],//クエスト詳細情報
        "userqueststetas"=>$quest_dataview["userquestdata"]//ユーザー成績
    );
    ResponsData($resdata);
}

//クエスト一覧表示パネル生成とパネルIDとクエストデータの対応付け
function questIdPanelGenerate(){
    require_once( ROOT_DIR."\php\getDataMusic.php");

    //クエストIDリスト生成
    $questArray=array();//全クエストデータ格納用
    $quest_panel_list=[];//ユーザ固有の表示クエストデータ格納(クエスト出現条件など出てきたときに本気出す)
   

    //クエストデータリストファイルパス
    $questFileName= ROOT_DIR."\datas\gameMasterData\questDataList.json";

    //クエストデータ取得
    $questJson = file_get_contents($questFileName);//jsonファイル読み込み
    $questArray = json_decode(mb_convert_encoding($questJson, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN'),true);//連想配列にエンコード

    //SQL接続(クリア状況獲得用)-----------------------------------------------------------------
    require( ROOT_DIR."\datas\sql.php");
    $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
    $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
    $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    //----------------------------------------------------------------------
    $index=0;//クエストID+1
    $quest_pages = [];
    $quest_data_list=[];

    $quest_chapter_id=-1;
    $chapter_quest_cnt=[]; //章ごとのクエスト数カウント
    $quest_page_index=[]; //章の表紙が何Pにくるか
    
    //イベント演出のあるクエストデータ取得
    $_SESSION["evnQuestIds"]=GetEventQuestList();

    $i=0;
    foreach($questArray as $quest){
        $user_quest_data = $sql_list->query("SELECT * FROM {$userquestdata} WHERE UserID='{$_SESSION["userid"]}' AND QuestID={$quest["ID"]}");
        $user_quest_data = $user_quest_data->fetch();

        if($user_quest_data){//解放済みクエストであればクエストページ生成
            array_push($quest_data_list, $quest);

            //章タイトルページ生成
            if($quest_chapter_id !== $quest["chapter"]){
                $quest_chapter_id = $quest["chapter"];

                if($quest_chapter_id >= 0 ){
                    if($quest_chapter_id === 0){
                        $quest_page_index[$quest_chapter_id] = 2;//+$quest_chapter_id;
                    }else{
                        $quest_page_index[$quest_chapter_id] = 2+$quest_page_index[$quest_chapter_id-1]+$chapter_quest_cnt[$quest_chapter_id-1]*2;//+$quest_chapter_id;
                    }

                    $quest_title_page = "<p>第".($quest_chapter_id+1)."章</p>";
                    array_push($quest_pages,$quest_title_page);
                    if($quest_chapter_id > 0){
                        $quest_title_page = "<p>あらすじ</p>";
                        array_push($quest_pages,$quest_title_page);
                    }
                    $chapter_quest_cnt[$quest_chapter_id]=0;
                }
            }

            $specialPartyMes="";
            $memberNmList="<br>";
            $_SESSION["eventQuestParam"]=isEventQuest($_SESSION["evnQuestIds"],$quest["ID"]);
            //演出のあるクエストか
            if($_SESSION["eventQuestParam"]["evnQuest"]){
                foreach($_SESSION["eventQuestParam"]["sParty"] as $spmember){
                    if($spmember != -1){
                        $specialPartyMes="<br>特殊な編成で挑むクエストです";
                        $memberNmList .= $spmember[2]."<br>";
                    }
                }
            }

            $clear_mes = ($user_quest_data["ClearFlag"])? "クリア済み":"未クリア";
            array_push($quest_panel_list, $quest);//パネルに対するクエストデータ格納
            //$questId = $quest["ID"]; //パネルIDとクエストIDの紐づけ
            $start_btn="<button><div class=quest-start-img><a href=/game/battle onclick=LocateBattleScene(".$quest["ID"].")>クエスト開始</a></button><br>";

            $quest_info_text="<p>【".$quest["title"]."】</p><br><p id=clear_flag>:{$clear_mes}</p>{$specialPartyMes}{$memberNmList}".$start_btn;
            $quest_story_text="<p id=story_title>【Story】<br>".$quest["story"]."</p>";
            

            array_push($quest_pages, $quest_info_text);
            array_push($quest_pages, $quest_story_text);

            if($quest_chapter_id >= 0 )
                $chapter_quest_cnt[$quest_chapter_id]++;
        }

        $i++;
    }

    //クエストデータ保持
    $_SESSION["QUESTID_LIST"]=$quest_data_list;

    //レスポンスデータ生成
    return [$quest_pages, $chapter_quest_cnt,$quest_data_list,$quest_page_index];
}


//イベントクエスト情報更新
function UpdateEventParam($id){
    $_SESSION["eventQuestParam"]=isEventQuest($_SESSION["evnQuestIds"],$id);
}

//イベント用クエストID一覧生成
function GetEventQuestList(){
    $evnIds=array();
    //CSV取得
    $record_data = file(ROOT_DIR."\datas\csv\EventQuestData.csv");//csvファイル読み込み
    $i=0;
    foreach($record_data as $line){//データ探索
        $data = explode(',',$line);
        if($i > 0){//最初の行は飛ばす
            array_push($evnIds, $data);
        }
        $i++;
    }
    return $evnIds;
}

//イベントクエストかどうか検査
function isEventQuest($evnQuestData,$id){
    $evnQuestParam=array(
        "evnQuest"=>false,
        "sLeaderSkill"=>-1,
        "sParty"=>array(-1,-1,-1,-1)
    );

    foreach($evnQuestData as $qd){
        if((int)$qd[0] === (int)$id){
            $evnQuestParam["evnQuest"]=true;
            //特殊編成
            $evnQuestParam["sParty"]=isEventParty($qd);
            //特殊リーダースキル
            $evnQuestParam["sLeaderSkill"]=$qd[1];
            break;
        }
    }
    return $evnQuestParam;
}

//特殊編成ID一覧生成
function isEventParty($qd){
    require_once(ROOT_DIR."\php\getcharacterlist.php");
    $filepath=ROOT_DIR."\datas\csv\CharactersStetas.csv";
    $specialCharaIds=array();

    $index=0;
    for($i=2; $i<6; $i++){
        array_push($specialCharaIds,(int)$qd[$i]);

        if((int)$qd[$i] > 0){    //指定のキャラIDがある
            //キャラ情報取得
            $cData=getRecord($qd[$i], $filepath);
            $specialCharaIds[$index]=$cData; //キャラ情報に更新
        }
        $index++;
    }
        return $specialCharaIds;
}

function ResponsData($data){
    //レスポンスデータ整頓
    header('Content-Type: application/json; charset=utf-8');
    $resjson =json_encode( $data,JSON_PRETTY_PRINT );
    echo  $resjson;
    exit();//処理終了
}
?>