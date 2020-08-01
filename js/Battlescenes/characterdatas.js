

//キャラクターデータクラス
function CharacterData(img){
  this.img=new Image(),
  this.img.src=img;
  member_size_X = member_size_Y=64,//SD画像一枚分のサイズ

  this.waitSprite = new AnimSprite(this.img,0,0,3,1,0.5);//待機
  this.attackSprite= new AnimSprite(this.img,member_size_X*3,0,3,1,0.5);//攻撃
  this.skillSprite= new AnimSprite(this.img,member_size_X*3,member_size_Y*3,3,1,0.5);//スキル使用
  this.damageSprite= new AnimSprite(this.img,0,member_size_Y*4,3,1,0.5);//被ダメージ
  this.deadSprite= new AnimSprite(this.img,member_size_X*6,member_size_Y*5,3,1,0.5);//戦闘不能
  this.songSprite = new AnimSprite(this.img,member_size_X*6,member_size_Y,3,1,0.5);//演奏
  this.motionsprite = this.waitSprite;

}
//キャラ描画
Characterdraw = function(charax_,charay_,sprite_){
  return sprite_.animdraw(ctx,charax_,charay_,updatecounter);//モーション終了フラグ
};
