var
//可変
backimg = new Image(),
homecharacterimg = new Image(),
//固定イメージ
vanerimg = new Image(),
mypagebuttonimg = new Image(),
partybuttonimg = new Image(),
questbuttonimg = new Image(),
optionbuttonimg = new Image(),

backgraund,
homecharacter,
vaner,
mypage,
party,
quest,
optionbuttonimg;

function initSprites(){
  vanerimg.src="img/bana-.jpg";
  backimg.src="img/hometest.jpg";
  homecharacterimg.src="img/teststand2.jpg";

  vaner=new Sprite(vanerimg,0,0,300,95);
  backgraund=new Sprite(backimg,0,0,354,200);
  homecharacter = new Sprite(homecharacterimg,0,0,200,282);
}
