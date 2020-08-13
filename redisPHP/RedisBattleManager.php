<?php

class RedisBattleManager{

    private $redis;
    private $sql;

    private $partyDataRedis;
    private $enemyDataRedis;

    private $partyDataObject;
    private $enemyDataObject;

    private $nextNotesId=0;
    private $notesData;
    private $battleState="Continue";

    private static $baseParamTemp=array(
        "characterId"=>1,
        "imgId"=>00000,
        "name"=>"",
        "health"=>0,
        "maxHealth"=>0,
        "power"=>0,
        "mental"=>0,
        "raceId"=>0,
        "typeId"=>0,
        "jobId"=>0,
        "weponId"=>0,
        "skillId"=>1,
        "leaderSkillId"=>1
    );

    private static $skillParamTemp=array(
        "skillClass"=>"",
        "skillId"=>1,
        "skillName"=>"",
        "discription"=>"",
        "timing"=>"action",
        "maxCnt"=>0,
        "args"=>[]
    );

    public function __construct(){
        //Redis接続---------------------------------
        $addr = "127.0.0.1";
        $port = 6379;
        $this->redis = new Redis();
        $this->redis->connect($addr,$port);
        //------------------------------------------

       //SQL接続-----------------------------------------------------------------
        require("datas/sql.php");
        $this->sql=new PDO("mysql:host=$SERV;dbname=$GAME_DBNAME",$USER,$PASSWORD);
        $this->sql->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
        $this->sql-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //----------------------------------------------------------------------

        $this->InitStetas($_SESSION["partyIds"]);



    }

    /**
     * @param partIds:パーティに編成されているキャラクターIDリスト
     */
    private function InitStetas($partyIds){
        if($this->redis->exists($_SESSION["user_id"]."_playerDatas")){
            $this->partyDataRedis = $this->redis->get($_SESSION["user_id"]."_playerDatas");
        }else{
            //csvからでーたしゅとく
            $characterCsvPath="/datas/csv/CharactersStetas.csv";
            $skillCsvPath="/datas/csv/SkillList.csv";
            $leaderSkillCsvPath="/datas/csv/LeaderSkillList.csv";
            for($i=0;$i<4;$i++){
                if($partyIds[$i] > 0){
                //ベースステータス設定
                    $characterData=getRecord($partyIds[$i],$characterCsvPath);
                    $setParam = $this->baseParamTemp;
                    $setParam["characterId"]=$partyIds[$i];
                    $setParam["imgId"]=$characterData[1];
                    $setParam["name"]=$characterData[2];
                    $setParam["health"]=$characterData[15];
                    $setParam["maxHealth"]=$characterData[15];
                    $setParam["power"]=$characterData[16];
                    $setParam["mental"]=$characterData[19];
                    $setParam["raceId"]=$characterData[11];
                    $setParam["typeId"]=$characterData[13];
                    $setParam["jobId"]=$characterData[14];
                    //$setParam["weponId"]=$characterData[2];
                    $setParam["skillId"]=$characterData[21];
                    $setParam["leaderSkillId"]=$characterData[22];
                //スキル設定
                    $setSkill = InitSkillData("skillId",$setParam["skillId"],$skillCsvPath);
                //リーダースキル設定
                    $setSkill = InitSkillData("leaderSkillId",$setParam["leaderSkillId"],$skillCsvPath);
                }
            }
        }

        if($this->redis->exists($_SESSION["user_id"]."_enemyDatas")){
            $this->enemyDataRedis = $this->redis->get($_SESSION["user_id"]."_enemyDatas"); //全データ取得
        }else{

        }

        
    }

    private function InitSkillData($skillType,$id,$csvPath){
        $skillData = getRecord($setParam["skillId"],$skillCsvPath);
        $setSkill = $this->skillParamTemp;
        $setSkill["skillClass"]=$skillData[2];
        $setSkill["skillId"]=$id;
        $setSkill["skillName"]=$skillData[1];
        $setSkill["discription"]=$skillData[3];
        if($skillType==="leaderSkillId"){
            $setSkill["timing"]=$skillData[4];
            $setSkill["maxCnt"]=0;
        }else if($skillType==="skillId"){
            $setSkill["timing"]=0; //アクションスキル
            $setSkill["maxCnt"]= $skillData[4];
        }
        //スキル設定-パラメータ設定
        $setSkill["args"]=array();
        for($n = 5; $n < $skillData.length; $n++){
            array_push($setSkill["args"],$skillData[$n]);
        }

        return $skillData;
    }

    private function InitNotesData(){
        if($this->redis->exists($_SESSION["user_id"]."_battleState")){
            //ノーツ、判定、音楽データ取得
            $questSt = $_SESSION["questData"][0];
            $musicSt = $_SESSION["questData"][1];
            $_SESSION["illegalNotes"]=$illegalNotesIds=[];
            
            $notesUrl =$musicSt["notesFilePath"];
            $audiotext = [$musicSt["audioFilePath"],$musicSt["musictitle"]];//audioファイルのパスと曲名
            $titletext= $questSt["title"];//クエスト名
            
                if(file_exists($notesUrl)){
                    //ノーツデータリストの生成
                    $notesjson = file_get_contents($notesUrl);
                    $notesjson = mb_convert_encoding($notesjson, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
            
                    //イベント用特殊判定ノーツリストの生成
                    $illegalNotesIds = CreateIllegalNotesList($eventQuestData,$notesjson);
            
                    $this->redis->set("illegalNotesIds",json_encode($illegalNotesIds));
                }
            
        }
    }

    private function AddNextNotesId(){
        $this->nextNotesId++;
        if($this->nextNotesId >=count($this->notesData)){
            $this->nextNotesId=count($this->notesData);
            $this->battleState="Consert";
        }
    }
    private function NotesCalc(){

    }

    public function Action(){

    }
}
?>