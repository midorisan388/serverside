<?php
//パーティメンバーを編成する
ini_set("display_errors", 1);
error_reporting(E_ALL);

define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."\serverside");

require_once( ROOT_DIR."\php\getcharacterlist.php");//キャラクターデータ取得メゾット
require(ROOT_DIR."\datas\sql.php");//ユーザー情報テーブル

$list="";//表示する選択キャラクターリスト
$message="";//操作メッセージ
$drop_down_list="";//ドロップダウンリスト
$jobdata=array();
$racedata=array();
$battledata=array();
$resdata=array();
$party_member_ids=array();

session_start();
$party_stetas=isset($_SESSION["PARTY_STETAS"])? $_SESSION["PARTY_STETAS"]:array();//パーティメンバーのステータス
$selectdata = (isset($_SESSION["SELECT_CHARACTER_STETAS"]))?$_SESSION["SELECT_CHARACTER_STETAS"]: 0;//選択中キャラのステータスを保持
$panelIddata = [];//パネルに対応するキャラクターIDを格納
$userid = $_SESSION["userid"];//ユーザー保持

//マスターデータファイル
$characterfilepath= ROOT_DIR."\datas\csv\CharactersStetas.csv";

function setStetas($characterId, $cLv){

    if($characterId != null){
        //マスターデータファイル
        global $characterfilepath;
        $skillfilepath="../datas\csv\SkillList.csv";
        $lSkillfilepath="../datas\csv\LeaderSkillList..csv";
        $battletype_path="../datas\gameMasterData\battleTypeDataList.json";
        $job_path="../datas\gameMasterData\jobDataList.json";
        $race_path="../datas\gameMasterData/raceDataList.json";
        $weponType_path="../datas\gameMasterData\weponTypeDataList.json";

        $character_record = getRecord((int)$characterId, $characterfilepath);//キャラクターデータ取得
        $skill_record = getRecord((int)$character_record[21], $skillfilepath);//スキルデータ取得
        $leader_skill_record = getRecord((int)$character_record[22], $lSkillfilepath);//リーダースキルデータ取得
        $racedata=getJsonData($character_record[11], $race_path);//種族データ取得
        $jobdata = getJsonData($character_record[14], $job_path);//ジョブデータ取得
        $battledata = getJsonData($character_record[13], $battletype_path);//戦闘タイプデータ取得

        $wepondata =array();//武器種データ
        foreach( $jobdata["equipWeponId"] as $weoponid){//武器種リスト作成
            if(isset($weoponid)){
                $wepon_setdata = getJsonData($weoponid, $weponType_path);//武器種データ取得
                array_push($wepondata, $wepon_setdata);
            }
        }

        //表示用ステータス生成
        $returnstetas = array(
            "character_id"=>$character_record[0],
            "character_name"=>$character_record[2],
            "character_lv"=>$cLv,
            "character_img_id"=>$character_record[1],
            "character_job"=>$jobdata["jobName"],
            "character_sei"=>$character_record[4],
            "character_race"=>$racedata["raceName"],
            "character_battleType"=>$battledata["typeName"],
            "character_wepon"=>$wepondata,
            "character_skilldata"=>array(
                "skill_name"=>$skill_record[1],
                "skill_ct"=>$skill_record[4],
                "skill_discription"=>$skill_record[3]
            ),
            "character_leader_skilldata"=>array(
                "skill_name"=>$leader_skill_record[1],
                "skill_discription"=>$leader_skill_record[3]
            ),
            "stetas"=>array(
                "hp"=>$character_record[15]+$cLv*50,
                "pow"=>$character_record[16]+$cLv*50,
                "def"=>$character_record[17]+$cLv*50,
                "magic"=>$character_record[18]+$cLv*50,
                "mental"=>$character_record[19]+$cLv*50
            )
        );
    }else{
        //0なら空データを返す
        $returnstetas = array(
            "character_id"=>0,
            "character_name"=>"",
            "character_img_id"=>"",
            "character_lv"=>0,
            "character_job"=>"",
            "character_sei"=>"",
            "character_race"=>"",
            "character_battleType"=>"",
            "character_wepon"=>null,
            "character_skilldata"=>null,
            "stetas"=>null
        );
    }
    return $returnstetas;
}

function setpartyIds($userid_){//MySQL間のデータ更新と取得

    global $SERV,$GAME_DBNAME,$USER,$PASSWORD,$userpartytable;
    global $party_member_ids;
    global $party_stetas;
    //SQL接続-----------------------------------------------------------------
    $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
    $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
    $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    //----------------------------------------------------------------------
    if(!isset($_SESSION["PARTY_IDS"])){
        $party_record=$sql_list->query("SELECT * FROM {$userpartytable} WHERE UserID='{$userid_}'");//パーティ情報レコード取得
        $party_record=$party_record->fetch();
    }else{
        $party_record=$_SESSION["PARTY_IDS"];
    }
    
        //メンバーキャラクターID格納
        $party_member_ids=array(
            "1st"=> $party_record["1st"],
            "2nd"=> $party_record["2nd"],
            "3rd"=> $party_record["3rd"],
            "4th"=> $party_record["4th"]
        );

        $_SESSION["PARTY_IDS"]=$party_member_ids;//IDリストも保持
   
    $party_stetas=array();
    foreach($party_member_ids as $member){
       
        //キャラレコード取得,格納
        array_push($party_stetas,  setStetas($member, getCharacterLv($member)));
    }
    //次回以降表示するパーティステータスを保持
    $_SESSION["PARTY_STETAS"]=$party_stetas;
}

function panelListGenerage(){

    global $SERV,$GAME_DBNAME,$USER,$PASSWORD,$userdatas,$userplayable;
    global $party_stetas;
    global $drop_down_list;
    global $panelIddata;
    global $selectdata;
    global $characterfilepath;

      //選択できるキャラクターリストを作成
      $i=0;//最初の行は飛ばす
      $panelcount=0;
      $drop_down_list="";
      $character_record_list = file($characterfilepath);

      //加入済みキャラクターID取得
      //SQL接続-----------------------------------------------------------------
      $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
      $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
      $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      //----------------------------------------------------------------------
    $playerUnitId=$sql_list->query("SELECT * FROM {$userdatas} WHERE UserID='{$_SESSION['userid']}'");
    $playerUnitId=$playerUnitId->fetch()["playerUnitID"];

    //加入済みキャラのIDリスト生成
    $userPlayableList=array();
    $userPlayableData = $sql_list->query("SELECT CharaID FROM {$userplayable} WHERE UserID='{$_SESSION['userid']}'");
    while($unitId = $userPlayableData->fetch()["CharaID"]){
        array_push($userPlayableList,$unitId);
    }

    //表示リスト生成
    foreach($character_record_list as $character_list){
        $setflag = 1;//編成済みフラグ
        if($i === 0){
            $drop_down_list .="<option class=selet_character_panel id=panel_{$panelcount} value={$panelcount} data-cId={$i}>キャラクターを選んでください</option>";
        }else{
            $data = explode(",", $character_list);
            foreach ($userPlayableList as $playable) {
                if((int)$playable === (int)$data[0]){ //加入キャラID
                    if(CheckinParty($i,$party_stetas,$selectdata["character_id"])){
                        $setflag=1;
                        break;
                    }else{
                        $setflag=0;
                        break;
                    }
                }
            }
            if($setflag === 0){
                $drop_down_list .="<option class=selet_character_panel id=panel_{$panelcount} value={$panelcount} data-cId={$i}>".$data[2]."</option>";
                array_push( $panelIddata, $i);//パネルIDとキャラクターIDを関連つけておく
                $panelcount++;
            }
        }
        $i++;
    }  
        //$drop_down_list .="<option class=selet_character_panel id=panel_{$panelcount} value={$panelcount} data-cId={$i}>編成しない</option>";
        //array_push( $panelIddata, 0);//編成無しもID0で格納
}

//キャラレベル取得
function getCharacterLv($cId){
    global $SERV,$GAME_DBNAME,$USER,$PASSWORD,$userdatas,$userplayable;
    
     //SQL接続-----------------------------------------------------------------
     $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
     $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
     $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
     //----------------------------------------------------------------------

     if($cId != null && $cId > 0){
        $sql = "SELECT CharaLv FROM userpartycharacters WHERE USERID = ? AND CharaID=?";
        $stmt = $sql_list->prepare($sql);
        $stmt->execute(array($_SESSION["userid"], $cId));
        $cLv = $stmt->fetch(PDO::FETCH_ASSOC);
        return $cLv["CharaLv"];
     }
     return 0;
}

//パーティ編成済みか
function CheckinParty($id,$partySt,$sId){
    foreach($partySt as $pId){
        if($id === (int)$pId["character_id"] || $id === $sId) {
           return true;
        }
    }
    return false;
}

function setNewPartyData($userid_, $select_party_){
    global $party_stetas,$selectdata,$party_stetas, $message;
    global $SERV,$GAME_DBNAME,$USER,$PASSWORD,$userpartytable;

    if($selectdata["character_id"] === null) $selectdata["character_id"]=0;
    $nullparty=0;//誰も編成されていないフラグ
    foreach( $party_stetas as $party){
        if($party["character_id"] === null)$party["character_id"] = 0;
        if( $party["character_id"] !== 0 ){
            $nullparty++;
        }
    }
    if( $nullparty <= 1 && $selectdata["character_id"] === 0){
        $message= "パーティを無人にはできません";
    }else{
        $party_insert_id =  $select_party_;////編成先のID
        $party_insert_character = $_SESSION["SELECT_CHARACTER_STETAS"];//編成予定のキャラクターステータス
        $insert_character_id = $selectdata["character_id"];
        //MySQLのレコードも更新
        //SQL接続-----------------------------------------------------------------
        $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
        $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
        $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //----------------------------------------------------------------------
        $party_table_columns =array('1st','2nd','3rd','4th');
        $update_query="UPDATE {$userpartytable} SET {$party_table_columns[$party_insert_id]}={$insert_character_id} WHERE UserID='{$userid_}'";
        $sql_list->query($update_query);  
        $update_party_ids = $sql_list->query("SELECT * FROM {$userpartytable} WHERE UserID='{$userid_}'");
        $update_party_ids = $update_party_ids->fetch();
        //セッションデータ更新
        $newparty_member_ids=array(
            "1st"=> $update_party_ids[1],
            "2nd"=> $update_party_ids[2],
            "3rd"=> $update_party_ids[3],
            "4th"=> $update_party_ids[4]
        );

        $_SESSION["PARTY_IDS"]=$newparty_member_ids;//IDリストも保持
   
        setpartyIds($userid_);//DB更新　パーティ情報更新
        panelListGenerage();//パネルリスト更新

        $party_stetas[$party_insert_id]=$party_insert_character;
        $_SESSION["PARTY_STETAS"]=$party_stetas;//データを編成予定だったキャラステータスで更新

        $message= $party_insert_character["character_name"]."を編成しました";
    }
}

try{
    require_once(ROOT_DIR."\php\UpdateUserStageId.php");
    UpdateStageId(1,$_SESSION["userid"]);

    if(isset($_SESSION["PARTY_STETAS"])){
        //パーティ情報取得済み
        panelListGenerage();//パネルリスト生成
    }
    //初回はDBからキャラクターステータス生成
    if(!isset($_SESSION["PARTY_STETAS"])){
        setpartyIds($userid);
        unset($_SESSION["SELECT_CHARACTER_STETAS"]);
        panelListGenerage();//パネルリスト生成

        $selectdata=0;
        $message="編成するキャラクターをリストから選んでください(初回)";

    }else if(isset($_POST["select_cId"])){//キャラクター確認ボタンが押された
        $select_panelId=(int)$_POST["select_cId"];//何番目のドロップダウン要素か格納
        $select_characterId = $panelIddata[$select_panelId];//ドロップダウンリストに格納されたキャラクターID格納

        $nullparty=1;//誰も編成されていないフラグ
        foreach( $party_stetas as $party){
            if( $party["character_id"] !== null){
                $nullparty = 0;
                break;
            }
        }
        if( $nullparty === 0 || $select_characterId){
            //編成するキャラクター情報取得
            $selectdata = setStetas($select_characterId,getCharacterLv($select_characterId));//表示するキャラレコード更新
            $message= $selectdata["character_name"]."を編成する位置の「編成する」ボタンを選んでください";
            $_SESSION["SELECT_CHARACTER_STETAS"]=$selectdata;//選択中のキャラクターステータス保持
        }else{
            $message= "パーティを無人にはできません";
        }
        
    }else if(isset($_POST["select_partyId"]) && $selectdata !== null ){//編成するボタンが押された
        setNewPartyData($userid, (int)$_POST["select_partyId"]);
    }else{
        //更新時
        setpartyIds($userid);
        $party_stetas= $_SESSION["PARTY_STETAS"];
        unset($_SESSION["SELECT_CHARACTER_STETAS"]);
        $selectdata=0;
        $message="編成するキャラクターをリストから選んでください";
        panelListGenerage();//パネルリスト生成
    }

    $resdata=array(
        "panelList"=> $_SESSION["PARTY_IDS"],
        "partystetas"=> $party_stetas,//パーティメンバー情報
        "selectharacter"=> $selectdata,//選択中のキャラクターステータス
        "message"=>$message,//表示メッセージ
        "listpanel"=>$drop_down_list//選択キャラクターリスト
    );

    //レスポンスデータ整頓
    header('Content-Type: application/json; charset=utf-8');
    $resdata =json_encode( $resdata,JSON_PRETTY_PRINT );
    echo($resdata);

}catch(PDOExeption  $erro){
        echo "次のエラーが発生しました<br>";
        echo $erro->getmessage();
}
?>