var
  CHARACHIP_X=64,
  CHARACHIP_Y=64,
  MAP_Width = 2560,
  MAP_Height = 1280,
  MAP_test,
  Player,
  Map_Brines=[],
  MAP_wall=[],
  ply_anim=[1,0,2,1],

  map_brin =[],//エリア外の塗りつぶし用
  map_wall_img=[],
  ply_img = new Image(),
  mapimg_=new Image();


function initSprites() {
    ply_img.src="img/player.jpg";
    mapimg_.src=mapdata.map_img_uri;

     for(var i=0;i<area_lenght;i++){
        map_brin[i]=new Image();
        map_wall_img[i]=new Image();
        map_wall_img[i].src=mapdata.map_img_wall[i];
        map_brin[i].src=mapdata.bringimg_uri[i];
        MAP_wall[i]=new Sprite(map_wall_img[i],0,0,MAP_Width,MAP_Height);
        Map_Brines[i] = new Sprite(map_brin[i],0,0,MAP_Width,MAP_Height);
    }

    MAP_test=new Sprite(mapimg_,0,0,MAP_Width,MAP_Height);
    Player = [
      ///////////////////////0////////////////////////////////////1////////////////////////////////////////////////////2/////////////////////////////////////////////
      [new Sprite(ply_img,0,0,CHARACHIP_X,CHARACHIP_Y),new Sprite(ply_img,CHARACHIP_X,0,CHARACHIP_X,CHARACHIP_Y),new Sprite(ply_img,CHARACHIP_X*2,0,CHARACHIP_X,CHARACHIP_Y)],
      [new Sprite(ply_img,0,CHARACHIP_Y,CHARACHIP_X,CHARACHIP_Y),new Sprite(ply_img,CHARACHIP_X,CHARACHIP_Y,CHARACHIP_X,CHARACHIP_Y),new Sprite(ply_img,CHARACHIP_X*2,CHARACHIP_Y,CHARACHIP_X,CHARACHIP_Y)],
      [new Sprite(ply_img,0,CHARACHIP_Y*2,CHARACHIP_X,CHARACHIP_Y),new Sprite(ply_img,CHARACHIP_X,CHARACHIP_Y*2,CHARACHIP_X,CHARACHIP_Y),new Sprite(ply_img,CHARACHIP_X*2,CHARACHIP_Y*2,CHARACHIP_X,CHARACHIP_Y)],
      [new Sprite(ply_img,0,CHARACHIP_Y*3,CHARACHIP_X,CHARACHIP_Y),new Sprite(ply_img,CHARACHIP_X,CHARACHIP_Y*3,CHARACHIP_X,CHARACHIP_Y),new Sprite(ply_img,CHARACHIP_X*2,CHARACHIP_Y*3,CHARACHIP_X,CHARACHIP_Y)]
    ];
  }
