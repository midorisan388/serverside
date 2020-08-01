<?php
    //オプション設定保存
    session_start();

    ini_set('display_errors',"On");
    error_reporting(E_ALL);

    try{
        switch($_POST["option"]){
            case 'audio':
                //オーディオ設定(BGM)
                $_SESSION["AUDIO_VOLUME"]=$_POST["audio_volume"];//音量保持
                
                $res=(int)$audio_vol;
            break;
            case 'se':
                //オーディオ設定(SE)
                $_SESSION["SE_VOLUME"]=$_POST["se_volume"];//音量保持

                $res=(int)$se_vol;
            break;
            default:
                //全体適用(音量全体)
                $audio_vol = (isset($_SESSION["AUDIO_VOLUME"]))?$_SESSION["AUDIO_VOLUME"]:0.1;
                $se_vol = (isset($_SESSION["SE_VOLUME"]))?$_SESSION["SE_VOLUME"]:0.5;

                $res=array(
                    "audio"=>$audio_vol,
                    "se"=>$se_vol
                );
            break;
        };

        //Cookieに保存
        $_COOKIE["AUDIO"] = $audio_vol;
        $_COOKIE["SE"] = $se_vol;

        header('Content-Type: application/json; charset=utf-8');
        $resjson = json_encode( $res,JSON_PRETTY_PRINT );
        echo $resjson;
        
    }catch(PDOExeption $erro){
        echo "次のエラーが発生しました<br>";
        echo $erro->getmessage();
    }
?>