<?php
//バトルメイン処理
ini_set('display_errors',"On");
error_reporting(E_ALL);      

define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."\serverside");

require_once(ROOT_DIR."\php\NotesGlobal.php");
require_once(ROOT_DIR."\php\battlePHP\BaseBattler.php");
require_once(ROOT_DIR."\php\battlePHP\EnemyBaseClass.php");
require_once(ROOT_DIR."\php\battlePHP\BattleActorStetas.php");
//require_once(ROOT_DIR."php\battlePHP\BattleActorsManager.php");

session_start();
require_once( ROOT_DIR."\php\NotesCounter.php");


try{
    $notes = $_POST["notesdata"];
    $gametimer = $_POST["time"];
    $lern = (int)$_POST["lernid"];
    $judgemode = $_POST["updatenotes"];
    $_SESSION["enemyChange"]=false;
    $_SESSION["consert"]=false;
    $nId=(isset($_POST["notesId"]))? $_POST["notesId"]:-1;
    $setPartyParam = (isset($_SESSION["eventBattleParty"]))? $_SESSION["eventBattleParty"] : $_SESSION["partySt"];

    $battleMg = new BattlleMain($setPartyParam, $_SESSION["enemySt"], $_SESSION["enemyStMst"]);
    $battleMg->ActionMain( $notes, $lern , $gametimer, $judgemode, $nId);
    $battleMg->ReturnJson();
  
}catch(PDOExeption $erro){
    echo "次のエラーが発生しました<br>";
    echo $erro->getmessage();
}

/**
*$enemyStetasList :敵ステータスリスト
*$enemyStetas :攻撃対象になる敵のステータス
*$partyStetas :バトルメンバーステータス
*$gameFlag  :ゲーム続行フラグ
*$damage    :ダメージ量
*$motion    : アニメーションの種類
*$actionMes : 行動内容を表記
**/


/**
 * @class 戦闘状態を管理する
 * @brief: イベント戦闘用に抽象化
 */
class BattlleMain{
    
    private $gameFlag;
    //private $motion="idle";
    private $actionMes;
    
    //前提条件　ステータスの初期化が完了している
    
    private $enemyStetasList=array();
    private $enemyStetas=array();
    private $partyStetas=array();
    
    private $NotesStetas=array();
    private $csvpath;
    private $resdata=array();

    //private $BattleMmbMng=null;

    public function __construct($sessionParty, $sessionEnemy, $sessionElist){
        //$this->BattleMmbMng=new BattleActorsManager($sessionParty,$sessionEnemy, $sessionElist);

        $this->partyStetas=$sessionParty;
        foreach($this->partyStetas as $p)    
            if($p->motion != "dead")$p->motion="idle";
        
        $this->enemyStetas = $sessionEnemy;
        $this->enemyStetasList= $sessionElist;
    
        $this->gameFlag = "Continue";
        $this->damage = 0;
        //$this->motion="idle";
        $this->actionMes ="";
        $this->csvpath ="../../datas/csv/CharactersStetas.csv";
        $this->resdata=array();
    }
    
     //スキル使用処理
     function SkillUse( $id ){ 
    
        $skillUpdate =$this->partyStetas[$id]->skillUse($this->partyStetas, $this->enemyStetas, $id);
        
        $this->partyStetas = $this->skillUpdate[0];
        $this->enemyStetas = $this->skillUpdate[1];
        $this->actionMes = $this->skillUpdate[2];

        //$this->motion="skill";

    }

    //通常攻撃
    function NomalAttack($id){
        $resdata = $this->partyStetas[$id]->Attack($this->partyStetas,$this->enemyStetas);

        $this->partyStetas = $resdata[0];
        $this->enemyStetas=$resdata[1];
        $this->actionMes=$resdata[2];
        //$this->motion="attack";
    }

    //メンバー戦闘不能時の処理
    function NotBattle( $id ){
        //$this->motion="dead";
        $this->actionMes = $this->partyStetas[$id]->Damaged(0);

        //蘇生
        //if($this->partyStetas[$id]->isAwaken()) $this->motion="idle";
    }
    
    //パーティの生存キャラがいるか確認
    function saftyParty($partyMembers){
        for($i=0;$i<count($partyMembers);$i++){
            if($partyMembers[$i]->isAwaken() && isset($partyMembers[$i])){
                return true;
            }
        }
        return false;
    }

    //ノーツの評価によって行動者を決定
    function NotesState(){
        if($this->NotesStetas["notesdatas"]["judge"] === "ALWAY"){
            return 'idle';
        }else if($this->NotesStetas["notesdatas"]["judge"] === "MISS" || $this->NotesStetas["notesdatas"]["judge"] === "BAD"){
            return 'enemy';
        }else{
            //プレイヤーの攻撃処理
            return ($this->NotesStetas["notesdatas"]["judge"] != "OVER")? 'player':'song';
        }
    }

    //行動処理
    function ActionMain($notes, $lernid,  $time, $update, $notesId){
       
        $this->NotesStetas = NotesCol($time,$notes,$lernid, $update, $notesId);
        $mode=$this->NotesState();
         /*
        $mode=$this->NotesState();
        $mainAct = $this->BattleMmbMng->ActionMain($mode,$lernid);
        if($mainAct===false){
            if($mode==="player"){
                $_SESSION["consert"]=true;
                $this->actionMes = "コンサートモード<br>";
                $this->motion="song";
            }else if($mode==="enemy"){
                
            }
        }*/
        
        if($mode === 'player'){
            //メンバーが編成されている
            if(isset($this->partyStetas[$lernid])){
                //行動可能である
                if($this->partyStetas[$lernid]->isAwaken()){
                    //敵パーティが全滅していない
                    if($this->saftyParty($this->enemyStetas)){  
                        $_SESSION["consert"]=false;  
                        //行動処理
                        //$this->partyStetas[$lernid]->AddSkillCnt();
                        //if($this->partyStetas[$lernid]->checkSkillFlag()){
                            //$this->SkillUse($lernid); 
                        //}else{
                          $this->NomalAttack($lernid);
                         
                        //}
                    }else{
                        //敵が全滅している
                        $_SESSION["consert"]=true;
                        $this->actionMes = "コンサートモード<br>";
                        foreach($this->partyStetas as $p){
                            $p->motion="song";
                        }
                        //$this->motion="song";
                    }
                }else{
                    //メンバーが戦闘不能時の処理
                    $this->NotBattle($lernid);
                }
                //リーダースキル処理(行動毎)
                if(isset($_SESSION["leaderSkill"]))
                    if($_SESSION["leaderSkill"]->timing === 2)
                        $_SESSION["leaderSkill"]->LeaderSkillAction($this->partyStetas,$this->enemyStetas,$lernid,0,0);
            }else{
                //メンバーが編成されていない処理
                $damage=0;
            }
        }else if($mode === 'enemy' || $update==="miss"){
            //MISS処理
            if(isset($this->enemyStetas[$lernid])){
                if($this->enemyStetas[$lernid]->isAwaken()){
                    if($this->partyStetas[$lernid]->isAwaken()){
                        $data = $this->enemyStetas[$lernid]->Attack($this->enemyStetas,$this->partyStetas);
                        //$this->motion="damage";
    
                        $this->partyStetas=$data[1];
                        $this->enemyStetas=$data[0];

                        $this->actionMes =$data[2];
                    }else{
                        //パーティキャラクターが戦闘不能
                        //$this->motion="dead";
                        $this->actionMes = $this->partyStetas[$lernid]->characterName."は戦闘不能状態です<br>";
                        //スコアを削る
                        $_SESSION["Score"] -=100;
                    }
                }else{
                    //$this->motion="damage";
                    $this->actionMes = $this->partyStetas[$lernid]->characterName."の心譜が乱れた！<br>".$this->NotesStetas["notesdatas"]["judge"]."<br>";
                }
            }else{
                //エネミーデータが存在しないときの処理
                $damage_val=0;
                $_SESSION["Score"] -=100;
                $this->actionMes="スコアが削れた！<br>";
            }
        }else{//判定範囲外にタップされた
            $this->actionMes='';
            
        }
        

        /*-------------------生存判定----------------------------*/
        if($this->saftyParty($this->partyStetas)){
            $this->gameFlag = "Continue";
        }else{
            $this->gameFlag = "Gameover";

            if(isset($_SESSION["leaderSkill"]))
                if($_SESSION["leaderSkill"]->timing === 6)
                    if($_SESSION["leaderSkill"]->LeaderSkillAction($_SESSION["partySt"],$target,0,0,0)){
                        $this->gameFlag = "Continue";
                        //$this->motion="idle";
                        $this->actionMes="リーダースキルでパーティが復帰した";
                    }
        }
    
        if($this->saftyParty($this->enemyStetas)){
            $this->gameFlag = "Continue";
        }else{
            $this->gameFlag = "Clear";
        }

        //セッションデータ更新
         if(!$_SESSION["enemyChange"]){
            $_SESSION["enemyStMst"]=$this->enemyStetasList;//敵ステータスリスト
            $_SESSION["enemySt"]=$this->enemyStetas;//攻撃対象になる敵のステータス   
         }else{
            $this->enemyStetasList = $_SESSION["enemyStMst"];//敵ステータスリスト
            $this->enemyStetas = $_SESSION["enemySt"];//攻撃対象になる敵のステータス  
         }
            $_SESSION["partySt"]=$this->partyStetas;//バトルメンバーステータス
            //$_SESSION["notesdata"]=$this->NotesStetas;

            global $taregtNotes;
        //送信用データJSON
        $this->resdata = array(
            "test"=>$taregtNotes,
            "noteshantei"=>$_SESSION["pointCnt"],
            "score"=>$_SESSION["Score"],
            "combo" =>$_SESSION["Comb"],
            "notesdata"=>$_SESSION["notesdata"],//ノーツデータ
            //"motionState"=>$this->motion,
            "gameOverFlag"=>$this->gameFlag,//ゲーム続行フラグ
            "enemydata"=>$this->enemyStetas,//敵ステータス
            "enemyList"=>$this->enemyStetasList,
            "memberdata"=>$this->partyStetas,//array($this->partyStetas[0]->getParameta(),$this->partyStetas[1]->getParameta(),$this->partyStetas[2]->getParameta(),$this->partyStetas[3]->getParameta()),//パーティステータス
            "message"=>$this->actionMes//表記内容
        );
    }

    function ReturnJson(){
         //レスポンスデータ整頓
         header('Content-Type: application/json; charset=utf-8');
         $resjson = json_encode( $this->resdata, JSON_PRETTY_PRINT );
         echo($resjson);  
    }

}

?>
