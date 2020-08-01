<?php
//ページ読み込み時,最初に必ず呼ばれる
error_reporting(E_ALL);
ini_set('error_log', '/tmp/php.log');
ini_set('log_errors', true);
ini_set('display_errors',"On");

define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."\serverside");

session_start();
include ROOT_DIR."\php\battlePHP\BattleSessionInit.php";
include ROOT_DIR."\php\battlePHP\battleStetasSetUp.php";
require_once( ROOT_DIR."\php\battlePHP\questDataTest.php");//クエストデータのセットアップ


if(IllegalPageMove($_SESSION["userid"])){
  header('Content-Type: application/json; charset=utf-8');  
  echo json_encode(array("messege"=>"err"), JSON_PRETTY_PRINT);
  exit();
}else{

  $eventQuestData = EventQuest($_SESSION["QUEST_ID"]);

  require_once(ROOT_DIR."\php\UpdateUserStageId.php");
  UpdateStageId(4,$_SESSION["userid"]);
  InitBattleSessionData();

  //クエストデータセット
  $_SESSION["questData"] = setQuestDate($_SESSION["QUEST_ID"]);
  $_SESSION["AP_UPDATE"]=false;

  //初期化
  $_SESSION["gameStatus"]="AtPlayGame";
  $_SESSION["judgedatas"]=array("MISS"=>0,"GOOD"=>0,"GREAT"=>0,"PARF"=>0);
  $_SESSION["notesdata"]=array();
  $_SESSION["Score"]=0;
  $_SESSION["Comb"]=0;
  $_SESSION["maxComb"]=0;

    try{

      $questSt = $_SESSION["questData"][0];
      $musicSt = $_SESSION["questData"][1];
      $_SESSION["illegalNotes"]=$illegalNotesIds=[];
      
      $notesUrl =$musicSt["notesFilePath"];
      $audiotext = [$musicSt["audioFilePath"],$musicSt["musictitle"]];//audioファイルのパスと曲名
      $titletext= $questSt["title"];//クエスト名
      
      if(file_exists($notesUrl)){
        //ノーツデータリストの生成
        $notesjson = file_get_contents($notesUrl);
        $notesjson = mb_convert_encoding($notesjson, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

        //イベント用特殊判定ノーツリストの生成
        $illegalNotesIds = CreateIllegalNotesList($eventQuestData,$notesjson);

        $_SESSION["illegalNotes"]=$illegalNotesIds;
      }else{
        $notesjson="ファイルがありません";
      }

      //ステータスデータ設定 
      SetInitStetas();
      SetLeaderSkill($_SESSION["eventQuestParam"]);

      $resdata = array(
        "ellegalNotes"=>$illegalNotesIds,
        "event"=>$eventQuestData, //イベント用データ
        "messege"=>"",
        "notesdata"=>$notesjson,//ノーツファイルのurl
        "audiohtml"=>$audiotext ,//audioタグの情報
        "title"=>$titletext,//タイトル
        "enemySt"=>$_SESSION["enemySt"],//前衛敵ステータス
        "partySt"=>$_SESSION["partySt"],//パーティステータス
        "leaderSkill"=>[$_SESSION["leaderSkill"]->skillname, $_SESSION["leaderSkill"]->discription]
      );

      header('Content-Type: application/json; charset=utf-8');
      $resjson = json_encode($resdata, JSON_PRETTY_PRINT);
            
      echo $resjson;

    }catch(PDOExeption $erro){
      echo "次のエラーが発生しました<br>";
      echo $erro->getmessage();
  }
}

//イベント演出のあるクエストIDか検査
function EventQuest($qId){
  foreach ($_SESSION["evnQuestIds"] as $id) {
    if((int)$qId === (int)$id[0]){
      //イベント用jsonデータ取得
      $eventJson = file_get_contents(ROOT_DIR."\datas\scinarios\\eventTimeline-".$id[0].".json");
      $jsondata=mb_convert_encoding($eventJson, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');//文字化け防止
      $jsondata=json_decode($jsondata,true);

      return $jsondata;
    }
  }
  return false;
}

function SetLeaderSkill($sId){
  unset($_SESSION["leaderSkill"]);
  if((int)$sId["sLeaderSkill"] < 0){
    $_SESSION["leaderSkill"]=$_SESSION["partySt"][0]->LeaderSkillInit();
  }else{
    //リーダースキルに特殊仕様があれば設定
    $_SESSION["leaderSkill"]=setLeaderSkillData($sId["sLeaderSkill"],0);
  }
}

function CreateIllegalNotesList($qDt,$nDt){

  if(!$qDt) return array();

  $nDt = json_decode($nDt, true);
  $list=array();
  for( $i=0; $i<count($qDt); $i++) {
    if($qDt[$i]["illegaldata"] != []){
      array_push($list, [$qDt[$i]["illegaldata"][0],$qDt[$i]["timing"], $qDt[$i+1]["timing"]]); //特殊判定と適用開始時間,有効時間一覧設定
    }
  }

  //特殊判定対象になるノーツIDを設定
  $illegalNotesData=array();
  for( $i=0; $i<count($nDt); $i++) {
    foreach ($list as $ld) {
      if( $ld[2] > $nDt[$i]["timing"] && $nDt[$i]["timing"] > $ld[1]){ //適用開始時間より後で、かつ適用時間内であるノーツ
        array_push($illegalNotesData,[$ld[0],$i]); //判定とノーツID格納
      }
    }
  }

  return $illegalNotesData;
}

//「戻るボタン」などからページに移動してきたら強制的にクエストページに戻す
function IllegalPageMove($uId){
  //SQL接続-----------------------------------------------------------------
  require(ROOT_DIR."\datas\sql.php");
  $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
  $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
  $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  //----------------------------------------------------------------------
  $userStage=$sql_list->query("SELECT * FROM {$userdatas} WHERE UserID='{$uId}'");
  $userStage=$userStage->fetch();
  return ($userStage["playerstageID"] != 3 && $userStage["playerstageID"] != 4);
}
?>