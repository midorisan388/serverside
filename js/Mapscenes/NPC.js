
var npctimer=300,


NPC = function(img,idx,idy,name){
  this.name=name;
  this.img=new Image();
  this.img.src=img;
  this.name=name;

  this.idx=idx;
  this.idy=idy;
  this.pz=0;

  this.rotat=0;
  this.sprite=new Sprite(this.img,0,0,CHARACHIP_X,CHARACHIP_Y);
  this.nowmapid_stetas=1;

}

NPC.prototype.posSetting = function(){
  this.px=((mx+MAP_Width/2)+move_map_x*(this.idx-this.idy))-CHARACHIP_X/2;//(mx + MAP_Width/2 - CHARACHIP_X/2)+(this.idx-this.idy)*move_map_x;
  this.py=(my+move_map_y*(this.idx+this.idy)-CHARACHIP_Y/2)-5;//(-my-96-CHARACHIP_Y)+(this.idx+this.idy)*move_map_y;
}

NPC.prototype.walk=function(p_movex,p_movey){//表示座標更新
  this.px += p_movex;
  this.py+=p_movey;
}

NPC.prototype.npcrender=function(){
  this.sprite.draw(ctx_main,this.px,this.py);
}

NPC.prototype.update=function(){
  //移動時の条件計算
  if(npctimer>0){
    npctimer--;
  }else if(npctimer==0){
    var rotatid = Math.floor(Math.random () * 3) + 0;
   switch (rotatid) {
    case 0://左
      if(!(this.idx>0))break;
        if(MapIDcatch(MAP_DATA[this.idy][this.idx-1])){
          this.UpdateID(-1,0);
          this.walk(-move_map_x,-move_map_y);
          this.rotat=1;
          move_bool=true;
        }
       break;
    case 1://上
    if(!(this.idy>0))break;
        if(MapIDcatch(MAP_DATA[this.idy-1][this.idx])){
          this.UpdateID(0,-1);
          this.walk(move_map_x,-move_map_y);
          this.rotat=3;
          move_bool=true;
        }
     break;
    case 2://右
      if(MapIDcatch(MAP_DATA[this.idy][this.idx+1]) && this.idx > MAP_SIZE_X-1){
        this.UpdateID(1,0);
        this.walk(move_map_x,move_map_y);
        this.rotat=2;
        move_bool=true;
      }
     break;
    case 3://下
        if(MapIDcatch(MAP_DATA[this.idy+1][this.idx]) && this.idy < MAP_SIZE_Y-1){
          this.UpdateID(0,1);
          this.walk(-move_map_x,move_map_y);
          this.rotat=0;
          move_bool=true;
        }
     break
    default:      move_bool=false; break;
   }
   nowmapid_stetas=MAP_DATA[this.idy][this.idx];//本来のidを保存
   npctimer=300;
 }
}

NPC.prototype.UpdateID=function(x,y){
  MAP_DATA[this.idy][this.idx]=1;//this.nowmapid_stetas;//id状態をもとに戻す
  this.idx += x;
  this.idy += y;
  this.pz = Zbuffer[this.idy][this.idx];

  MAP_DATA[this.idy][this.idx]=0;//座標更新後id変化
  this.pz = Zbuffer[this.idy][this.idx];
}

function MapIDcatch(mapid){
  if(mapid==0) return false;
  else if(mapid>=1){
     return true;
  }
}
