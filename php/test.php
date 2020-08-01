<?php

ini_set('display_errors',"On");
error_reporting(E_ALL);

//JSONデータを取得
    $Json = file_get_contents("../datas/Notesfile/Notestimingdatatest.json");
    $Json = mb_convert_encoding($Json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    printf($Json."<br>");

    $Array = json_decode($Json,true);

    $a = $Array;

    $i=0;
    foreach ($a as $s) {
        if($s["timing"] <= 10){
            echo $s["timing"]."<br>";
            $a[$i]["timing"] += 1.00;
            echo  $a[$i]["timing"]."<br>";
        }
        $i++;
    }
    var_dump($a[0]);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>test</title>
</head>
<body>
    <script>
        let notes=[];

        const notesdata_json = JSON.parse(<?php echo $Json ?>);
        console.log(notesdata_json);

        var i=0;
        notesdata_json.forEach((notesdata) => {
            notes[i] = {
                timing:notesdata['timing'],//タイミング時間
                lernID:notesdata['lernID'],//レーンID
                type:notesdata['type'],//ノーツタイプ
                status: "Awaken",
                judge: "ALWAY",//状態初期化
                hanteiT: 0.00
            };
            i++;
        });

        console.table(notes);
    </script>
</body>
</html>