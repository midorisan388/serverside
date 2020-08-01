<?php 
ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once( ROOT_DIR."/php/Skills/SkillClass.php");
require_once( ROOT_DIR."/php/StetasClass/StetasUpgrade.php");

//倍率ダメージ+PT回復スキル
class OverRay extends SkillBase{
    private $damageup=0;//ダメージ倍率
    private $heal=0;//回復量   
    //スキル名 上昇ダメージ倍率
    public function __construct($name,$ct,$sId,$stetasArray,$userid){
        $this->skillId=$sId;
        $this->userid=$userid;
        $this->arguments=$stetasArray[0];
        $this->skillname = $name;
        $this->skillCharge=(int)$ct;
    }

    public function skillaction($actionplayer, $targetSt,$uId){
        $actionSt = $actionplayer[$id]->getParameta();

        $eId = $actionplayer[$uId]->targetSet($targetSt, $uId);
        $tagSt =  $targetSt[$eId]->getParameta();

        $this->damageup = (int)$this->damageup;//整数にキャスト
        $this->heal =(int)$this->heal;

        //ダメージ計算
        $Basedamage =  $actionSt["pow"];//基礎ダメージ
        $damage = ($Basedamage + random_int(10,100))*$this->damageup -  $tagSt["def"];
        $actionMes = $actionplayer[$uId]->characterName."はスキル<strong class=damage >".$this->skillname."</strong>を発動<br>";

        $actionMes .=$targetSt[$eId]->Damaged($damage);

        
        //$actionMes .=  $targetSt[$eId]->characterName."に".$damage."のダメージを与えた<br>";

        //PT回復
        for($i =0 ;$i<4; $i++){
            $actionSt = $actionplayer[$i]->getParameta();
            $actionSt->damage(-($this->heal));
         }
         $actionMes .= "味方を".$this->heal."回復した<br>";

        return [$actionplayer, $targetSt,$actionMes ]; 
    }
}

?>