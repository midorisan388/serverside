<?php
  require 'password.php';
  session_start();

    ini_set('display_errors',"On");
    error_reporting(E_ALL);

    define( "ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."/serverside");

    require_once(ROOT_DIR."/php/getcharacterlist.php");//キャラクターレコードを取得する処理ファイル
    require_once(ROOT_DIR."/datas/sql.php");

    $csvpath= ROOT_DIR."/datas/csv/CharactersStetas.csv";
    // $id_cookie= $_COOKIE["userid_cookie"];
    $id_cookie = $_SESSION['userid'];
    //$num_cookie=$_COOKIE["usernum_cookie"];
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
      'usernum'=>0,
      'userAP'=>100
    );
    try{
      require_once(ROOT_DIR."/php/UpdateUserStageId.php");
      UpdateStageId(0,$_SESSION["userid"]);

    //ユーザデータ取得
        //SQL接続-----------------------------------------------------------------
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

        if(!isset($_SESSION["USER_DATA"])){
          $userdata['userAP']=100;
        }else{
          $userdata['userAP']=$_SESSION["USER_DATA"]["userAP"];
        }

        $_SESSION["USER_DATA"] = $userdata;//ユーザー情報を保持
        $_SESSION["PARTY_IDS"] = array(
          "1st"=> $kekka['1st'],
          "2nd"=> $kekka['2nd'],
          "3rd"=> $kekka['3rd'],
          "4th"=> $kekka['4th']
        );
      

      //PT情報取得  
      $partymember_id=(int)$_SESSION["PARTY_IDS"]['1st'];//マイページののキャラクターID取得

      $character_data = getRecord($partymember_id,$csvpath);//キャラクターデータ取得

      $character_image_dir = $character_data[1];//通し番号立ち絵

      $userdata = array(
        "userdata"=>$_SESSION["USER_DATA"],
        "charaurl"=>$character_image_dir
      );

      $resdata = json_encode($userdata,JSON_PRETTY_PRINT);

      echo $resdata;

    }catch(PDOExeption $erro){
      echo "次のエラーが発生しました<br>";
      echo $erro->getmessage();
    }
?>
