<?php
ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once(ROOT_DIR."\php\Skills\SkillClass.php");

//セレンのアクティブスキル
class SerenActiveSkill extends SkillBase{

    public function __construct($name,$ct,$sId,$stetasArray,$userid){
      $this->skillId=$sId;
      $this->userid=$userid;
      $this->arguments=$stetasArray;
      $this->skillname = $name;
      $this->skillCharge=(int)$ct;
   }

    /**
     * パーティ回復。戦闘不能メンバーの数だけ回復性能UP。セレンのレベルが50以上なら確率蘇生効果追加
     * @param actionplayer : 味方PTのステータス
     * @param targetSt : 敵PT のステータス(使わない)
     * @param id : 使用者PTID
     */
    public function skillaction($actionplayer, $targetSt, $uId){
        $heal = (int)$this->arguments;
        $upVal=$this->UpVal($actionplayer);
        $reviv = ($actionplayer[$uId]->getParameta()["level"] >= 50);

        for($i =0 ;$i<4; $i++){
            if(!$actionplayer[$i]->isAwaken() && $reviv){
                //蘇生効果追加
                $actionplayer[$i]->Revive($heal);
            }else{
                $actionplayer[$i]->Characterparam["currentDamage"]-=$heal;
                if($actionplayer[$i]->Characterparam["currentDamage"] < 0)$actionplayer[$i]->Characterparam["currentDamage"] = 0;
            }
        }
        $_SESSION["partySt"]=$actionplayer;
          
        $actionMes = $actionplayer[$uId]->characterName."はスキル<strong class=damage >".$this->skillname."</strong>を発動<br>";

        return [$actionplayer, $targetSt,$actionMes ]; 
    }

    private function UpVal($party){
        $cnt=0;
        foreach($party as $a){
            if(!$a->isAwaken()){
                $cnt++;
            }
        }

        return (1+($cnt*2)/10);
    }
}

?>