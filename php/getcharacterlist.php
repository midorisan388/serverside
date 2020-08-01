<?php
ini_set('display_errors',"On");
error_reporting(E_ALL);

try{
  function getRecord($no, $filepath){
    $data_path=$filepath;//csvファイルパス

        $record_data = file($data_path);//csvファイル読み込み
        $i=0;
      foreach($record_data as $line){//データ探索
        $data = explode(',',$line);
          if($i > 0){
            if($i === (int)$no){
            return $data;//csvレコードを返す
            }
        }
          $i++;
      }
  }

  function getJsonData($id, $jsonpath){//jsonデータ取得
    $id=(int)$id;
    $jsondata=file_get_contents($jsonpath);
    $jsondata=mb_convert_encoding($jsondata, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');//文字化け防止
    $jsondata=json_decode($jsondata,true);
    return $jsondata[$id];
  }

  }catch(PDOExeption $erro){
    echo "次のエラーが発生しました<br>";
    echo $erro->getmessage();
  }

?>