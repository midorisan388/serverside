<?php
ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once(ROOT_DIR."\php\Skills\SkillClass.php");

class SimpleHeal extends SkillBase{//単体回復
    private $heal = 1;//回復量

    public function __construct($name,$ct,$sId,$stetasArray,$userid){
      $this->skillId=$sId;
      $this->userid=$userid;
      $this->arguments=$stetasArray;
      $this->skillname = $name;
      $this->skillCharge=(int)$ct;
   }

    /**
     * 味方を回復する
     * @param actionplayer : 味方PTのステータス
     * @param targetSt : 敵PT のステータス(使わない)
     * @param id : 使用者PTID
     */
    public function skillaction($actionplayer, $targetSt, $uId){
      $heal = (int)$this->arguments;
      for($i =0 ;$i<4; $i++){
          if($a->isAwaken() && $reviv){
            $a->Characterparam["currentDamage"]-=$heal;
            if($a->Characterparam["currentDamage"] < 0)$a->Characterparam["currentDamage"] = 0;
          }
      }
      $_SESSION["partySt"]=$actionplayer;
          
        $actionMes = $actionplayer[$uId]->characterName."はスキル<strong class=damage >".$this->skillname."</strong>を発動<br>";
        $actionMes .=  "味方を".$this->heal."回復した<br>";

        return [$actionplayer, $targetSt,$actionMes ]; 
    }
}

?>