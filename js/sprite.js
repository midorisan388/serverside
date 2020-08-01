//スプライトシート 576*384 px

function Sprite(img, x, y, width, height,scal) {
    this.img = img;
    this.x = x ;
    this.y = y ;
    this.width = width;
    this.height = height;
    this.dw = this.width/scal;
    this.dh = this.height/scal;
};

function AnimSprite(img, x, y,stepsW,stepsH,scal) {
    this.member_size_X = this.member_size_Y=64,//SD画像一枚分のサイズ
    this.partyspritesize_W=576,this.partyspritesize_H=384;//スプライトシート一枚分のサイズ
    
    this.img = img;
    this.x = x ;
    this.y = y ;
    this.width = this.partyspritesize_W;//全体の幅
    this.height = this.partyspritesize_H;//全体の高さ
    //-------------アニメーションステータス---------------------//
    this.spriteWidth = this.member_size_X;//1コマの幅
    this.spriteHeight = this.member_size_Y;//1コマの高さ
    this.stepX=0;
    this.stepY=0;
    this.maxStepX=stepsW;//コマ数 x
    this.maxStepY=stepsH;//コマ数 y
    this.scal=scal;
};

Sprite.prototype.draw = function (ctx_, x, y) {
    ctx_.drawImage(this.img, this.x, this.y, this.width, this.height,
        x, y, this.dw, this.dh);
};


AnimSprite.prototype.animdraw = function (ctx_, x, y,updatecounter) {
    const STEPTIME = 10;

    if(this.stepX >= this.maxStepX) this.stepX=0;//コマ始点X0

    ctx_.drawImage(this.img,//Image()
        this.x+this.stepX*(this.spriteWidth),//コマの始点座標x
        this.y+this.stepY,//コマの始点座標y
        this.spriteWidth,//切り取り幅
        this.spriteHeight,//切り取り高さ
        x, y,
        this.spriteWidth*this.scal,
        this.spriteHeight*this.scal);

    if(updatecounter % STEPTIME === 0){//コマ送り
      this.stepX++;
    }
    return (this.stepX < this.maxStepX);
};
