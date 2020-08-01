<?php
//session_start();

ini_set('display_errors',"On");
error_reporting(E_ALL);

    class Stetas{//ステータスクラスの親
        public $stetasname="";//ステータス名
        public $argument_val=0;//効果量
        private $Buffturn=0;//効果ターン
        
        public function __construct($name, $argument, $turn){
            $this->stetasname =$name;//表示名
            $this->argument_val = $argument;//効果量
            $this->Buffturn = $turn;//効果時間
        }

        public function getFuncTurn(){
            return $this->Buffturn;//残り効果時間を返す
        }

        public function GetStName(){
            return $this->stetasname;
        }
        public function updateTurn($add_turn){
            //効果時時間を操作
            $this->Buffturn += $add_turn;//０以上で延長、０以下で減少
        }
    }; 
?>