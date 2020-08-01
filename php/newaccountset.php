<?php
  require 'password.php';
  session_start();

  ini_set('display_errors',"On");
  error_reporting(E_ALL);

  define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."\serverside");
  require_once(ROOT_DIR."\datas\sql.php");

  $login_succes=false;
  $log_mes="アカウント登録";
  $errorMes="";

  /**
  * userID: ユーザーID
  * userpass: ユーザーパスワード
  * usernum: ユーザーナンバー
  * username: ユーザー名
  */
  $userdata=array(
    'userID'=>0,
    'userpass'=>"",
    'usernum'=>"",
    'username'=>""
  );

 if(isset($_POST["Create"])){
    if(empty($_POST["userid"])){
      $errorMes="<br>ユーザーIDを入力してください";
    }
    if(empty($_POST["userpass"])){
      $errorMes="<br>パスワードを入力してください";
    }
    if(empty($_POST["username"])){
      $errorMes="<br>ユーザー名を入力してください";
    }

   if(!empty($_POST['userid']) && !empty($_POST['userpass']) && !empty($_POST['username'])){
      $id = $_POST['userid'];
      $pass =$_POST['userpass'];
      $name = $_POST['username'];
      $sexial = $_POST['sexial'];
      $race = $_POST['race'];

      try{
          //SQL接続-----------------------------------------------------------------
          $quest_data_file= ROOT_DIR."\datas\gameMasterData\questDataList.json";//クエストデータファイルパス

          $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
          $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
          $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
          //----------------------------------------------------------------------
          $s = "SELECT * FROM useraccountdata";
          $row=$sql_list->query($s);
          
          #ユーザIDの重複チェック
          while($kekka=$row->fetch()){
            if($kekka['userID']==$id){
              $log_mes = "すでにこのIDは使用されています";
              echo $kekka['userID'];
              $login_succes=false;
              break;
            }else{
              $login_succes=true;
            }
          }

          #ユーザ登録処理
          if($login_succes == true){
              $quest_list=array();
              $quest_list=file_get_contents($quest_data_file);
              $quest_list=mb_convert_encoding($quest_list, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
              $quest_list=json_decode($quest_list,true);

              $raceId = (int)($race+$sexial);
              $userdata['userID'] = $id;
              $userdata['userpass'] = $pass;
              $userdata['username']= $name;
              $userdata['userrace']=$raceId;

              //アカウント追加処理
              $add = $sql_list->prepare("CALL adduser(?,?,?, ?)");

              $sql_list->beginTransaction();
              try{
                $add->bindParam(1,$id,PDO::PARAM_STR);
                $add->bindParam(2,$pass,PDO::PARAM_STR);
                $add->bindParam(3,$name,PDO::PARAM_STR);
                $add->bindParam(4,$raceId,PDO::PARAM_INT);

                $add->execute();
                $sql_list->commit();
              }catch(PDOExeption $e){
                $sql_list->rollback();
                throw $e;
              }

              /*foreach($quest_list as $questId){
                $questlist = $sql_list->query("INSERT INTO {$userquestdata} VALUES ('{$id}','{$questId["ID"]}',0,NULL)");//クエストクリアフラグ追加
              }*/
              $log_mes = "新しく登録されました<br><a href='/game/login'>ログインに戻る</a>";
              $login_succes=true;
          }else{
            echo "<br>すでにこのIDは使用されています";
          }
         }
        catch(PDOExeption $erro){
            echo "次のエラーが発生しました<br>";
            echo $erro->getmessage();
        }
      }
  }

?>

<html>
<head>
    <meta charset="utf-8">
    <title>新規登録</title>
</head>
<body>
  新規登録画面:
    <form action="" method="post">
       ID:<input name="userid" type="text" size="5">
       PASS:<input name="userpass" type="text" size="12">
       ユーザー名:<input name="username" type="text" size="8">※ユーザー名は登録後も変更できます
       主人公の性別:
       <input type="radio" name="sexial" value=0 checked> 男
       <input type="radio" name="sexial" value=1> 女
       主人公の種族:
       <select class="user-race" name="race">
        <option class="characterlist" value=1>ヒューマン</option>
        <option class="characterlist" value=3>ビースト</option>
        <option class="characterlist" value=5>コボルト</option>
        <option class="characterlist" value=7>エルフ</option>
        <option class="characterlist" value=9>オーガ</option>
       </select>
      <input type="submit" name="Create" value="登録">
      <input type="hidden" name="login" value="newlogin">
    </form>
    <?php
      echo $log_mes;
      echo $errorMes;
    ?>
</body>
</html>
