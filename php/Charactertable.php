<?php
ini_set('display_errors',"On");
error_reporting(E_ALL);

define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."\serverside");

try{
require_once( ROOT_DIR."\php\getcharacterlist.php");
require(ROOT_DIR."\datas\sql.php");

$sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
$sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
$sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$csvpath = ROOT_DIR."\datas\csv\CharactersStetas.csv";
$i=0;
$charadatas = array(getRecord(15,$csvpath),getRecord(18,$csvpath),getRecord(20,$csvpath),getRecord(25,$csvpath),getRecord(26,$csvpath)); //IDでキャラデータ取得

echo '<table border=1>';
echo "<tr><th>No</th><th>名称</th><th>出身</th><th>性別</th><th>年齢</th><th>身長</th><th>体重</th><th>趣味</th><th>好きなもの</th><th>嫌いなもの</th><th>種族</th><th>概要</th><th>タイプ</th><th>基礎ジョブ</th><th>上位ジョブ</th><th>最終ジョブ</th><th>基礎HP</th><th>基礎攻撃力</th><th>基礎防御力</th><tr>";   
//キャラデータテーブル追加していく
foreach($charadatas as $charadata){
    echo '<p><tr>';

    for($n=0;$n<10;$n++){
        echo '<td>'.$charadata[$n].'</td>';
    }
   
    $race=$sql_list->query("SELECT RaceName FROM $racelist WHERE RaceID=$charadata[10]");
    $race=$race->fetch();
    echo '<td>'.$race[0].'</td>';

    echo '<td>'.$charadata[11].'</td>';

    $type=$sql_list->query("SELECT TypeName FROM $typelist WHERE TypeID=$charadata[12]");
    $type=$type->fetch();
    echo '<td>'.$type[0].'</td>';
    $job=$sql_list->query("SELECT jobName FROM $joblist WHERE jobID=$charadata[13]");
    $job=$job->fetch();
    echo '<td>'.$job[0].'</td>';
    $job=$sql_list->query("SELECT jobName FROM $joblist WHERE jobID=$charadata[14]");
    $job=$job->fetch();
    echo '<td>'.$job[0].'</td>';
    $job=$sql_list->query("SELECT jobName FROM $joblist WHERE jobID=$charadata[15]");
    $job=$job->fetch();
    echo '<td>'.$job[0].'</td>';

    echo '<td>'.$charadata[16].'</td>';
    echo '<td>'.$charadata[17].'</td>';
    echo '<td>'.$charadata[18].'</td>';

    $rare=$sql_list->query("SELECT RareName FROM $rarelist WHERE RareID=$charadata[19]");
    $rare=$rare->fetch();
    echo '<td>'.$rare[0].'</td>';
    echo '</tr></p>';

}
echo '</table>';

}catch(PDOExeption $erro){
    echo "次のエラーが発生しました<br>";
    echo $erro->getmessage();
}
?>

<html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>キャラステータス</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body>
</body>
</html>