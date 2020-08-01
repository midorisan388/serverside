var Player = {

  //歩行アニメ
  anim_id:0,
  walk:false,
  walk_time:0,
  walk_count:8,

  ply_sel_x:0,
  ply_sel_y:0,
  ply_sel_z:0,
  ply_rotat:0,

  //固定
  offset_x:160,
  offset_y:168,

  GetPlayer:function(){//プレーヤーオブジェクトを渡す
    return this;
  },

    Move:function(keyId){
        var move_bool;
      switch (keyId) {
        case 37://左
          if(MapIDcatch(MAP_DATA[this.ply_sel_y][this.ply_sel_x-1]) && this.ply_sel_x > 0){
            //_mapid=MAP_DATA[this.ply_sel_y][this.ply_sel_x-1];
            this.ply_sel_x--;
            this.ply_rotat=1;
            Move_x=-move_map_x;
            Move_y=-move_map_y;

            move_bool=true;
          }
          break;
        case 38://上
          if(this.ply_sel_y > 0 && MapIDcatch(MAP_DATA[this.ply_sel_y-1][this.ply_sel_x])){
            _mapid=MAP_DATA[this.ply_sel_y-1][this.ply_sel_x];

            this.ply_sel_y--;

            this.ply_rotat=3;
            Move_x=move_map_x;
            Move_y=-move_map_y;

            move_bool=true;
          }
          break;
        case 39://右
            if(this.ply_sel_x < MAP_SIZE_X-1 && MapIDcatch(MAP_DATA[this.ply_sel_y][this.ply_sel_x+1])){
              _mapid=MAP_DATA[this.ply_sel_y][this.ply_sel_x+1];
              this.ply_sel_x++;

              this.ply_rotat=2;
              Move_x=move_map_x;
              Move_y=move_map_y;

              move_bool=true;
            }
          break;
        case 40://下
            if(this.ply_sel_y<MAP_SIZE_Y-1 && MapIDcatch(MAP_DATA[this.ply_sel_y+1][this.ply_sel_x])){
              _mapid=MAP_DATA[this.ply_sel_y+1][this.ply_sel_x];
              this.ply_sel_y++;

              this.ply_rotat=0;
              Move_x=-move_map_x;
              Move_y=move_map_y;

              move_bool=true;

            }
          break;
        default:
          move_bool=false;
        break;
      }
      if(move_bool) this.Movedraw(Move_x,Move_y);
    },

    Movedraw:function(_mx,_my){
      //カメラ(マップ)移動
      //通常はマップ画像だけ移動させる
        MAP_DATA[this.ply_sel_y][this.ply_sel_x]=0;
        this.walk=true;
        this.walk_time=this.walk_count*4;
        mx += -_mx;
        my += - _my;
        MAP_DATA[this.ply_sel_y][this.ply_sel_x]=1;
        mapdata.npcobj.walk(-_mx,-_my);
        this.ply_sel_Z=Zbuffer[this.ply_sel_y][this.ply_sel_x];
        inputtimer=0;
        keyId=0;
        //NPC座標も変更
    },

    updata:function(){

      this.px=this.offset_x;
      this.py=this.offset_y;

        if(this.Walking() == false){
          this.anim_id=0;
        }
        this.walk_time--;
    },

    render:function(ctx){
      Player[this.ply_rotat][ply_anim[this.anim_id]].draw(ctx,this.offset_x,this.offset_y);
    },

    Walking:function(){
      if(this.walk_time>0){
        if(this.walk_time%this.walk_count==0){
          this.anim_id++;
          if(this.anim_id>=3)this.anim_id=0;
        }
        return true;
      }
        return false;
    }
};
