<?php
  session_start();

  ini_set('display_errors',"On");
  error_reporting(E_ALL);
  
  try{
    //ST初期格納
    $modelST = {
        'name' => "",
        'imgurl'=>"",
        'skillname'=>"",
        'lv'=>0,
        'exp'=>0,
        'nexp'=>0,
        'power'=>0,
        'deff'=>0,
        'hp'=>0
    };

    $memberSt=[];
    $enemySt=[];

    $requestData_party;
    $requestData_enemy;
    
    $i=0;
    foreach($requestData_party as $stlist){
        $data = explode(',',$stlist);
        //csvファイルから読み込み
        $memberSt[$i]['name']=$data[0];
        $memberSt[$i]['imgurl']=$data[1];
        $memberSt[$i]['skillname']=$data[2];
        /*--整数値に変換--*/
        $memberSt[$i]['lv']=($data[3]);
        $memberSt[$i]['exp']=$data[4];
        $memberSt[$i]['nexp']=$data[5];
        $memberSt[$i]['power']=$data[6];
        $memberSt[$i]['deff']=$data[7];
        $memberSt[$i]['hp']=$data[8];

        $i++;
    }
  }catch(PDOExeption $erro){
    echo "次のエラーが発生しました<br>";
    echo $erro->getmessage();
}

?>