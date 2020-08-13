<?php


abstract class BaseBattler{
    private $level=1;
    private $index=0;
    private $skillCnt=0;
    private $deadCnt=0;
    private $baseParam;
    private $actionSkill;
    private $leaderSkill;

    public function __construct($baseParam,$index,$level,$skillParam,$leaderSkillParam){
        $this->baseParam=$baseParam;
        $this->actionSkill = new $skillParam["skillClass"]($skillParam);
        $this->leaderSkill = new $leaderSkillParam["skillClass"]($leaderSkillParam);
    }

    public function Action($party,$enemy){
        $actionmsg="";
        if(!($this->isAwaken())){
            $actionmsg =  $this->baseParam["name"]."は体勢を立て直している…";
            return $actionmsg;
        }

        if($this->canUseSkill()){
            $actionmsg=$this->UseSkill($party,$enemy);
            return $actionmsg;
        }

    }

    public function UseSkill($party,$enemy){
        $actionmng=$this->baseParam["name"]."は".$this->actionSkill->getSkillName()."を使用した";
        return $actionmng;
    }

    private function isAwaken(){
        return ($this->baseParam["health"] > 0);
    }

    private function SetDeadStatus($vals){
        $this->deadCnt = $val;
        $this->baseParam["health"]=0;
    }

    private function AddDeadCnt($val){
        $this->deadCnt -= $val;
        if($this->deadCnt < 0){
            $this->deadCnt=0;
            $this->baseParam["health"] += $this->baseParam["maxHealth"]*0.1;
        }
    }

    private function canUseSkill(){
        return ($this->skillCnt >= $this->actionSkill->getSkillCount());
    }
}

?>