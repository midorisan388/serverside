<?php
//leaderスキルベース
ini_set('display_errors',"On");
error_reporting(E_ALL);
require_once(ROOT_DIR."\php\Skills\SkillClass.php");

//チュートリアル１用リーダースキル
class SpecialLeaderSkill1 extends BaseLeaderSkill{

    //発動条件など
    protected $isFlag=true;

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
     */
    public $timing=0;

    public function __construct($name,$timing,$discription,$sId,$stetasArray,$userid){
        $this->skillId=$sId;
        $this->userid=$userid;
        $this->discription=$discription;
        $this->arguments=$stetasArray;
        $this->skillname = $name;
        $this->timing=(int)$timing;
    }

    //スキルカウントが貯まらないようにする
    //復帰カウントを+5する
    public function LeaderSkillAction($actionplayer, $targetSt, $uId, $tId, $val){
        $actionplayer[$uId]->Characterparam["skillCount"]=0;
        if(!$actionplayer[$uId]->isAwaken())
            $actionplayer[$uId]->Characterparam["deadcnt"]+=5;

        $_SESSION["partySt"][$uId]=$actionplayer[$uId];
    }

    public function isSkill(){
        return $this->isFlag;
    }

    public function isSkill_B($val){
        return $this->isFlag;
    }
}
?>