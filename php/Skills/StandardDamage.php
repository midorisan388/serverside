<?php
ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once( ROOT_DIR."/php/Skills/SkillClass.php");
require_once( ROOT_DIR."/php/StetasClass/StetasUpgrade.php");

/**
* @class 倍率ダメージスキル
*/
class StandardDamage extends SkillBase{
    /**
    * @param name : スキル名
    * @param ct : チャージターン数
    * @param stetasArray : 倍率
    */
    public function __construct($name,$ct,$sId,$stetasArray,$userid){
        $this->skillId=$sId;
        $this->userid=$userid;
        $this->arguments=$stetasArray;
        $this->skillname = $name;
        $this->skillCharge=(int)$ct;
    }

    /**
    * 基礎ダメージに倍率をかけて計算
    * @param actionplayer : 使用者サイドのステータスリスト
    * @param targetSt : 対象サイドのステータスリスト
    * @param id : 使用者のPTID
    * @param eId :　対象のPTID
    */
    public function skillaction($actionplayer, $targetSt, $uId){
        $actionSt = $actionplayer[$uId]->getParameta();

        $eId = $actionplayer[$uId]->targetSet($targetSt,$uId);
        $tagSt =  $targetSt[$eId]->getParameta();

        $damageup = (int)$this->arguments;

        //ダメージ計算
        $Basedamage = $actionSt["pow"];
        $damage = ($Basedamage + random_int(10,100))* $damageup - $tagSt["def"];
        $actionMes = $actionplayer[$uId]->characterName."はスキル<strong class=damage >".$this->skillname."</strong>を発動<br>";

        //ダメージ処理
        $actionMes .= $targetSt[$eId]->Damaged($damage);
        //$actionMes .=  $targetSt[$eId]->characterName."に".$damage."のダメージを与えた<br>";

        return [$actionplayer, $targetSt,$actionMes]; 
    }
}

?>
