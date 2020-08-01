<?php
//クエストデータとアイテムIDの整合性チェック
error_reporting(E_ALL);
ini_set('error_log', '/tmp/php.log');
ini_set('log_errors', true);
ini_set('display_errors',"On" );

require_once( ROOT_DIR."/php/getDataMusic.php");

/**
 * クエストデータ取得
 * @param qId :　クエストID
 * @return クエストデータ , 音楽データ
 */
function setQuestDate($qId){
    try{
        
    //使用データ格納・連想配列初期化
        $questData =array();
        
        $questFileName= ROOT_DIR."/datas/gameMasterData/questDataList.json";
        $musicFileName= ROOT_DIR."/datas/gameMasterData/musicDataList.json";

        $questJson = file_get_contents($questFileName);
        $questJson = mb_convert_encoding($questJson, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $questArray = json_decode($questJson,true);
        $musicData =array();
        $i=0;
        foreach($questArray as $questline){//データ探索
            $data = $questline;//レコードデータ格納
            $id = $data["ID"];//判定用ID格納
            if($qId === $id){//該当クエストデータ取得
                $questData = $data;//該当クエストデータ格納
                //セッションに音楽データ保持
               $musicData = getMusicData($questData);

                break;
            }
         }
         return [$questData, $musicData];
         
    }catch(PDOExeption $erro){
      echo "次のエラーが発生しました<br>";
      echo $erro->getmessage();
    }
}
?>
