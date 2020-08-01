<?php
//クエストデータとアイテムIDの整合性チェック
error_reporting(E_ALL);
ini_set('error_log', '/tmp/php.log');
ini_set('log_errors', true);
ini_set('display_errors',"On" );

session_start();
define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."\serverside");
require_once(ROOT_DIR."\php\getDataMusic.php");
unset($_SESSION["QUEST_ID"]);

function questDatagenerate($panelId_){
    //クエスト詳細情報生成
    $quest_data="クエスト詳細";//クエスト詳細情報
    $user_quest_stetas="ユーザの成績";//ユーザのクエスト成績

    $view_questdata = $_SESSION["QUESTID_LIST"][$panelId_];//クエストデータをパネルIDで取得
    $musicData = getMusicData($view_questdata);//音楽データ再取得

    //クエスト詳細情報　クエスト名とクリア状況
    $quest_data="<div id=quest_title>".$view_questdata["title"]."</div>
        <div id=clear_flag>未クリア</div>
        <div id=music_title>".$musicData["musictitle"]."</div>
        <div id=piece_count>ピース数/99</div>";

    //クエスト成績情報 仮表示
    $user_quest_stetas="
            クエスト成績
            <div class=nomal_stetas>
                <p class=nomal_title>ノーマル</p>
                <div id=max_score>ノーマル:20000</div>
                <div id=max_comb>ノーマル:20</div>
            </div>
            <div class=hard_stetas>
                <p class=hard_title>ハード</p>
                <div id=max_score>ハード:38000</div>
                <div id=max_comb>ハード:38</div>
            </div>
            <div class=extream_stetas>
                <p class=extream_title>エクストリーム</p>
                <div id=max_score>エクストリーム:120045</div>
                <div id=max_comb>エクストリーム:178</div>
            </div>";

    //クエスト情報データを返す
    return $returndata=array(
        "questviewdata"=>$quest_data,
        "userquestdata"=>$user_quest_stetas
    );

}

try{
    //3.クエスト開始が選択された
    if(isset($_POST["quest_start"])){
       // $questid = $_SESSION["QUESTID_LIST"][$_SESSION["PANEL_ID"]];//選択されているパネルIDからクエストデータ取得
        //$_SESSION["QUEST_ID"] = $questid["ID"];//戦闘シーンで渡すクエストIDをセッションで保持

        header( "Location: /game/battle" );
        exit();

    }else if(isset($_POST["panelId"])){
        //2.クエストパネルが選択された
        SelectQuestPanel();
    }else{
        //1.初回はパネルリスト生成
        //ユーザの状態をクエスト選択状態に変更
        require_once(ROOT_DIR."\php\UpdateUserStageId.php");
        UpdateStageId(3,$_SESSION["userid"]);

       InitQuestpanel();
    } 

}catch(PDOExeption $erro){
    echo "次のエラーが発生しました<br>";
    echo $erro->getmessage();
}

function InitQuestpanel(){
      //クエストリスト生成
      $quest_list_panels = questIdPanelGenerate();
      //クエスト一覧を返す
      $resdata=array(
          "questpanel"=>$quest_list_panels,//クエスト一覧のパネル
      );

      ResponsData($resdata);
}
   
function SelectQuestPanel(){
    
    $selected_panelId=(int)$_POST["panelId"];
    $_SESSION["PANEL_ID"] = $selected_panelId;//選択されているパネルIDを保持
    $_SESSION["QUEST_ID"] = $selected_panelId;

    //パネル生成
    //$quest_list_panels = questIdPanelGenerate();
    //クエスト表示情報取得
    $quest_dataview = questDatagenerate($selected_panelId);//パネルIDを渡してクエスト情報取得

    //クエスト一覧と詳細情報とユーザー成績を返す
    $resdata=array(
        "selectquestdata"=>[ $_SESSION["PANEL_ID"],  $_SESSION["QUEST_ID"] ],
        //"questpanel"=>$quest_list_panels,//クエスト一覧のパネル
        "questdata"=>$quest_dataview["questviewdata"],//クエスト詳細情報
        "userqueststetas"=>$quest_dataview["userquestdata"]//ユーザー成績
    );
    ResponsData($resdata);
}

//クエスト一覧表示パネル生成とパネルIDとクエストデータの対応付け
function questIdPanelGenerate(){
    require_once( ROOT_DIR."\php\getDataMusic.php");

    //クエストIDリスト生成
    $questData =array();//表示クエストデータ
    $questArray=array();
    $quest_panel_list=[];
    $quest_list="";//クエストパネルリスト<div>要素

    //クエストデータリストファイルパス
    $questFileName= ROOT_DIR."\datas\gameMasterData\questDataList.json";

    //クエストデータ取得
    $questJson = file_get_contents($questFileName);//jsonファイル読み込み

    $questArray = json_decode(mb_convert_encoding($questJson, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN'),true);//連想配列にエンコード

    $index=1;//クエストID+1と一致
    $row=1;//上から何番目
    $culms=0;//左から何番目
    $panel_count=0;

    //SQL接続-----------------------------------------------------------------
    require(ROOT_DIR."\datas\sql.php");
    $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
    $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
    $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    //----------------------------------------------------------------------
    foreach($questArray as $quest){

        $user_quest_data = $sql_list->query("SELECT * FROM {$userquestdata} WHERE UserID = '{$_SESSION["userid"]}' AND QuestID='{$index}'");
        $user_quest_data = $user_quest_data->fetch();
        $clear = (int)$user_quest_data["ClearFlag"];

        $clear_mes = ($clear === 0)? "未クリア":"クリア済み";

        array_push($quest_panel_list, $quest);//パネルに対するクエストデータ格納
        $panel_count++;
        $questId = $index-1;
        $culms++;

        if($panel_count === 6){//グリッドスタイル設定
            $row++;//改行
            $culms=1;
            $panel_count=1;//１行のパネル数カウントリセット
        }
        $quest_list .="<div class=quest_panel style=grid-row:".$row.";grid-column:".$culms." id=quest_".$questId." onclick=clickQuestPanel({$questId})><div id=quest_title>".$quest["title"]."</div><div id=clear_flag>{$clear_mes}</div></div>";
        $index++;
    }

    //クエストデータとパネルIDの対応をセッションで保持
    $_SESSION["QUESTID_LIST"]=$quest_panel_list;

    //レスポンスデータ生成
    return $quest_list;//<div>リスト返す
}

function ResponsData($data){
    //レスポンスデータ整頓
    header('Content-Type: application/json; charset=utf-8');
    $resjson =json_encode( $resdata,JSON_PRETTY_PRINT );
    echo  $resjson;
    exit();//処理終了
}
?>