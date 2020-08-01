
let
  enemyHpvar=new Image(),
  enemyHpSprite,
  //ステータス初期値 テストデータ
  enemydates=[
    {
      x:0,y:45,
      name:"",
      currentHp:0,
      damage:0,
      img:"img/characters/sprite_char4.jpg",
      sprite:new CharacterData("img/enemy/enemystand_1.png")
    },
    {
      x:0,y:45+1*20,
      name:"",
      currentHp:0,
      damage:0,
      img:"img/characters/sprite_char4.jpg",
      sprite:new CharacterData("img/enemy/enemystand_2.png")
    },
    {
      x:0,y:45+2*20,
      name:"",
      currentHp:0,
      damage:0,
      img:"img/characters/sprite_char4.jpg",
      sprite:new CharacterData("img/enemy/enemystand_3.png")
    },
    {
      x:0,y:45+3*20,
      name:"",
      currentHp:0,
      damage:0,
      img:"img/characters/sprite_char4.jpg",
      sprite:new CharacterData("img/enemy/enemystand_1.png")
    }
  ];
 


function Enemyinit(data){
  //enemyHpvar.src="img/battlescene/enemyHpvar.jpg";//エネミーHpフレーム
  //enemyHpSprite=new Sprite(enemyHpvar,0,0,180,35,1);

  var i=0;

  while(i< 4){    
    enemydates[i].x=0;
    enemydates[i].y= 45+i*20;
    enemydates[i].name=data[i]["characterName"];
    enemydates[i].currentHp = enemydates[i].maxHp = data[i]["Characterparam"]["hp"];    
    enemydates[i].sprite = new CharacterData("img/enemy/enemystand_2.png");
    i++;
  }

}

function EnemyStetasUpdate(enemySt){
  for(var i=0; i < 4; i++){
    enemydates[i].damage=enemydates[i].currentHp-(enemySt[i]["Characterparam"]["hp"]-enemySt[i]["Characterparam"]["currentDamage"]);
    enemydates[i].currentHp=enemySt[i]["Characterparam"]["hp"]-enemySt[i]["Characterparam"]["currentDamage"];
  }
}

function EnemyDraw(){
  for (var i=0;i<4;i++){
    //登場時の座標移動
   if(enemydates[i].currentHp > 0){
      while(enemydates[i].x < 156-markerdistance)enemydates[i].x+=3;
      Characterdraw(enemydates[i].x - member_size_X/2 ,45+i*20,enemydates[i].sprite.motionsprite);

      ctx.fillStyle="black";
      ctx.strokeText(("【"+enemydates[i].name+"】"+enemydates[i].currentHp),140-markerdistance, 60+i*20);
      
      //enemyHpSprite.draw(ctx,140-markerdistance,40+i*20);
      //ctx.strokeText(enemydates[i].name,142-markerdistance, 42+i*20);
    }
  }
}

