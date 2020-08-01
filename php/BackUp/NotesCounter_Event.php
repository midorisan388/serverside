<?php
ini_set('display_errors',"On");
error_reporting(E_ALL);
require_once(ROOT_DIR."\php\NotesGlobal.php");

//判定ノーツ共有用グローバル
try{
    /**
     * ノーツデータ更新
     * -----------------------------
     * @param gametime: 経過時間
     * @param notesdata: 全ノーツ情報
     * @param id: 判定レーンID
     * @param notesSt: 失敗判定で呼ばれたかユーザ判定で呼ばれたか
     * @param notesId: 失敗判定で呼ばれた時のノーツ番号
     */
    function NotesCol($gametime,$notesdata,$id, $notesSt, $notesId){
        global $taregtNotes;
        

        $timingZone=array(//タイミング許容範囲と追加SCORE
            "BAD"=>array(
                "judget"=>0.42,
                "upScore"=>0
            ),
            "GOOD"=>array(
                "judget"=>0.38,
                "upScore"=>100
            ),
            "GREAT"=>array(
                "judget"=>0.25,
                "upScore"=>300
            ),
            "PARF"=>array(
                "judget"=>0.06,
                "upScore"=>700
            )
        );

        
        //初期化
        $taregtNotes["judge"]="ALWAY";
        $hanteicount = array("MISS"=>0,"BAD"=>0,"GOOD"=>0,"GREAT"=>0,"PARF"=>0);//判定データ
        $score=(!isset($_SESSION["Score"]))?0:$_SESSION["Score"];//スコアデータセッション
        $combo=0;

        $battleNotesdatas = $notesdata;
        if(isset($battleNotesdatas)){
         $noteslength = count($battleNotesdatas);//要素数取得
        }else $noteslength=0;
        
        //if($notesSt != "miss"){
            //タイミングの計算
            $count = 0;
            foreach($battleNotesdatas as $battleNotes){
                if($battleNotes["judge"]==="ALWAY"){ //未処理のノーツから検査
                    if((int)$battleNotes["lernID"] === $id){
                        $timejudge = $gametime-$battleNotes["timing"];
                       
                        if( $battleNotes["timing"]-$gametime > $timingZone["BAD"]["judget"]){ //判定時間外
                            //$battleNotesdatas[$count]["judge"] = "ALWAY";
                            break;
                        }
                        $isIllegal=CheckIllegalNotes($count);
                            
                            //$battleNotesdatas[$count]["hanteiT"]=$gametime;        
                        $borderName=["PARF","GREAT","GOOD","BAD"];
                        $comp=false;
                        for($i=0; $i<5; $i++){
                            if($isIllegal===false){ //通常判定
                                if($i >= 4){//MISS判定
                                    if($notesId >= 0 ) $setId=$notesId;
                                    else $setId=$count;

                                    $battleNotesdatas[$setId]["judge"] = "MISS";
                                    $taregtNotes=$battleNotesdatas[$setId];
                                }else if(abs($timejudge) <= $timingZone[$borderName[$i]]["judget"]){    
                                    $battleNotesdatas[$count]["judge"] = $borderName[$i];
                                    $score +=$timingZone[$borderName[$i]]["upScore"];
                                    $taregtNotes=$battleNotesdatas[$count];
                                }
                            }else{
                                $battleNotesdatas[$count]["judge"] = $isIllegal;
                                if($isIllegal[0] != "MISS") $score +=$timingZone[$isIllegal]["upScore"];

                                $taregtNotes=$battleNotesdatas[$count];
                            }            
                            $comp=true;
                            break;
                        }
                        if($comp)break;
                    }
                }
                $count++;
            }
        //}else if($notesSt === "miss"){//失敗判定で呼ばれていたらmissを返す
        //    $battleNotesdatas[$notesId]["judge"] = "MISS";
        //    $taregtNotes=$battleNotesdatas[$notesId];
        //}
        
        //コンボ数の更新
        $comboDt=CntComb($battleNotesdatas,$hanteicount);
        $_SESSION["Comb"] = $comboDt[0];
        $_SESSION["pointCnt"]=$comboDt[1];

        UpdateComboData($id);
        
        $_SESSION["Score"] =  $score;
        $_SESSION["notesdata"]=$battleNotesdatas;//ノーツデータ更新

        //リーダースキル処理（ノーツ判定時）
        if(isset($_SESSION["leaderSkill"]))
            if($_SESSION["leaderSkill"]->timing === 10)
                $_SESSION["leaderSkill"]->LeaderSkillAction($_SESSION["partySt"],$_SESSION["enemySt"],$id,0,0);
             
        $resdata =array(
            "score"=>$score,
            "combo"=>$combo,
            "notesdatas"=> $taregtNotes,//判定したノーツのデータ
            "hanteilist"=>$hanteicount//成績データ
        );

        $resdata = mb_convert_encoding($resdata, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

        return $resdata;
    }

    function CheckIllegalNotes($id){
        //if($_SESSION["illegalNotes"]===[]) return false;

        foreach($_SESSION["illegalNotes"] as $data) {
            if($data[1]===$id) return $data[0];
        }
        return false;
    }

    function CntComb($notes,$hantei){
        $combo=0;

        foreach($notes as $battleNotes){
            if( $battleNotes["judge"] === "ALWAY") break;

           $hantei[$battleNotes["judge"]]++;
            if($battleNotes["judge"] === "MISS"){
                $combo=0;
            }else{
                $combo++;
            }
        }

        return [$combo,$hantei];
    }

    function UpdateComboData($id){
        if($_SESSION["Comb"] > $_SESSION["maxComb"]){
            $_SESSION["maxComb"]= $_SESSION["Comb"];
            //リーダースキル（コンボ数更新時）
            if(isset($_SESSION["leaderSkill"]))
                if($_SESSION["leaderSkill"]->timing === 15)
                    $_SESSION["leaderSkill"]->LeaderSkillAction($_SESSION["partySt"],$_SESSION["enemySt"],$id,0,0);
        }
    }
}catch(PDOExeption $erro){
    echo "次のエラーが発生しました<br>";
    echo $erro->getmessage();
}


?>