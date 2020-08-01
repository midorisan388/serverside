<?php
    session_start();

    ini_set('display_errors',"On");
    error_reporting(E_ALL);


    $seliflist = [];
    $selectlist =[];

    try{
        //$filename = $_POST['file'];
        $filename="../csv/event-selif-test.csv";
        $lines = file($filename);

        $i=0;$n=0;

        foreach($lines as $line){
                $data = explode(',',$line);

                $seliflist[$i]["owner"] = $data[0];
                $seliflist[$i]["id"] = $data[1];
                $seliflist[$i]["nextline"] = $data[2];
                $seliflist[$i]["selif"] = $data[3];
                $seliflist[$i]["faceurl"]=$data[4];
               

                if($data[0] === "@@"){
                    $selectlist[$data[1]][$n]["selecttxt"]=$data[3];
                    $selectlist[$data[1]][$n]["nextline"]=$data[2];
                    $n++;
                }else{
                    $n=0;
                }

            $i++;
        }
        
    }catch(PDOExeption $erro){
        echo "次のエラーが発生しました<br>";
        echo $erro->getmessage();
    }

?>
