<?php
//バフ計算　デバフ
    ini_set('display_errors',"On");
    error_reporting(E_ALL);

    try{
        //攻撃上昇量計算    攻撃者のバフデバフリスト受け取り
        function PowerCol($plySt){
            $total=0;//攻撃力影響量
            $pow_up_total=0;//攻撃力上昇量合計
            $pow_down_total=0;//攻撃力減少量合計
            $stId=0;//何番目のステータスを参照しているか格納
            //クラスの選別
            foreach($plySt as  $st){
                if(get_class($st) === "PowerUp"){ //攻撃力上昇バフ　
                   $pow_up_total = $st->stetasfunc($pow_up_total);//上昇量追加したものを格納
                   $st->updateTurn(-1);//効果時間減少
                }else if(get_class($st) === "PowerDown"){//攻撃力減少デバフ　
                    $pow_down_total = $st->stetasfunc($pow_down_total);//減少量追加したものを格納
                    $st->updateTurn(-1);//効果時間減少
                }
                $stId++;
            }

            $total = $pow_up_total+$pow_down_total;//攻撃力影響量合計
            return $total;//攻撃力上昇量返す
        }

        //防御力上昇量計算 対象者のバフデバフリスト受け取り
        function DefenseCol($targetSt){
            $total=0;//防御力影響量
            $def_up_total=0;//防御力上昇量合計
            $def_down_total=0;//防御力減少量合計

            //クラスの選別
            foreach($plySt as  $st){
                if(get_class($st) === "DefenseUp"){ //防御力上昇バフ　
                   $def_up_total = $st->stetasfunc($def_up_total);//上昇量追加したものを格納
                   $st->Buffturn -=1;//効果時間減少
                }else if(get_class($st) === "DefenseDown"){//防御力減少デバフ　
                    $def_down_total = $st->stetasfunc($def_down_total);//減少量追加したものを格納
                    $st->Buffturn -=1;//効果時間減少
                }
            }

            $total = $def_up_total+$def_down_total;//防御力影響量合計
            return $total;//防御力上昇量返す
        }

    }catch(PDOExeption $erro){
        echo "次のエラーが発生しました<br>";
        echo $erro->getmessage();
    }

?>