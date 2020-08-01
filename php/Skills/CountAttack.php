<?php

ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once( ROOT_DIR."\php\Skills\SkillClass.php");

class CountAttack extends SkillBase{//連続ダメージ

    public function __construct($name,$ct,$sId,$stetasArray,$userid){
        $this->skillId=$sId;
        $this->userid=$userid;
        $this->arguments=$stetasArray;//攻撃回数
        $this->skillname = $name;
        $this->skillCharge=(int)$ct;
    }

    public function skillaction($actionplayer, $targetSt, $uId){
        $actionMes = $actionplayer[$uId]->characterName."はスキル<strong class=damage >".$this->skillname."</strong>を発動<br>";

        $actionSt = $actionplayer[$uId]->getParameta();
        $eId = $actionplayer[$uId]->targetSet($targetSt, $uId);

        $count=(int)$this->arguments;
        //ダメージ計算
        $tagSt =  $targetSt[$eId]->getParameta();

        $i=1;
        while($i <= $count){
            $Basedamage = $actionSt["pow"];//基礎ダメージ
            $damage = ($Basedamage + random_int(10,100)) - $tagSt["def"];
         
            $actionMes .= $targetSt[$eId]->Damaged($damage);
            //$targetSt[$eId]->currentDamage += $this->damage;

            //$actionMes .= $targetSt[$eId]->characterName."に".$damage."のダメージを与えた<br>";
            $i++;
        }
        
        return [$actionplayer, $targetSt,$actionMes ]; 
    }
}

?>