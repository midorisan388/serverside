<?php
ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once( ROOT_DIR."/php/Skills/SkillClass.php");
require_once( ROOT_DIR."/php/StetasClass/StetasUpgrade.php");

//倍率ダメージ+PTに防御UP付与
class Kikyou extends SkillBase{
    private $damageup=0;//上昇倍率    
    private $defBuff=array();//防御ステータスID        
    //スキル名 上昇ダメージ倍率
    public function __construct($name,$ct,$sId,$stetasArray,$userid){
        $this->skillId=$sId;
        $this->userid=$userid;
        $this->arguments=$stetasArray;
        $this->skillname = $name;
        $this->skillCharge=(int)$ct;
    }
                            //技使用者ステータス　対象ステータス
    public  function skillaction($actionplayer, $targetSt, $uId){
        $actionSt = $actionplayer[$uId]->getParameta();

        $eId = $actionplayer[$uId]->targetSet($targetSt, $uId);
        $tagSt =  $targetSt[$eId]->getParameta();

        $damageup = (int)$this->damageup;
        //ダメージ計算
        $Basedamage =  $$actionSt["pow"];;
        $damage = ($Basedamage + random_int(10,100))*$damageup - $tagSt["def"];
        $actionMes = $actionplayer[$uId]->characterName."はスキル<strong class=damage >".$this->skillname."</strong>を発動<br>";

        //ダメージを与える
        $actionMes .= $targetSt[$eId]->Damaged($damage);

        
        //$actionMes .=  $targetSt[$eId]->characterName."に".$damage."のダメージを与えた<br>";

        //PTに防御バフ付与
       // $responsdata = GeinStetasPT($actionplayer, $this->defBuff);
        //$actionplayer = $responsdata["updateSt"]; 
        $actionMes .="味方に{$responsdata['Stname']}を付与した<br>";
        
        return [$actionplayer, $targetSt, $actionMes]; 
    }
}

?>