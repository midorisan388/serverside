<?php
//バトル終了後の処理
ini_set('display_errors',"On");
error_reporting(E_ALL);

define( "ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."/serverside");

require(ROOT_DIR."/datas/sql.php");

session_start();

// バトルで使用したセッションデータ削除

$clear_flag = $_GET["clearmode"];

//クリアフラグ更新
//SQL接続-----------------------------------------------------------------
$sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
$sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
$sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//----------------------------------------------------------------------
$questId =$_SESSION["QUEST_ID"] ;

//$user_clear_data = $sql_list->query("SELECT * FROM {$userquestclearflag} WHERE UserID = '{$_SESSION["userid"]}' AND QuestID='{$questId}'");
if($clear_flag === "clear"){
    //$sql_list->query("UPDATE {$userquestclearflag} SET ClearFlag=1 WHERE UserID = '{$_SESSION["userid"]}' AND QuestID='{$questId}'");//クリアにする
    //$sql_list->query("UPDATE {$userquestclearflag} SET ClearDate=Now() WHERE UserID = '{$_SESSION["userid"]}' AND QuestID='{$questId}'");//クリアにする
}
if(!$_SESSION["AP_UPDATE"]){
    $_SESSION["USER_DATA"]["userAP"] -= 10;
    if($_SESSION["USER_DATA"]["userAP"] <= 0){
        $_SESSION["USER_DATA"]["userAP"] = 100;
    }
    $_SESSION["AP_UPDATE"]=true;
}       

require_once(ROOT_DIR."/php/battlePHP/BattleSessionInit.php");

header( "Location: /game/page?page-link=quest" );
exit();

?>