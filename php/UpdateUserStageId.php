<?php
error_reporting(E_ALL);
ini_set('error_log', '/tmp/php.log');
ini_set('log_errors', true);
ini_set('display_errors',"On");

//userStageIDの更新
/** 
 * 0:マイページ 
 * 1:パーティ編成
 * 2:ユーザ情報
 * 3:クエスト選択
 * 4:バトルシーン
 * 5:リザルト画面
 * 6:その他（ログアウト画面）
*/
function UpdateStageId($id,$uId){
    //SQL接続-----------------------------------------------------------------
    require( ROOT_DIR."\datas\sql.php");
    $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
    $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
    $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    //----------------------------------------------------------------------
    $sql_list->query("UPDATE {$userdatas} SET playerstageID = {$id} WHERE UserID='{$uId}'");
}

?>