<?php
ini_set('display_errors',"On");
error_reporting(E_ALL);
require_once(ROOT_DIR."\php\Skills\SkillClass.php");
require_once(ROOT_DIR."\php\NotesGlobal.php");

//セレン用リーダースキル
class SerenLeaderSkill extends SkillBase{
    protected $isFlag=true;
    public $discription="";
    /**
     * 発動タイミング
     * 0: 通常攻撃時
     * 1: 被攻撃時
     * 2: 行動時
     * 3: 行動終了後
     * 4: スキル使用時
     * 5: 戦闘不能時
     * 6: パーティ全滅時
     * 7: 敵せん滅時
     * 8: バトル開始時
     * 9: 敵撃破時
     * 10:ノーツ判定時
     * 11:演奏終了時 
     * 12:タイプ相性計算時（有利）
     * 13:タイプ相性計算時（不利）
     * 14:コンボ計算時
     * 15:コンボ更新時
     */
    public $timing=0;

    public function __construct($name,$timing,$discription,$sId,$stetasArray,$userid){
        $this->skillId=$sId;
        $this->userid=$userid;
        $this->discription=$discription;
        $this->arguments=$stetasArray[0];
        $this->skillname = $name;
        $this->timing=(int)$timing;
    }

    public function skillaction($actionplayer, $targetSt, $uId){}

    /**
     * パーティ回復
     */
    public function LeaderSkillAction($actionplayer, $targetSt, $uId, $tID, $val){
        if($this->isSkill()){
            $i=0;
            foreach ($actionplayer as $a) {
                if($a->isAwaken()){
                    $a->Characterparam["currentDamage"]-=$actionplayer[$uId]->getParameta()["mpow"]/2;
                    if($a->Characterparam["currentDamage"] < 0)$a->Characterparam["currentDamage"]=0;
                }
            }
            $_SESSION["partySt"]=$actionplayer;
        }
    }

    public function isSkill(){
        global $taregtNotes;

        if($taregtNotes === null) return false;
        if($taregtNotes["judge"] === "MISS" || $taregtNotes["judge"] === "ALWAY")  return false;
        return ($taregtNotes["type"]==="ENH");
            
    }

    public function isSkill_B($val){
        return $this->isFlag;
    }
}
?>