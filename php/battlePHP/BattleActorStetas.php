<?php
ini_set('display_errors',"On");
error_reporting(E_ALL);
require_once(ROOT_DIR."\php\battlePHP\BaseBattler.php");
require_once(ROOT_DIR."\php\battlePHP\EnemyBaseClass.php");
require_once( ROOT_DIR."\php\Skills\Require_skillList.php");//スキルファイルリスト

//TODO:ステータスの受け渡しを参照にしたいなぁ

//バトルステータスクラス
class BattleActor extends BaseBattler{
};

?>