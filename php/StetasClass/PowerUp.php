<?php

ini_set('display_errors',"On");
error_reporting(E_ALL);
require_once( ROOT_DIR."/php/StetasClass/StClass.php");

//攻撃力上げるステータス
class PowerUp extends Stetas{
    public function stetasfunc($damase_upval){
        $damase_upval += $this->arguments;//上昇率増加
        return $damase_upval;//上昇するダメージ量を返す
    }
};

?>