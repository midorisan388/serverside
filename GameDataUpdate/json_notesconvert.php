<?php
    ini_set('display_errors',"On");
    error_reporting(E_ALL);

    $dir = '../Notesfile/';
    $dot = '.json';
    $line_len=0;
    $json_conv_txt ='[';//最初の括弧 

    if(isset($_POST["upld"]) && isset($_FILES["up"])){
        //ファイルをディレクトリに保存
        $up_file_name =$dir.basename($_POST['filenm']);
        $file_name = $_FILES["up"];

        //ファイル生成
         if(move_uploaded_file($_FILES['up']['tmp_name'], $up_file_name.$dot)){
            echo "<br>保存完了<br>";
        }else{
            echo "<br>エラー<br>";
        }
        //ファイル読み込み
        $convert_text = (file(__DIR__ . $up_file_name.$dot));

        //余分な文字を削除
        //var_dump($convert_text);
        //json形式にコンバート   既存データ読み込み
        $file = fopen($up_file_name.$dot, 'r');
        if($file){
            $row_txt='';
            while ($line = fgets($file)){
                //最初の行でなければ,をつける
                if($line_len > 0){
                    $row_txt .=',';
                }
                $i=0;
                while($line[$i] !== '{'){
                    $i++;
                }
                $n=$i; $count=0;
                while( $line[$n] !== '}'){
                    //{ ~ }までを一文字ずつ読み込む
                    $count++;
                    $n=$i+$count;
                    $row_txt = substr($line,$i,$count);
                }
                $row_txt .='},'."\n";
                $json_conv_txt .= $row_txt;    
            }
            $line_len++;//行数カウント 
        }
        fclose($file);
        $json_conv_txt .=']';

        print_r($json_conv_txt);
        //書き込み
        $new_norts_file = fopen($up_file_name.$dot, 'w');
        fwrite($new_norts_file, $json_conv_txt);
        fclose($new_norts_file);
        
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ノーツファイル整頓</title>
</head>
<body>
    <form class="Form" action="" method="post" enctype="multipart/form-data">
        <input type="file" name="up">
        <input type="text" name="filenm" size="12">
        <input type="submit" name="upld" value="送る">
    </form>
</body>
</html>