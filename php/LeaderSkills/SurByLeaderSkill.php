<?php
ini_set('display_errors',"On");
error_reporting(E_ALL);
require_once(ROOT_DIR."\php\Skills\SkillClass.php");
require_once(ROOT_DIR."\php\NotesGlobal.php");
//スーヴァイ用リーダースキル
class SurByLeaderSkill extends SkillBase{
    protected $isFlag=true;
    public $discription="";
    /**
     * 発動タイミング
     * 0: 通常攻撃時
     * 1: 被攻撃時
     * 2: 行動時
     * 3: 行動終了後
     * 4: スキル使用時
     * 5: 戦闘不能時
     * 6: パーティ全滅時
     * 7: 敵せん滅時
     * 8: バトル開始時
     * 9: 敵撃破時
     * 10:ノーツ判定時
     * 11:演奏終了時 
     * 12:タイプ相性計算時（有利）
     * 13:タイプ相性計算時（不利）
     * 14:コンボ計算時
     * 15:コンボ更新時
     */
    public $timing=0;

    public function __construct($name,$timing,$discription,$sId,$stetasArray,$userid){
        $this->skillId=$sId;
        $this->userid=$userid;
        $this->discription=$discription;
        $this->arguments=(int)$stetasArray[0];
        $this->skillname = $name;
        $this->timing=(int)$timing;
    }

    public function skillaction($actionplayer, $targetSt, $uId){

    }

    /**
     * 複数回、敵にダメージを与える
     * @param val: ランダムターゲット数
     */
    public function LeaderSkillAction($actionplayer, $targetSt, $uId, $tID, $val){
        if($this->isSkill()){
            $i=0;
            $mes="";
            while($i < $this->arguments){
                $targetId=$this->Randomtarget($targetSt);
                $targetSt[$targetId]->Damaged((int)$actionplayer[$uId]->getparameta()["pow"]/2);
                $i++;
            }
            //$_SESSION["enemySt"]=$targetSt;
        }
    }

    private function Randomtarget($targets){
        //全滅状態をチェック
        $cnt=0;
        foreach ($targets as $t) {
            if($t->isAwaken()) break;
            $cnt++;
        }
        if($cnt >3) return 0;

        $id=0;
        while(true){
            $id = mt_rand(0,3);
            if($targets[$id]->isAwaken()){
                return $id;
                break;
            }
        }
    }

    //コンボ数が偶数
    public function isSkill(){
        global $taregtNotes;
        return  $_SESSION["Comb"] %2===0 && $taregtNotes["judge"] != "MISS" && $taregtNotes["judge"] != "ALWAY";
    }

    public function isSkill_B($val){
        return $this->isFlag;
    }
}
?>