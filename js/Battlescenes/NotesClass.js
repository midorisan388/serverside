let notes=[], notesLen=0;//ノーツオブジェクト配列

const notesImagsprite = new Image();

notesImagsprite.src = "img/battlescene/notesiconsprite.png";

//------JSONファイルからノーツ情報取得&格納-------------------------------------//
function Notesinit(data){
  
  const notesdata_json = JSON.parse(data);//JSON形式へエンコード
  var i=0;

  notesdata_json.forEach((notesdata) => {
    notes_ = notesdata;
    //ノーツデータ分だけ確保して初期化
    notes[i] = {
      timing:notes_['timing'],//タイミング時間
      lernID:notes_['lernID'],//レーンID
      type:notes_['type'],//ノーツタイプ
      status: "Awaken",
      judge: "ALWAY",//状態初期化
      hanteiT: 0.00
    };
    i++;
  });
  notesLen = notes.length;//全ノーツ数格納
}
//----------------------------------------------------------------------------//

function Judg(id){
  const juge_time={
    BAD:0.42,
    GOOD:0.38,
    GREAT:0.25,
    PARF:0.06
  };

  const timingjudge = Math.abs(Gametimer-notes[id].timing);//判定時差計算

  if(timingjudge <= juge_time.PARF){
    //PERFECT範囲内
    return "perf";
  }else if(timingjudge <= juge_time.GREAT){
    //GREAT範囲内
    return "great";
  }else if(timingjudge <= juge_time.GOOD){
    //GOOD範囲内
    return "good";
  }else if(timingjudge <= juge_time.BAD){
    //BAD範囲内
    return "bad";
  }else if( Gametimer - notes[id].timing >  juge_time.BAD ){
    //ミス 
    return "miss";
  }else {
    //判定外時間
    return "alway";
  }
}

function NotesDraw(){
  const 
  notesimg_size=32,
  notesimg_sprite={
    "ATK":new Sprite(notesImagsprite,0,0,notesimg_size,notesimg_size,1),
    "DEF":new Sprite(notesImagsprite,notesimg_size,0,notesimg_size,notesimg_size,1),
    "ENH":new Sprite(notesImagsprite,0,notesimg_size,notesimg_size,notesimg_size,1),
    "TEC":new Sprite(notesImagsprite,notesimg_size,notesimg_size,notesimg_size,notesimg_size,1)
  },
  ctx = document.getElementById("maincanvas").getContext('2d');

  for(var i=0;i<notesLen;i++){
    //center 156px
    //60fps = 110px  -notesimg_size/2
    if(notes[i].judge === "ALWAY"){
      const  
        x_ = 156+((notes[i].timing-Gametimer)*markerdistance)-notesimg_size/2,//横移動座標
        y_=notes[i].lernID*20;//レーンIDに対応した座標
        
      //サポート機能用に残す
      /*if(Judg(i) === "perf"){
        //PERFECT範囲内
       notesimg_sprite[notes[i].type].draw(ctx, x_, 30+y_);
      }else if(Judg(i) === "great"){
        //GREAT範囲内
       notesimg_sprite[notes[i].type].draw(ctx, x_, 35+y_);
      }else if(Judg(i) === "good"){
        //GOOD範囲内
        notesimg_sprite[notes[i].type].draw(ctx, x_, 40+y_);
      }else if(Judg(i) === "bad"){
        //BAD範囲内
        notesimg_sprite[notes[i].type].draw(ctx, x_, 38+y_);
      }else*/ 
      if(Judg(i) === "miss"){
        notes[i].judge="MISS";
        notes[i].status="MISS";
        
        notesMissfunc(i, notes[i].lernID);
        break;
      }else {
        //判定外時間
        notesimg_sprite[notes[i].type].draw(ctx, x_, 45+y_);
      }
    }
  }
}

//打ち損じ
const notesMissfunc = function(no, id_){
  let hantei="miss";
  //if(isEvn) hantei="";
  
  $.ajax({
    url: 'php/battlePHP/ActionSkillList.php',
    dataType:"json",
    type:"post",
    data:{
      notesdata:notes,
      notesId: no,
      time: Gametimer,//audioMng.mainAudio.currentTime,
      lernid:id_,
      updatenotes:"miss"
    },
    success:function(data){
      notes=data["notesdata"];

      StetasUpdate(id_,data);
    },
    error:(
      function(XMLHttpRequest, textStatus, errorThrown) {
        alert('miss:error!!!');
    　　console.log("XMLHttpRequest : " + XMLHttpRequest.status);
    　　console.log("textStatus     : " + textStatus);
    　　console.log("errorThrown    : " + errorThrown.message); 
      })
  });
}