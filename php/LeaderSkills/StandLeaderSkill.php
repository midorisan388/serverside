<?php
//leaderスキルベース
ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once(ROOT_DIR."\php\Skills\SkillClass.php");

class StandLeaderSkill extends BaseLeaderSkill{

    protected $isFlag=true;
    public $timing=1;

    public function __construct($name,$timing,$discription,$sId,$stetasArray,$userid){
        $this->skillId=$sId;
        $this->userid=$userid;
        $this->discription=$discription;
        $this->arguments=array(true,true,true,true);//スキル適用フラグ
        $this->skillname = $name;
        $this->timing=(int)$timing;
    }

    //一度だけHP1残して耐える
    public function LeaderSkillAction($actionplayer, $targetSt, $uId, $tId, $val){
        if($this->isSkill_B($uId)){
            $actionplayer[$uId]->Characterparam["currentDamage"] = $actionplayer[$uId]->Characterparam["hp"]-1;
            $_SESSION["partySt"][$uId]=$actionplayer[$uId];

            $this->arguments[$uId]=false;
            return $actionplayer[$uId]->characterName."は攻撃を耐えた!<br>";
        }

        return false;
    }

    public function isSkill(){
        return $this->isFlag;
    }
    //スキル効果を既に受けているか
    public function isSkill_B($uId){
        return $this->arguments[$uId];
    }
}
?>