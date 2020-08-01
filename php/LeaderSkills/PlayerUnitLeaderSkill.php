<?php
//leaderスキルベース
ini_set('display_errors',"On");
error_reporting(E_ALL);
require_once(ROOT_DIR."/php/Skills/SkillClass.php");

//主人公キャラのリーダースキル
class PlayerUnitLeaderSkill extends BaseLeaderSkill{

    //発動条件など
    protected $isFlag=false;
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
     */
    public $timing=0;

    public function __construct($name,$timing,$discription,$sId,$stetasArray,$userid){
        $this->skillId=$sId;
        $this->userid=$userid;
        $this->discription=$discription;
        $this->arguments=$stetasArray[0];
        $this->skillname = $name;
        $this->timing=$timing;
    }

    //戦闘不能時に一度だけ蘇生する
    public function LeaderSkillAction($actionplayer, $targetSt, $uId, $tId, $val){
        $skillActive=false;
        if($this->isSkill()){
            $skillActive=true;
            $party=$_SESSION["partySt"];
            foreach ($party as $member) {
                $member->Characterparam["currentDamage"] -= (int)($this->arguments); //後でレベル補正
                $member->Characterparam["deadcnt"]=0;
            }
            $_SESSION["partySt"]=$party;
            $this->isFlag=true;
        }
        return $skillActive;
    }

    public function isSkill(){
        $safeParty=false;

        foreach ($_SESSION["partySt"] as $member) {
            if($member->isAwaken()) $safeParty=true;
        }

        return (!$this->isFlag && !$safeParty);
    }

    public function isSkill_B($val){
        return $this->isFlag;
    }
}
?>