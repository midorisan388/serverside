<?php
    ini_set('display_errors',"On");
    error_reporting(E_ALL);

    define( "ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."\serverside");
    require( ROOT_DIR."\datas\sql.php");

try{
    if(isset($_GET["questdataset"])){
        //登録
        if(empty($_GET["questid"]) || empty($_GET["questtitle"])){
            echo "データ空欄あるで";
        }
        if(isset($_GET["musicId"])){

            //バトルクエスト登録
            $set_questId = $_GET["questid"];
            $set_quest_title=$_GET["questtitle"];
            $set_musicId = $_GET["musicId"];

            //SQL接続-----------------------------------------------------------------
            $sql_list=new PDO("mysql:host=$SERV;dbname=$userDB",$USER,$PASSWORD);
            $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
            $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            //----------------------------------------------------------------------
            
            $users = $sql_list->query("SELECT UserID FROM {$usertable}");//ユーザーリスト取得
           // $users = $users->fetch();

            foreach($users as $userrecord){
                //新クエストデータレコードをユーザーIDだけ追加
                $sql_list->query("INSERT INTO {$tableDB}.{$user_quest_table} VALUES('{$userrecord[0]}', {$set_questId}, 0, NULL)");
                echo $userrecord[0]."<br>";
            }

            $new_quest_data=array(
                "ID"=>(int)$set_questId,
                "title"=>$set_quest_title,
                "musicData"=>intval($set_musicId)
            );

            //既存データ読み込み
            $current_jsondata=file_get_contents($quest_data_file);
            $current_jsondata=mb_convert_encoding($current_jsondata, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');//文字化け防止
            $current_jsondata=json_decode($current_jsondata,true);
            
            array_push($current_jsondata, $new_quest_data);//データ追加
            //更新書き込み
            $quest_json = fopen($quest_data_file, 'w');
            fwrite($quest_json, json_encode($current_jsondata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            fclose($quest_json);

           echo "登録完了";

        }else if(isset($_GET["textPath"])){
            //ストーリークエスト登録
            //SQL接続-----------------------------------------------------------------
            $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
            $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
            $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            //----------------------------------------------------------------------
        }
    }
}catch(PDOExeption $erro){
    echo "次のエラーが発生しました<br>";
    echo $erro->getmessage();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>クエストデータ登録</title>
</head>
<body>  
    <p>クエストタイプ:</p>
    <select id="questmode" name="questtype" onchange="SetQuestParam()">
        <option id="battle">バトル</option>
        <option id="story">ストーリー</option>
    </select>
    <form class="Form" action="" method="get">
        <p>クエストID:</p><input id="idtext" name="questid" type="text" size="5">
        <p>クエスト名:</p><input id=titletext name="questtitle" type="text" size="12">
       <div id="questparam"></div>

       <input id ="addtbtn" type="submit" name="questdataset" value="クエスト登録">
    </form>

    <script type="text/javascript">
        const questMode = document.getElementById("questmode");
        function SetQuestParam(){
            if(questMode.value === "バトル"){//バトルデータ
                document.getElementById("questparam").innerHTML = "音楽データID:<input id=musicid type=text size=2 name=musicId>";
            }else  if(questMode.value === "ストーリー"){//ストーリーデータ
                document.getElementById("questparam").innerHTML = "テキストファイルパスID:<input id=storyid type=text size=256 name=textPath>";
            }
        }
    </script>
</body>
</html>