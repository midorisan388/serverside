<?php
ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once( ROOT_DIR."/php/StetasClass/StClass.php");

    //防御力上げるステータス
    class DefenseUp extends Stetas{
        public function stetasfunc($def_upval){
            $def_up = 0;
            $def_up += $this->arguments;//上昇率増加
            return $def_up;//上昇するダメージ量を返す
        }
    };
?>