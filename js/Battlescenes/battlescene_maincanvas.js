
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
let
GameStage = "Ready",//プレイ段階
dispDamage=false,
markerdistance=110;//中央マーカーからの両サイドの距離px

////////////////////////////////////////////////////メイン処理//////////////////////////////////////////

function Maincanvas_render(){
  ctx.fillRect(156,0,1,300);
  NotesDraw();
  Party_Draw();
  EnemyDraw();
  if(dispDamage) DamageDrow();
}

function DamageDrow(){
  ctx.fillStyle="red"
  //味方側のダメージ表示
  playerRenderStetas.map(function(memberSt, index, array){
    if(memberSt.damage > 0)
      ctx.strokeText("-"+memberSt.damage,140+markerdistance-20,40+index*20);
  });

  //敵側のダメージ表示
  enemydates.map(function(enemySt, index, array){
    if(enemySt.damage > 0)
      ctx.strokeText("-"+enemySt.damage,140-markerdistance,50+index*20);
  });
}
