
var screen_w=980,screen_h=545;


function Test(e){
    //検査用のログ
    console.log(player.offset_y);
    console.log(mapdata.npcobj.py);
}

function main(){
   mapmain();

   stetasInit();
   initSprites();
   initButton();

   run();
}

function run(){
 var loop = function () {
   if(startclear==true){
     update();
     render();
   }
     window.requestAnimationFrame(loop, canvas_main);
 }

 window.requestAnimationFrame(loop, canvas_main);
}

function update(){
  touch();
  mapupdata();
}

function render(){
    ctx_main.clearRect(0,0,width_main,height_main);

 maprender();
 vardraw();

}

