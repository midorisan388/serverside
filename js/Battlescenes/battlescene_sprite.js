const 
SD_W=120,SD_H=120,
ICON_W=120,ICON_H=120;

var
  x,y,

  backgroundImg="img/battlescene/mapimgtest.jpg",//背景画像
  Nortsimg = new Image(),
  Norts_,
  lernImg = new Image(),

  leaderIcon=new Image(),


  //プレイヤーメンバー画像
  memberIconimg=[new Image(),new Image(),new Image(),new Image()],//タップアイコン画像
  memberSDImg=[new Image(),new Image(),new Image(),new Image()],//戦闘SD画像
  battler=[],//メンバーデータ

  //レーン画像座標
  lernElement_top=[],
  lernElement_h=[],
  lernElement_w=[];

//レーン描画
  function memberRender(ctx_){
    for(i=1;i<=4;i++){
      lernElement_w[i]=$('#lern'+i).width();
      lernElement_h[i]=$('#lern'+i).height();
      lernElement_top[i]=$('#lern'+i).offset().top;
      x=lernElement_w[i]*0.75;
      y=canvas_h*0.5 + (canvas_h*0.5/4)*(i-1);
      battler[i-1].memberSD.draw(ctx_,x,y-SD_H/2);
      Norts_.draw(ctx_,canvas_w/2-(40*i),y-150/2);
    }
  }
