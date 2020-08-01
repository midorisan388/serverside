//CSVファイルを読み込む関数getCSV()の定義
function getCSV(csv,mapping){
    var req = new XMLHttpRequest(); // HTTPでファイルを読み込むためのXMLHttpRrequestオブジェクトを生成
    req.open("get", csv, true); // アクセスするファイルを指定
    req.send(null); // HTTPリクエストの発行

    // レスポンスが返ってきたらconvertCSVtoArray()を呼ぶ

    req.onload = function(){
      if(mapping==0)
	     convertCSVtoArray(req.responseText); // 渡されるのは読み込んだCSVデータ
      else
        convertCSVZbuffe(req.responseText);
    }
}

// 読み込んだCSVデータを二次元配列に変換する関数convertCSVtoArray()の定義
function convertCSVtoArray(str){ // 読み込んだCSVデータが文字列として渡される
    var result = []; // 最終的な二次元配列を入れるための配列
    var tmp = str.split("\n"); // 改行を区切り文字として行を要素とした配列を生成

    // 各行ごとにカンマで区切った文字列を要素とした二次元配列を生成
    for(var i=0;i<40;i++){
      MAP_DATA[i]=[];
        result[i] = tmp[i].split(',');
        MAP_DATA[i]=result[i];
    }
}

//描画の順番ID
function convertCSVZbuffe(str){
  var result = [];
  var tmp = str.split("\n");

  for(var i=0;i<40;i++){
    Zbuffer[i]=[];
    result[i]=tmp[i].split(',');

    Zbuffer[i]=result[i];
  }
  startclear=true;

}
