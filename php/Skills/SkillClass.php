<?php

ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once(ROOT_DIR."/php/battlePHP/BaseBattler.php");
require_once(ROOT_DIR."/php/battlePHP/BattleActorStetas.php");
require_once(ROOT_DIR."/php/battlePHP/EnemyBaseClass.php");

try{
    //スキルの基底クラス
    abstract class SkillBase {
        private $userid=0;//使用者のパーティインデックス
        public $skillId=0;
        public $skillname;//スキル名
        public $arguments=array();//固有引数
        public $skillCharge;//スキルCT

        abstract public function skillaction($actionplayer, $targetSt,$uId);
    }
}catch(PDOExeption $erro){
    echo "次のエラーが発生しました<br>";
    echo $erro->getmessage();
}

?>
