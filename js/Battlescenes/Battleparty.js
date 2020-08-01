var i=0;
const 
  partylength=4,
  positionY=45,
  positiondist=20;

  let
  playerRenderStetas=[
    {
      y:positionY,
      name:"",
      currentHp:0,
      maxHp:1000,
      damage: 0,
      state:"idle",
      id_img:100000,//通し番号
      img: "",
      sprite:null
    },
    {
      y:positionY+1*positiondist,
        name:"",
        currentHp:0,
        maxHp:1000,
        damage: 0,
        state:"idle",
        id_img:100000,
        img:"",
        sprite:null
    },
    {
      y:positionY+2*positiondist,
        name:"",
        currentHp:0,
        maxHp:1000,
        damage: 0,
        state:"idle",
        id_img:100000,
        img:"",
        sprite:null
    },
    {
      y:positionY+3*positiondist,
      name:"",
      currentHp:0,
      maxHp:1000,
      damage: 0,
      state:"idle",
      id_img:100000,
      img:"",
      sprite:null
    }
  ];


//メンバーのステータス初期値格納
function Partyinit(data){
  let partySt = data;

  playerRenderStetas.map(function( partyrender, index, array){
    let img_id = 0;
    let chara_img_dir ="";//キャラ画像フォルダ名
    
    partyrender.y = positionY+index*positiondist;
    partyrender.state="idle";

    partyrender.id_img = partySt[index]["imgId"];//通し番号格納    
    img_id=partyrender.id_img;

    chara_img_dir="img/characters/"+img_id;

    partyrender.sprite=new CharacterData(chara_img_dir+"/"+img_id+"0301.png");//スプライトデータ生成
    $("#membericonimg"+(index+1)).attr("src", chara_img_dir+"/"+img_id+"0101.png");//顔グラフィックurl格納
    
    partyrender.currentHp=partyrender.MaxHp=partySt[index]["Characterparam"]["hp"];//HP格納
    partyrender.name=partySt[index]["name"];//キャラ名格納

    let id=index+1;

    $("#HPbox_member"+id).css("width", "100%");
    $("#member"+id+"stetas").html(partyrender.currentHp);
    
  });
}

function CharacterStateChange(id_,state_){
  playerRenderStetas[id_].state = state_;
  switch(state_){
    case "idle":
      playerRenderStetas[id_].sprite.motionsprite =  playerRenderStetas[id_].sprite.waitSprite;
    break;
    case "attack":
      playerRenderStetas[id_].sprite.motionsprite =  playerRenderStetas[id_].sprite.attackSprite;
    break;
    case "damage":
      playerRenderStetas[id_].sprite.motionsprite =  playerRenderStetas[id_].sprite.damageSprite;
    break;
    case "skill":
      playerRenderStetas[id_].sprite.motionsprite =  playerRenderStetas[id_].sprite.skillSprite;
    break;
    case "dead":
      playerRenderStetas[id_].sprite.motionsprite =  playerRenderStetas[id_].sprite.deadSprite;
    break;
    case "song":
      playerRenderStetas[id_].sprite.motionsprite =  playerRenderStetas[id_].sprite.songSprite;
    break;
    default:
      playerRenderStetas[id_].sprite.motionsprite =  playerRenderStetas[id_].sprite.waitSprite;
    break;
  }
  playerRenderStetas[id_].sprite.motionsprite.stepX=0;
}

function Party_Draw(){
  
  playerRenderStetas.map(function(memberSt, index, array){
    let sprite_x = 140+markerdistance;

    //コンサート中は演奏モーション
    if(concertFlag){
      memberSt.state="song";
      Characterdraw(sprite_x,memberSt.y,memberSt.sprite.motionsprite);
      return;
    }else{
     
    }

    if(memberSt.state === "dead"){//戦闘不能
      Characterdraw(sprite_x,memberSt.y,memberSt.sprite.deadSprite);
    }else if(memberSt.state === "damage"){//ダメージ
      sprite_x=140+markerdistance+20;
    }else if(memberSt.state==="song"){
      sprite_x = 140+markerdistance;
    }
    else if(memberSt.state !== "idle"){//攻撃、スキル
      sprite_x=140+markerdistance-20;
    }

    if(!Characterdraw(sprite_x,memberSt.y,memberSt.sprite.motionsprite)){
      if(memberSt.currentHp > 0)
        CharacterStateChange(index, "idle");
      else
        memberSt.state="dead";
    }
   
  });
}
