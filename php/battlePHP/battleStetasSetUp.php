<?php
// セッションにステータスの初期値をセット
ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once( ROOT_DIR."/php/battlePHP/BattleActorStetas.php");
require_once( ROOT_DIR."/php/battlePHP/EnemyBaseClass.php");
require_once( ROOT_DIR."/php/getcharacterlist.php");

//初期化
function SetInitStetas(){
    $_SESSION["partySt"]=array();
    $_SESSION["enemyStMst"]= $_SESSION["enemySt"]=array();

    try{
        SetPartyStetas();
        SetEnemyStetas(); 
    }catch(PDOExeption $erro){
        echo "次のエラーが発生しました<br>";
        echo $erro->getmessage();
    }
}

function SetPartyStetas(){
    require(ROOT_DIR."/datas/sql.php");
    $userid = $_SESSION["userid"];//ログインユーザーID
    $partymember_ids=array();

    //SQL接続-----------------------------------------------------------------
    $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
    $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
    $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    //----------------------------------------------------------------------
    $user_party_table = $sql_list->query("SELECT * FROM {$userpartytable} WHERE UserID='{$userid}'");
    $charIds = $user_party_table->fetch();

    //初回なら値をセット
    //if(!isset($_SESSION["patySt"])){//バトル中使っていくパーティステータス
        for($i=1; $i<=4; $i++){
            array_push($partymember_ids, $charIds[$i]);
        }

        $setpartyIds=($_SESSION["eventQuestParam"]["evnQuest"])? UpdateSetPartyStetas($partymember_ids) : $partymember_ids;
        $_SESSION["partySt"]=CreatePartyStetas($setpartyIds);
    //}
}

//イベント用パーティメンバーIDに差し替え
function UpdateSetPartyStetas($userpartyIds){
    $partyIds=array();
    $index=0;
    foreach ($_SESSION["eventQuestParam"]["sParty"] as $id) {
        if($id === -1){
            array_push($partyIds, $userpartyIds[$index]);
        }else{
            array_push($partyIds, $id[0]);
        }
        $index++;
    }
    return $partyIds;
}

function SetEnemyStetas(){
    $enemyStetasList=array();//全敵ステータス
    $enemystandSt =array();//更新用敵ステータス
        
    //if(!isset($_SESSION["enemyStMst"]) && !isset($_SESSION["enemySt"])){
        //敵ステータスをセット
        $stDateFilePath=ROOT_DIR."/datas/csv/EnemyStetas.csv";
   
        $enemyList = CreateEnemyStetas($_SESSION["questData"][0]["enemyList"]);
        $_SESSION["enemyStMst"] = $enemyList[0];//全敵ステータスリスト
        $_SESSION["enemySt"] = $enemyList[1];//攻撃対象になる敵のステータスリスト(前衛4体)    
    //}
}

/**
 * パーティメンバーのステータスクラス生成
 * @param ids: パーティーメンバーのキャラクターIDリスト
 * @return パーティーメンバーオブジェクトのリスト
 */
function CreatePartyStetas($ids){
    $partySt=array();//更新用パーティステータス
    $partyStetas_Origine=array();//ステータスの初期値
    $party_csvData=array();
    $csvpath = ROOT_DIR."/datas/csv/CharactersStetas.csv";

    //パーティのキャラIDを読み込む
    for($index=0;$index<4;$index++){
        $partyid=(int)$ids[$index];
        
        if($partyid > 0){
            //$partymember_idsをもとにデータ取得
            array_push($partyStetas_Origine,getRecord($partyid,$csvpath));//キャラデータの初期値取得(マスターデータ)

            //キャラのレベル取得
            require(ROOT_DIR."/datas/sql.php");
            $userid = $_SESSION["userid"];//ログインユーザーID
            //SQL接続-----------------------------------------------------------------
            $sql_list=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
            $sql_list->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
            $sql_list-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            //----------------------------------------------------------------------
            $characterData = $sql_list->query("SELECT * FROM {$userplayable} WHERE UserID='{$userid}' AND CharaID={$partyid}");
            $characterData=$characterData->fetch();
            $charaLv=(int)$characterData["CharaLv"];
            $charaExp=(int)$characterData["characterhaveExp"];

            //更新用ステータス連想配列格納
            $party_csvData[$index]=array(
                "imgid"=>$partyStetas_Origine[$index][1],//画像の通し番号
                "name"=>$partyStetas_Origine[$index][2], //表示キャラ名
                "id"=>$partyid,//キャラクターID
                "type"=>$partyStetas_Origine[$index][13],//バトルタイプ
                "dravegage"=>0,//奥義ゲージ(%)
                "HP"=>$partyStetas_Origine[$index][15],//基礎体力
                "pow"=>$partyStetas_Origine[$index][16],//基礎攻撃力
                "def"=>$partyStetas_Origine[$index][17],//基礎防御力
                "magicpow"=>$partyStetas_Origine[$index][18],//基礎魔力
                "mental"=>$partyStetas_Origine[$index][19],//基礎精神力
                "skillid"=>$partyStetas_Origine[$index][21],//スキルID
                "lskillid"=>$partyStetas_Origine[$index][22]//リーダースキルID
            );
            array_push($partySt, new BattleActor($party_csvData[$index],$index,$charaLv, $charaExp));
        }
    }
    return $partySt;
}

/**
 * 敵のステータスクラス生成
 * @param ids: エネミーリストのIDリスト
 * @return エネミーオブジェクトのリスト
 */
function CreateEnemyStetas($ids){
    $enemySt=array();//更新用パーティステータス
    $enemyStetas_Origine=array();//ステータスの初期値
    $enemyStandSt=array();
    $enemy_csvData=array();
    $csvpath = ROOT_DIR."/datas/csv/EnemyStetas.csv";

    //エネミーIDを読み込む
    for($index=0; $index < count($ids); $index++){
        $enemyid=$ids[$index];
        
        if($enemyid > 0){

            //目的のエネミーIDが見つかるまで検索
            array_push($enemyStetas_Origine,getRecord($enemyid,$csvpath));//敵のマスタデータ取得

            //更新用ステータス連想配列格納
            $enemy_csvData[$index]=array(
                "id"=>$enemyid,//キャラクターID
                "imgid"=>$enemyStetas_Origine[$index][1],//画像の通し番号
                "name"=>$enemyStetas_Origine[$index][2], //表示キャラ名
                "race"=>$enemyStetas_Origine[$index][3],//種族ID
                "type"=>$enemyStetas_Origine[$index][5],//バトルタイプ
                "dravegage"=>0,//奥義ゲージ(%)
                "HP"=>$enemyStetas_Origine[$index][6],//基礎体力
                "pow"=>$enemyStetas_Origine[$index][7],//基礎攻撃力
                "def"=>$enemyStetas_Origine[$index][8],//基礎防御力
                "magicpow"=>$enemyStetas_Origine[$index][9],//基礎魔力
                "mental"=>$enemyStetas_Origine[$index][10],//基礎精神力
                "skillid"=>$enemyStetas_Origine[$index][11]//スキルID
            );

            //敵リスト全体
            array_push($enemySt, new BaseEnemyClass($enemy_csvData[$index],$index));
            //前衛的リスト
            if($index < 4){
                array_push($enemyStandSt, $enemySt[$index]);
            }
        }
    }

    return [$enemySt,$enemyStandSt];
}
?>