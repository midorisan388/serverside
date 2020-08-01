<?php
ini_set('display_errors',"On");
error_reporting(E_ALL);

require_once(ROOT_DIR."\php\battlePHP\BaseBattler.php");
require_once( ROOT_DIR."\php\getcharacterlist.php");
require_once(ROOT_DIR."\php\battlePHP\BattleActorStetas.php");

//敵ベースクラス
class BaseEnemyClass extends BaseBattler{
    public $partyIndex=0;
    public $characterId=0;//キャラクターID
    public $characterName="";
    public $imgId="";

    public $Characterparam = array(
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

    public function __construct( $mStArray, $partyIndex){
        $this->partyIndex=$partyIndex;
        $this->characterId=$mStArray["id"];//パーティ配置配置ID
        $this->characterName=$mStArray["name"];//表示名
        $this->imgId=$mStArray["imgid"];//画像通し番号

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

        $this->skillParam = setSkillData($mStArray["skillid"],$this->characterId);//スキルデータ
    }

    //スキルの使用処理
    public function skillUse($enemyparty, $targetparty ){    
        $returnData = $this->skillParam->skillaction($_SESSION["enemySt"], $_SESSION["partySt"],$this->partyIndex);
 
        $this->Characterparam["skillCount"]=0;
        return $returnData;
    }

    public function Damaged($dm){
        $dmgMes = "";
        $deadcntmax=10;

        //戦闘不能時、後衛のエネミーと交代する
        if($this->Characterparam["currentDamage"] < $this->Characterparam["hp"]){
            $dm -= $this->Characterparam["def"];
            $this->Characterparam["currentDamage"] += (int)$dm;
            $dmgMes=$this->characterName."は<strong class=damage>".$dm."</strong>のダメージを受けた<br>";
            if($this->Characterparam["currentDamage"] >= $this->Characterparam["hp"]){
                $this->Characterparam["currentDamage"] = $this->Characterparam["hp"];
                $dmgMes .= $this->characterName."は倒れた！";
                $this->ChangeEnemyList();
            }
            if($this->Characterparam["currentDamage"] < 0)$this->Characterparam["currentDamage"] = 0;
        }
        return $dmgMes;
    }

    /**
     * 敵の後衛から投入
     */
    function ChangeEnemyList(){
        for($i=4; $i < count($_SESSION["enemyStMst"]); $i++){
            $deadEnemy = $this;//$_SESSION["enemyStMst"][$this->characterId];
            if(isset($_SESSION["enemyStMst"][$i]) && $_SESSION["enemyStMst"][$i]->isAwaken()){
                //前衛に移動
                $_SESSION["enemySt"][$this->characterId] = $_SESSION["enemyStMst"][$i];
                //リスト全体更新
                $_SESSION["enemyStMst"][$this->characterId] = $_SESSION["enemyStMst"][$i];
                $_SESSION["enemyStMst"][$i] = $deadEnemy;

                //交代フラグを立てる
                $_SESSION["enemyChange"]=true;
                break;
            }
        }
    }
}
?>