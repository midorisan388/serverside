var canvas = document.getElementById("maincanvas");
const ctx = canvas.getContext('2d');
const charsize=64;
const FPS = 60;
const c_w=canvas.width/2;
const c_h=canvas.height/2;
const c_waspect = window.parent.screen.width/canvas.width;
var charactersprites=[];
var characterImages=[];

var SpriteObject = function (img,n,w,h) {
    this.img=img;
    this.name = n;
    this.width = w;
    this.height = h;
    this.dx = 0;
    this.dy = 0;
    this.px = this.py = 0;
    this.tx = this.ty = 0;
    this.vecter = 1;

    this.sprites_=new AnimSprite(this.img,0,0,charsize*3,charsize,charsize,charsize,3,1,1);
    this.move_x = this.move_y = 0;
    this.move =() =>{draw(this);}
};

SpriteObject.prototype.vejesetting = function () {
    this.move = function () {
        if ((Math.abs(this.px-this.dx) < 1 && Math.abs(this.py-this.dy) < 1) || Math.abs(this.px-this.dx) < 1 ) {
            setstandard(this);
         } else {
            this.dx +=this.move_x;
            this.dy =this.vecter * (this.dx - this.tx) * (this.dx - this.tx)-this.ty;
            draw(this);
        }
    }
}

SpriteObject.prototype.linesetting = function () {
    this.move = function () {
        if (Math.abs(this.px-this.dx) < 1 && Math.abs(this.py-this.dy) < 1) {
            setstandard(this);
         } else {
            this.dx = this.dx +this.move_x;
            this.dy =this.dy +this.move_y;
            draw(this);
        }
    }
}


function init() {
    for(var i=0;i<4;i++){
        characterImages[i]=new Image();
        characterImages[i].src="img/Eventimg/sprite_char"+(i+1)+".jpg";
        charactersprites[i]=new SpriteObject(characterImages[i],charsize,charsize);
        charactersprites[i].dy = charactersprites[i].dx = i*10;
    }
}

var draw = function(sprite) {
    sprite.sprites_.animdraw(ctx,sprite.dx,sprite.dy,timer);
}

var setveje = function (obj, px_, py_, ty_) {
    obj.px = obj.dx+px_;
    obj.py = py_;
    obj.tx =obj.dx + (obj.px - obj.dx)/2;
    obj.ty = obj.dy+ty_;
    obj.move_x = (obj.px-obj.dx)/FPS;
    obj.vecter =(obj.ty > 0) ? 1 : -1;

    obj.vejesetting();
}

var setline = function(obj,px_,py_){
    obj.px=px_ + px_;
    obj.py=py_ + py_;
    obj.move_x = (obj.px-obj.dx)/FPS;
    obj.move_y = (obj.py-obj.dy)/FPS;

    obj.linesetting();
}

var setposition = function(obj,px_,py_){
    obj.dx=px_;
    obj.dy=py_;

    setstandard(obj);
}

var setstandard = function(obj){
    obj.move=() =>{draw(obj);}
}