<?php

ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once( ROOT_DIR."\php\Skills\SkillClass.php");

/**
 *  @class 状態を敵に付与するスキル
 */
class SetStetasEnemy extends SkillBase{

    /**
     *  @param name :　スキル名
     *  @param ct : チャージターン数 
     *  @param stetasArray : 付与する状態ID
     */
    public function __construct($name,$ct,$sId,$stetasArray,$userid){
        $this->skillId=$sId;
        $this->userid=$userid;
        $this->arguments=$stetasArray[0];
        $this->skillname = $name;
        $this->skillCharge=(int)$ct;
    }

    /**
     *  自身に状態を付与
     * @param actionplayer : 使用者PTのステータスリスト
     * @param targetSt :　対象パーティのステータスリスト
     * @param id :　スキル使用者のPTID
     */
    public function skillaction($actionplayer, $targetSt, $uId){
        $actionMes = $actionplayer[$uId]->characterName."はスキル<strong class=damage >".$this->skillname."</strong>を発動<br>";

        $actionSt = $actionplayer[$id]->getParameta();
        $stetasIds = array();
        $stetasIds = $this->arguments;

        foreach($stetasIds as $stetasId){//ステータス異常IDの数ループ
            
            $responsdata = $targetSt[$eId]->setStetas($this->searchStetas($stetasId));

            //付与確率判定
            if($actionSt["mental"] * 0.3 >= random_int(0, 1000)){
                $targetSt[$eId] = $responsdata["updateSt"];
                $actionMes .=　$targetSt[$eId]->characterName."に{$responsdata['Stname']}を付与<br>";
            }
        }
        return [$actionplayer, $targetSt,$actionMes ]; 
    }


    /**
     * 渡された状態IDからオブジェクト生成
     * 
     * @return 状態オブジェクト
     */
    private function searchStetas($stId){
        $stetasFile=file(ROOT_DIR."\datas\csv\StetasDataList.csv");
        $setStetas = null;
        //stetasIdsが昇順であることを前提とする
        //IDから状態クラスを生成
        foreach($stetasFile as $stetas){
            $data = explode(',', $stetas);
            //比較するスキルID
            $dataIndex =(int)$data[0];

            if($dataIndex === $stId){
                require_once( ROOT_DIR."\php\StetasClass\\".$data[2].".php");

                //状態オブジェクト生成
                $setStetas =new $data[2]($data[1],$data[3],$data[4]);
                break;
            }
        }
        return $setStetas;
    }
}


?>
