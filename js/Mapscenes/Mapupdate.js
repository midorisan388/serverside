var
    player,
    mapdata,

    input_key_buffer = new Array(),
    canvas_main,
    ctx_main,
    width_main,
    height_main,


    //マップ読み込み時の初期位置
    start_X,
    start_Y,
    //MAP_DATAの配列の大きさ
  //  map_img = new Image(),//マップ画像
    MAP_SIZE_X = 40,
    MAP_SIZE_Y=40,

    //MAPの座標
    mx =-795,//-(MAP_Width/2),
    my =257,// (MAP_Height/2),

    //プレイヤーからの相対的移動量
    Move_x=16,
    Move_y=8,

    //マップ上絵の移動量
    move_map_x=CHARACHIP_X/2,
    move_map_y=CHARACHIP_Y/4,

    MAP_DATA=[],//ID=0:不可侵　1:移動可能
    AREA_DATA=[],//エリアid検出用
    area_startx,area_endy,area_starty,area_endy,//プレイヤー中心の描画検出範囲
    x,y,
    Zbuffer=[],//描画順を調べるために使用
    zx,zy,startclear=false,
    area_lenght=6,
    currentarea=0;

    function CameraMove(_mx,_my){
      //カメラ(マップ)移動
        mx += -_mx;
        my += - _my;
        walk=true;
        t=40;
    }

function KeyGet(){
      document.onkeydown = function (e){
        if(!e) e = window.event; // レガシー

        input_key_buffer[e.keyCode] = true;
        //MAP_DATA上の操作とマップ画像上の操作
         player.Move(e.keyCode);

        Test(e);//デバッグ用
      };
    }

function stetasInit(){
    //マップ設定
    mapdata = map1;//マップリストからマップデータオブジェクトを受け取る
    area_lenght=mapdata.Arealength;
    getCSV("datas/areaid.csv",1);
    getCSV(mapdata.mapcsv,0);

    for(var i=0;i<MAP_SIZE_Y;i++){
      AREA_DATA[i]=[];
      for (var n=0;n<MAP_SIZE_X;n++) {
          for(var a=0;a<area_lenght;a++){
            if(n >= mapdata.Area_data[a][0][0] && n<=  mapdata.Area_data[a][0][1] && i >=  mapdata.Area_data[a][1][0] && i<=  mapdata.Area_data[a][1][1]){
              AREA_DATA[i][n]=a;//どこのエリアIDに属するかふりわけ
              break;
            }else {
              AREA_DATA[i][n]=area_lenght-1;
            }
          }
      }
    }

    //スタートcellと座標を自動調整//
    if(mapdata.start_sel[0]>0){//右移動計算
        mx = (mx - move_map_x*mapdata.start_sel[0]);
        my=(my - move_map_y*mapdata.start_sel[0]);
    }
    if(mapdata.start_sel[1]>0){//下移動計算
        mx = (mx + move_map_x*mapdata.start_sel[1]);
        my=(my - move_map_y*mapdata.start_sel[1]);
    }

  //NPC設定//////////////////////////////////////////////////////
    mapdata.npcobj.posSetting();
    // プレイヤー設定//////////////////////////////////////////////////////////////
    player = Player.GetPlayer();//プレイヤーオブジェクト受け渡し

    //キャラ画像をキャンバス中央に設置
    start_X=canvas_main.width/2 - CHARACHIP_X/2;
    start_Y=canvas_main.height/2- CHARACHIP_Y;


    player.offset_x=start_X;
    player.offset_y=start_Y;
    //MAP_DATA上の数値
    player.ply_sel_x=mapdata.start_sel[0];
    player.ply_sel_y=mapdata.start_sel[1];

    startclear=true;

  }

function mapmain() {
      canvas_main = document.getElementById("main");
      //canvassize

      width_main = screen_w;
      height_main =screen_h;


      if (width_main >= screen_w) {
          width_main = screen_w;
          width_sub = screen_h;
      }

      canvas_main.width = width_main;
      canvas_main.height = height_main;

      ctx_main = canvas_main.getContext("2d");


}

function mapupdata() {
  KeyGet();
  mapdata.npcobj.update();
  player.updata();
  currentarea=AREA_DATA[player.ply_sel_y][player.ply_sel_x];
}

function maprender() {//描画
  //ctx_main.fillRect(0,0,width_main,height_main);

  MAP_test.draw(ctx_main,mx,my);

  //Map_Brines[currentarea].draw(ctx_main,mx,my);

  //  MAP_wall[currentarea].draw(ctx_main,mx,my);
   area_startx=player.ply_sel_x-15; if(area_startx<0)area_startx=0;
   area_starty=player.ply_sel_y-15; if(area_starty<0)area_starty=0;
   area_endx=player.ply_sel_x+15; if(area_endx>=MAP_SIZE_X)area_endx=MAP_SIZE_X-1;
   area_endy=player.ply_sel_y+15; if(area_endy>=MAP_SIZE_Y)area_endy=MAP_SIZE_Y-1;

  if(mapdata.npcobj.idx>=area_startx && mapdata.npcobj.idx<=area_endx && mapdata.npcobj.idy>=area_starty && mapdata.npcobj.idy <= area_endy){
        if(player.ply_sel_y>mapdata.npcobj.idy){
            mapdata.npcobj.npcrender();
            player.render(ctx_main);
        }else{
          if(player.ply_sel_x>mapdata.npcobj.idx){
            mapdata.npcobj.npcrender();
            player.render(ctx_main);
          }else {
            player.render(ctx_main);
            mapdata.npcobj.npcrender();
          }
        }
    }else {
      player.render(ctx_main);
    }
}
