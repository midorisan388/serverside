var
button_left,button_up,button_right,button_down,
buttonimg=new Image(),

input_timer_s=30,

key_value={
  "up":38,
  "down":40,
  "left":37,
  "right":39
},keyid=0,
x,y,inputtimer=input_timer_s;



BUTTONSIZE_W=100,BUTTONSIZE_H=100;

function initButton(){
  buttonimg.src="img/menu/crossvar.jpg";
  button_left=new Sprite(buttonimg,0,150,BUTTONSIZE_W,BUTTONSIZE_H,1);
  button_up=new Sprite(buttonimg,100,50,BUTTONSIZE_W,BUTTONSIZE_H,1);
  button_right=new Sprite(buttonimg,200,150,BUTTONSIZE_W,BUTTONSIZE_H,1);
  button_down=new Sprite(buttonimg,100,150,BUTTONSIZE_W,BUTTONSIZE_H,1);
}

function touch(){
   document.getElementById( "main" ).addEventListener( "click", function( event ) {
  	var clickX = event.pageX ;
  	var clickY = event.pageY ;

  	// 要素の位置を取得
  	var clientRect = this.getBoundingClientRect() ;
  	var positionX = clientRect.left + window.pageXOffset ;
  	var positionY = clientRect.top + window.pageYOffset ;

  	// 要素内におけるクリック位置を計算
  	 x = clickX - positionX ;
  	 y = clickY - positionY ;
  } ) ;
  callmoving();
  call_playermove();
}

function callmoving(){
  if(inputtimer>=input_timer_s){
      if(x>=100 && x<=164){//上下キー判定
        if(y>=260 && y<=336){
          keyid=key_value["up"];

        }else if(y>=460 && y<=535)
          keyid=key_value["down"];
      }else if(y >=360 && y<=435){//左右キー
        if(x>=20 && x<= 84)  keyid=key_value["left"];
        if(x>=164 && x<= 228)  keyid=key_value["right"];
      }
    }else {
      inputtimer++;
      keyid=0;
    }
}

function  call_playermove() {
 player.Move(keyid);
}

function vardraw(){
    button_left.draw(ctx_main,0,350);
    button_up.draw(ctx_main,80,250);
    button_right.draw(ctx_main,150,350);
    button_down.draw(ctx_main,80,450);
}
