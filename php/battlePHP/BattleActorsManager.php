<?php
//バトルキャラクター管理
ini_set('display_errors',"On");
error_reporting(E_ALL);      

define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT']."/serverside");

require_once(ROOT_DIR."/php/NotesGlobal.php");
require_once(ROOT_DIR."/php/battlePHP/BaseBattler.php");
require_once(ROOT_DIR."/php/battlePHP/EnemyBaseClass.php");
require_once( ROOT_DIR."/php/battlePHP/BattleActorStetas.php");
session_start();
require_once( ROOT_DIR."/php/NotesCounter.php");

class BatttleActorsManager{

    $partyMembers=array();
    $enemyMembers=array();
    $enemyMemberAll=array();

    /**
     * コンストラクタ
     * --------------------------------------------------
     * @param partyMembers: プレイヤーキャラクタークラスリスト
     * @param enemySt: 敵キャラクタークラスリスト(前衛)
     * @param enemyList: 登場敵キャラクタークラスリスト(全リスト)
     */
    public function __construct($partySt,$enemySt,$enemyLs){
       $this->UpdateStetas($partySt,$enemySt,$enemyLs);
    }

    /**
     * ステータス情報の更新
     * --------------------------------------------------
     * @param partyMembers: プレイヤーキャラクタークラスリスト
     * @param enemySt: 敵キャラクタークラスリスト(前衛)
     * @param enemyList: 登場敵キャラクタークラスリスト(全リスト)
     */
    public UpdateStetas($partySt,$enemySt,$enemyLs){
        $this->partyMembers=$partySt;
        $this->enemyMemners=$enemySt;
        $this->enemyMemberAll=$enemyLs;
    }

    /**
     * 行動処理管理
     * -----------------------
     * @param mode:味方の行動か敵の行動か
     * @param cId: パーティインデックス
     */
    public function MainAction($mode,$cId){
        switch($mode){
            case "player":
                $data = $this->PlayerMainAction($cId);
                $this->UpdateStetas($data[0],$data[1],$_SESSION["enemyStMst"]);
            break;
            case "enemy":
                $data = $this->EnemyMainAction($cId);
                $this->UpdateStetas($data[1],$data[0],$_SESSION["enemyStMst"]);
            break;
            default:
                return [$this->partyMembers,$this->enemyMembers,$this->enemyMemberAll];
            break;
        }
    }

    /**
     * プレイヤーキャラクターの行動管理
     * ------------------------------------
     * @param id: パーティインデックス
     */
    private function PlayerMainAction($id){
        if($this->isAwaken_Enemy()){
            return $this->partyMembers[$id]->Attack($this->partyMembers,$this->enemyMembers);
        }
        else{
            return false;
        }
    }

    /**
     * 敵の行動管理
     * -----------------------------------
     * @param id: パーティインデックス
     */
    private function EnemyMainAction($id){
        if($this->isAwaken_Party()){
            return $this->enemyMembers[$id]->Attack($this->enemyMembers,$this->partyMembers);
        }else{
            return false;
        }
    }

    /**
     * プレイヤーパーティの全滅判定
     */
    public function isAwaken_Party(){
        foreach($this->partyMembers as $p){
            if($p->isAwaken()) return true;
        }
        return false;
    }

    /**
     * 敵全滅判定
     */
    public function isAwaken_Enemy(){
        foreach($this->enemyMemberAll  as $e){
            if($e->isAwaken()) return true;
        }
        return false;
    }

    /**
     * 敵リストの順番入れ替え
     * --------------------------
     * @brief 前衛が戦闘不能になった時呼び出す
     */
    public function EnemyListChange($eId){

    }

}

?>