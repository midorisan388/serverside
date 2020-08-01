<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  
 <link rel="stylesheet" href="./css/MenuVar.css" type="text/css" />
 <link rel="stylesheet" href="./css/gameFormatStyle.css" type="text/css" />

 <link rel="shortcut icon" href="img/menu/loginicon.jpg">
 <script type="text/jacascript" src="./js/TouchController.js"></script>
 <script type="text/javascript" src="js/prag/jquery-3.3.1.js"></script>
 <script type="text/javascript" src="js/prag/anim.min.js"></script>

 <style>
    main{
      margin:10px; padding: 0; border: 0;
      width: 100%; height:100%;
    }
 </style>
 <title>GAME</title>
<?=
  $html_txt='';
  if(!empty($_GET['page-link'])){
    $pageid = $_GET['page-link'];
    $html_txt = file_get_contents($pageid.'pg.html');

$style_txt = <<< EOF
      <link rel=stylesheet href= css/${pageid}style.css type=text/css />
EOF;
    echo $style_txt;
  }else{
      echo "ぺーじを選択してください<br>";
  }
?>
</head>

<!--ホーム画面-->
 <body style="background-color:black;">
 <audio id="homebgm" type="audio/ogg" src="./audio/bgm/homebgm.ogg" preload="auto" autoplay loop>
    <p>音声を再生するには、audioタグをサポートしたブラウザが必要です。</p>
 </audio>

 <main class="main-frame" id="main-frame"><?php echo $html_txt; ?></main>
 <div class="loading"><strong><?= $pageid ?></strong></div>

 <script type="text/javascript" src="js/GameFormatInsert.js"></script>
 <script type="text/javascript" src="js/MenuVarInsert.js"></script>
 <script>
    window.onload=LoadingComp;
  </script>
 </body>
</html>
