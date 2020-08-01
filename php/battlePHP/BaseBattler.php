<?php
require_once(ROOT_DIR."/php/battlePHP/BattleActorStetas.php");
require_once(ROOT_DIR."/php/battlePHP/EnemyBaseClass.php");
require_once( ROOT_DIR."/php/Skills/Require_skillList.php");//スキルファイルリスト

abstract class BaseBattler{
    private $skillid=0;
    private $leaderSkill=0;

    public $characterId=0;//キャラクターID
    public $partyIndex=0;
    public $characterName="";
    public $imgId="";
    public $motion="idle";

    public $Characterparam = array(
        "level"=>1,
        "exp"=>0,
        "type"=> "",
        "skillCount"=>0,
        "currentDamage" => 0,
        "hp"=>0,
        "pow"=>0,
        "def"=>0,
        "mpow"=>0,
        "mental"=>0,
        "stetasList"=>array(),
        "deadcnt"=>0
    );
   
    public $skillParam = null;
    public $leaderSkillParam=null;

    //csvから取得したキャラステータスを取得
    public function __construct($mStArray, $pId, $level, $exp){
        $this->partyIndex=$pId;
        $this->characterId=$mStArray["id"];
        $this->characterName=$mStArray["name"];//表示名
        $this->imgId=$mStArray["imgid"];//画像通し番号

        $this->Characterparam["level"] = $level;
        $this->Characterparam["exp"] = $exp;
        $this->Characterparam["type"]=$mStArray["type"];//バトルタイプ
        $this->Characterparam["skillCount"]=0;//現在のスキルチャージカウント
        $this->Characterparam["currentDamage"]=0;//受けているダメージ
        $this->Characterparam["hp"]=$mStArray["HP"];//HP
        $this->Characterparam["pow"]=$mStArray["pow"];//攻撃力
        $this->Characterparam["def"]=$mStArray["def"];//防御力
        $this->Characterparam["mpow"]=$mStArray["magicpow"];//魔力
        $this->Characterparam["mental"]=$mStArray["mental"];//精神力
        $this->Characterparam["deadcnt"]=0;//復活までの被ダメカウント
        $this->Characterparam["stetasList"] = array();

        $this->leaderSkill=$mStArray["lskillid"];
        $this->skillId=$mStArray["skillid"];

        $this->skillParam = setSkillData($this->skillId,$this->partyIndex);//スキルデータ
    }

    //外部から呼び出し
    public function LeaderSkillInit(){
        $this->leaderSkillParam = setLeaderSkillData($this->leaderSkill,0);//リーダースキル
        return $this->leaderSkillParam;
    }

    /**
     * 被ダメージ処理
     * @param dm :　受けるダメージ量
     */
    public function Damaged($dm){
        $dmgMes = "";
        $deadcntmax=10;

        //既に戦闘不能なら復帰カウント増加
        if($this->Characterparam["currentDamage"] >= $this->Characterparam["hp"]){
            $this->Characterparam["deadcnt"]++;
            $dmgMes =$this->characterName."の復活まであと<strong class=damage>".($deadcntmax-$this->Characterparam["deadcnt"])."</strong><br>";
            //HP30%で復活
            if($deadcntmax <= $this->Characterparam["deadcnt"]){
                $this->Characterparam["currentDamage"] = $this->Characterparam["hp"]*0.7;
                $dmgMes =$this->characterName."は戦闘に復帰した！<br>";
                $this->motion="idle";
                $this->Characterparam["deadcnt"]=0;
            }   
        }else{
            $this->motion="damag";
            $latestDmg=$dm;// - $this->Characterparam["def"];
            $befCurrentDamage=$this->Characterparam["hp"]-$this->Characterparam["currentDamage"];
            if($latestDmg < 0){
                $this->motion="idle";
                $latestDmg=0;
            }

            //ダメージ量が現HPを超える場合、ダメージ量を残HP分にまで制限
            $this->Characterparam["currentDamage"] += $latestDmg;
            if($this->Characterparam["currentDamage"] >= $this->Characterparam["hp"]){
                $latestDmg = $befCurrentDamage;
                $this->Characterparam["currentDamage"] = $this->Characterparam["hp"];
            }

            $dmgMes=$this->characterName."は<strong class=damage>".$latestDmg."</strong>のダメージを受けた<br>";
            //リーダースキル処理(被攻撃時)
            if(isset($_SESSION["leaderSkill"]))
                if($_SESSION["leaderSkill"]->timing === 1){
                   $result = $_SESSION["leaderSkill"]->LeaderSkillAction($_SESSION["partySt"],$_SESSION["enemySt"],$this->partyIndex,0,0);
                    if($result != false){
                        $dmgMes.= $result;
                    }
                }

            //戦闘不能処理
            if(!$this->isAwaken()){
                $dmgMes.=$this->characterName."は戦闘不能になった・・・<br>";
                $this->DeadFunc();
            }
        }
        if($this->Characterparam["currentDamage"] < 0)$this->Characterparam["currentDamage"] = 0;

        return $dmgMes;
    }

    /**
     * 蘇生時処理
     */
    public function Revive($heal){
        $this->motion="idle";
        $this->Characterparam["deadcnt"]=0;
        $this->Characterparam["currentDamage"]=$this->Characterparam["hp"]-$heal;
        if($this->Characterparam["currentDamage"] < 0)$this->Characterparam["currentDamage"] = 0;
    }

     //戦闘不能になった時の処理
    function DeadFunc(){
        $this->motion="dead";
        $this->ResetSkillCnt();
        $this->Characterparam["deadcnt"]=0;
    }

    //スキルの仕様フラグ
    public function checkSkillFlag(){
        return  $this->Characterparam["skillCount"] >= $this->skillParam->skillCharge;
    }

    //攻撃対象選定
    public function targetSet($targets){
        //デフォルトターゲットが生存している
        if(isset($targets[$this->partyIndex]) && $targets[$this->partyIndex]->isAwaken()){
            return $this->partyIndex;
        }else{ //他レーンのターゲットを探す
            while(true){
                $selectId = mt_rand(0,3);
                if($targets[$selectId]->isAwaken() && isset($targets[$selectId]) && $selectId != $this->partyIndex){
                    return $selectId;
                }
            }
        }
    }

    //タイプ判定
    public function BattleTypeCheck($targetType){
        if($this->Characterparam["type"] < $targetType){
            if($this->Characterparam["type"] === 0 && $targetType === 2){
                //ENH > ATK：ターゲットに対して不利な攻撃
                return 0.9;
            }else{
                //ターゲットに対して有利な攻撃
                return 1.1;
            }
        }else{
            //ターゲットに対して不利な攻撃
            return 0.9;
        }
        return 1.0; //タイプ一致
    }

    //スキルカウント増加
    private function AddSkillCnt(){
        $this->Characterparam["skillCount"]++;
    }

    //スキルカウントリセット
    private function ResetSkillCnt(){
        $this->Characterparam["skillCount"]=0;
    }

    //攻撃行動
    public function Attack($party,$target){
        if($this->isAwaken()){
            if($this->checkSkillFlag()){ //スキル使用
                $this->ResetSkillCnt();
               return $this->skillUse($party,$target);
            }else{//通常攻撃
                $this->motion="attack";

                $targetId = $this->targetSet($target);
                $BaseDamage=(int)($this->Characterparam["pow"]*$this->BattleTypeCheck($target[$targetId]->getParameta()["type"])-$target[$targetId]->getParameta()["def"]);
        
                $atkDamageMes = $target[$targetId]->Damaged($BaseDamage);
        
                $actmes = $this->characterName."の攻撃<br>";
                $actmes .= $atkDamageMes;
        
                $this->AddSkillCnt();
                
                return [$party, $target, $actmes];
            }
        }else{
            $this->motion="dead";
        }
    }

    //スキルの使用処理
    private function skillUse($playparty, $targetparty){    
       $returnData = $this->skillParam->skillaction($playparty, $targetparty,$this->partyIndex);
        $this->motion="skill";
       //リーダースキル処理(スキル使用時)
        if(isset($_SESSION["leaderSkill"]))
            if($_SESSION["leaderSkill"]->timing === 4) $_SESSION["leaderSkill"]->LeaderSkillAction($playparty,$targetparty,$this->partyIndex,0,0);

       return $returnData;
    }

    //状態リストを返す
    public function getStetas(){
        return $this->Characterparam["stetasList"];
    }

    //生存フラグを返す
    public function isAwaken(){
        return $this->Characterparam["hp"] > $this->Characterparam["currentDamage"];
    }
    
    //状態を追加する
    public function setStetas($add_stetas){
        array_push($this->Characterparam["stetasList"], $add_stetas);
        return $this->Characterparam["stetasList"];
    }

    //状態を削除する
    public function deleteStetas($remove_stetas){
        array_slice($this->Characterparam["stetasList"], $remove_stetas);
        return $this->Characterparam["stetasList"];
    } 

    //状態の効果時間を調査
    public function stetasCheck(){
        $stId=0;
        //ステータスの効果時間が0以下なら削除
        foreach($this->stetasList as $st){
            if($st->getFuncTurn() <= 0){
                array_slice($this->$stetasList, $stId, 1);//配列から削除
                $stId--;//Idを一つ戻す
            }
            $stId++;
        }
    }

    //ステータスの値を返す
    public function getParameta(){
        return $this->Characterparam;
    }

    //ステータスの値を更新する
    public function setParam($newparam){
        $this->Characterparam = $newparam;
    }
}


//スキルデータ取得
function setSkillData($sid_,$uId){
    $skillArgument=array();
    $searchId =(int)$sid_;
    $skillFileLine = file( ROOT_DIR."/datas/csv/SkillList.csv");
    $skillLineLength = count($skillFileLine);
    $i=0;
    
    foreach( $skillFileLine as $skillLine){
        $sdata=explode(',', $skillLine);
        if($i > 0){//最初の行は飛ばす
            if((int)$sdata[0] === (int)$searchId){//スキルデータレコードにヒット
                 $skillData = $sdata;
 
                for($n=5; isset($skillData[$n]); $n++){
                    array_push($skillArgument, $skillData[$n]);//ステータス引数配列作成
                }
                require_once( ROOT_DIR."/php/Skills/".$skillData[2].".php");//該当クラスファイル読み込み
                return new $skillData[2]($skillData[1],$skillData[4],$searchId,$skillArgument,$uId);
            }
        }
        $i++;
    }
     //echo "見つかりませんでした<br>";
     return null;
}

//リーダースキル取得
function setLeaderSkillData($sid_,$uId){
    $skillArgument=array();
    $searchId =(int)$sid_;
    $skillFileLine = file( ROOT_DIR."/datas/csv/LeaderSkillList..csv");
    $i=0;
    
    foreach( $skillFileLine as $skillLine){
        $sdata=explode(',', $skillLine);
        if($i > 0){//最初の行は飛ばす
            if((int)$sdata[0] === (int)$searchId){//スキルデータレコードにヒット 
                for($n=5; isset($sdata[$n]); $n++){
                    array_push($skillArgument, $sdata[$n]);//ステータス引数配列作成
                }
                require_once( ROOT_DIR."/php/LeaderSkills/".$sdata[2].".php");//該当クラスファイル読み込み
                return new $sdata[2]($sdata[1],$sdata[4],$sdata[3],$searchId,$skillArgument,$uId);
            }
        }
        $i++;
    }
     return null;
}
?>