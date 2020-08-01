<?php
  ini_set('display_errors',"On");
  error_reporting(E_ALL);
  echo "Cookieさくじょ ";
try{
  require_once(ROOT_DIR."/php/UpdateUserStageId.php");
  UpdateStageId(6,$_SESSION["userid"]);

  //ユーザー情報初期化
  setcookie("userid_cookie",'',time()-60*60*24*30);
  setcookie("userpass_cookie",'',time()-60*60*24*30);
  setcookie("usernum_cookie",'',time()-60*60*24*30);

  header( "Location: /game/login" );
  exit();
}catch(PDOExeption $erro){
  echo "次のエラーが発生しました<br>";
  echo $erro->getmessage();
}
?>