<?php

require 'php/password.php';
  session_start();

  ini_set('display_errors',"On");
  error_reporting(E_ALL);

  define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."/serverside");

  require_once(ROOT_DIR."/php/getcharacterlist.php");//キャラクターレコードを取得する処理ファイル

  $id_cookie = $_SESSION['userid'];
  define("CHARACTER_IMG_DIR","./img/characters");
  define("CHARACTER_DATA_DIR","./datas/csv/CharactersStetas.csv");

  $APhealtime = 10;//分

  $userdata=array(
    'userID'=>$id_cookie,
    'username'=>"",
    'userrank'=>0,
    'userunitID'=>0,
    'userTitleID'=>0,
    'userStage'=>0,
    'userAP'=>0,
    'userMaxAP'=>0,
    'userexp'=>0,
    'usernextexp'=>0,
    'userstartdate'=>0,
    'userlastdate'=>0,
    'userAPupdatedate'=>0,
  );

  try{
    require_once(ROOT_DIR."/php/UpdateUserStageId.php");
    UpdateStageId(2,$_SESSION["userid"]);

    //SQL接続-----------------------------------------------------------------
    require("./datas/sql.php");
    $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
    $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
    $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    //----------------------------------------------------------------------

     //ユーザー情報取得
     $userinfo=$sql_list->query("CALL getuseraccountdata('$id_cookie')");
     $kekka=$userinfo->fetch();

        $userdata['userID']=$kekka['userID'];
        $userdata['username']=$kekka['username'];
        $userdata['userexp']=$kekka['userhaveExp'];
        $userdata['userAP']=$kekka['playerAP'];
        $userdata['userMaxAP']=$kekka['playerMaxAP'];
        $userdata['usernextexp']=$kekka['usernextExp'];
        $userdata['userrank']=$kekka['userRank'];
        $userdata['lastdata']=$kekka['userplay_last'];
        $userdata['sdata']=$kekka['userplay_start'];
             
        $_SESSION["USER_DATA"] = $userdata;//ユーザー情報を保持
        $_SESSION["PARTY_IDS"] = array(
            "1st"=> $kekka['1st'],
            "2nd"=> $kekka['2nd'],
            "3rd"=> $kekka['3rd'],
            "4th"=> $kekka['4th']
        );

   //PT情報取得  
   $partymember_id=(int)$_SESSION["PARTY_IDS"]['1st'];//一番目のキャラクターID取得
   
   $character_data = getRecord($partymember_id,CHARACTER_DATA_DIR);//キャラクターデータ取得

   $character_image_dir = CHARACTER_IMG_DIR."/{$character_data[1]}/{$character_data[1]}0201.png";//通し番号立ち絵
   
   
   $res_data=array(
     "userdt"=>$userdata,
     "char_img_url"=>$character_image_dir
   );
   header('Content-Type: application/json; charset=utf-8');
   $resjson = json_encode( $res_data,JSON_PRETTY_PRINT );
   echo($resjson);
  }catch(PDOExeption $erro){
    echo "次のエラーが発生しました<br>";
    echo $erro->getmessage();
  }
?>