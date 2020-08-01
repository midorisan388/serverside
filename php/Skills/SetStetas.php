<?php

ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once(ROOT_DIR."/php/StetasClass/StClass.php");
require_once(ROOT_DIR."/php/Skills/SkillClass.php");

/**
 *  @class 状態を自身に付与するスキル
 */
class SetStetas extends SkillBase{

    /**
     *  @param name :　スキル名
     *  @param ct : チャージターン数 
     *  @param stetasArray : 付与する状態ID
     */
    public function __construct($name,$ct,$sId,$stetasArray,$userid){
        $this->skillId=$sId;
        $this->userid=$userid;
        $this->arguments=$stetasArray;
        $this->skillname = $name;
        $this->skillCharge=(int)$ct;
    }

    /**
     *  自身に状態を付与
     * @param actionplayer : 使用者PTのステータスリスト
     * @param targetSt :　敵パーティのステータスリスト
     * @param id :　スキル使用者のPTID
     */
    public function skillaction($actionplayer, $targetSt, $uId){
        $actionMes = $actionplayer[$uId]->characterName."はスキル<strong class=damage >".$this->skillname."</strong>を発動<br>";

        foreach($this->arguments as $stetasId){//ステータス異常IDの数ループ
            //スキルデータ取得
            $setSt=$this->searchStetas($stetasId);
            $responsdata = $actionplayer[$uId]->setStetas($setSt[0]);
            //$actionplayer[$id][] = $responsdata["updateSt"];
            $actionMes .="自身に".$setSt[1]."を付与<br>";
        }
        
        return [$actionplayer, $targetSt, $actionMes ]; 
    }


    /**
     * 渡された状態IDからオブジェクト生成
     * 
     * @return 状態オブジェクト
     */
    private function searchStetas($stId){
        $stetasFile=file(ROOT_DIR."/datas/csv/StetasDataList.csv");
        $setStetas = null;
        //stetasIdsが昇順であることを前提とする
        foreach($stetasFile as $stetas){
            $data = explode(',', $stetas);
            if((int)$data[0] === (int)$stId){
                require_once(ROOT_DIR."/php/StetasClass/".$data[2].".php");

                //状態オブジェクト生成
                $setStetas = new $data[2]($data[1],$data[3],$data[4]);
                return [$setStetas,$data[1]];
            }
        }
        require_once(ROOT_DIR."/php/StetasClass/PowerUp.php");
        return [new PowerUp("攻撃てすと",1,3),"無効値"];
    }
}


?>
