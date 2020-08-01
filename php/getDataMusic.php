<?php 

/**
 * 音楽データ取得
 * @param qdata: クエストデータ
 * @return 音楽データ
 */
function getMusicData($qdata){//クエストデータ取得
    $musicFileName= ROOT_DIR."\datas\gameMasterData\musicDataList.json";//音楽データリストファイルパス

    //jsonデータ一覧格納
    $musicJson = file_get_contents($musicFileName);
    $musicArray = mb_convert_encoding($musicJson, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $musicArray = json_decode($musicArray,true);

    //クエストデータの音楽IDから音楽データ検索
    $musicData = $musicArray[(int)$qdata["musicData"]];

    return $musicData;
}

?>