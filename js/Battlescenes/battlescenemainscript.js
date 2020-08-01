   let
      fps_=60,

      canvas_w,canvas_h,
      charasd_margine,charasd_offset_x,
      charaicon_margine,icon_offset_x,
      charaSD_X,
      memberView = [];

      audioMng=null;

      Startdelay=0,Gametimer=0,offsetTime=0,

      eventIndex=-1,
      isEvn=false, //イベントクエストフラグ
      eventDt=[], //イベント用データ
      concertFlag=false,//敵をせん滅したらフィーバーモードに移行

      initMessagetextTime=0, //メッセージテキストを初期化する時間を記憶
      updatecounter=0;

    const
      Waveinfo = document.getElementById("enemycount"),
      Scoreinfo = document.getElementById("scoreview"),
      
      parentdom = document.getElementById("mainview"),//アニメーション用div
      maincanvas_=document.getElementById("maincanvas"),//描画用
      ctx = maincanvas_.getContext('2d');


    function Timecount(){
        updatecounter++;
        Gametimer=updatecounter/fps_;
    }

    function Init(data){     
      Gametimer=Startdelay-offsetTime;

      canvas_w=parentdom.clientWidth;
      canvas_h=parentdom.clientHeight;

      charasd_offset_x=canvas_w*0.8;
      charasd_margine=canvas_h*0.1;
      icon_offset_x=canvas_w*0.05;
      charaicon_margine=canvas_w/4;

      //イベントクエストフラグとデータのセ設定
      isEvn=(data["event"] != false);
      eventDt = data["event"];
      
      //クエストデータ、ステータスデータ格納
      let
      audiodata = data["audiohtml"][0],
      audiotitle = data["audiohtml"][1],
      titledata = data["title"],
      notesfile=data["notesdata"],
      partystetas =data["partySt"],
      enemystetas = data["enemySt"];

      $("#battlebgm").attr("src",audiodata);
      $('#Titlw_h').html(titledata);//クエストタイトル表示
      $('#addstetas').html(audiotitle);//曲タイトル表示
      ScoreInit();
      Partyinit(partystetas);
      Enemyinit(enemystetas);
      Notesinit(notesfile);

      audioMng = new AudioManager(audiodata);
    }

    function touchStartPlay(){
      audioMng.AudioSetup();
      Action(0);

      if(isEvn)
        $(".event-content").addClass('event-content-open');
    }

    function update(){
      if(audioMng.audioDurarion <= Gametimer){
        GameStage="Clear";
      }else{
        Timecount();
      }
    }

    function render(){
      const initMessagetextWait=2; //メッセージ更新の5秒後に初期化
      ctx.clearRect(0, 0, maincanvas_.width, maincanvas_.height);
      Maincanvas_render();

      if(initMessagetextWait+initMessagetextTime < Gametimer){
        $('#battle_anounce_party').html("");
        dispDamage=false;
      }

      //イベントテキスト表示
      if(isEvn){
        for(var i=eventDt.length-1; i >= eventIndex; i--){
          if(i < 0)break;
          if(eventDt[i]["timing"] < Gametimer && eventIndex != i){
            eventIndex=i;
            $(".event-content-text").html(eventDt[eventIndex]["text"]);
            break;
          }
        }

        if(eventDt[eventDt.length-1]["timing"] + 5 <= Gametimer){
          $(".event-content").removeClass('event-content-open');
          $(".event-content").addClass('event-content-close');
        }
      }
    }

    //TODO:GameStageをコンソールから弄れないようにする
    function run() {
      if(GameStage === "GameOver"){
        location.href="/game/page?page-link=my";
      }
      if(GameStage === "Clear"){
        GameStage="Result";
        //location.href="/game/page?page-link=my";
        location.href="/game/battleresult";
      }
      else if(GameStage === "Play"){
        if(!checkMedia()){
          GameStage="Pause";
          audioMng.mainAudio.pause();
          return;
        }
        update();
        render();
      }
    }

//トリガーイベント
$(function(){

  $('#member1-icon').on(EVENTNAME_TOUCHSTART,function(){
    Action(0);
  });
  $('#member2-icon').on(EVENTNAME_TOUCHSTART,function(){
    Action(1);
  });
  $('#member3-icon').on(EVENTNAME_TOUCHSTART,function(){
    Action(2);
  });
  $('#member4-icon').on(EVENTNAME_TOUCHSTART,function(){
    Action(3);
  });
});

function Action(memberid_){
  audioMng.SEpop();

  if(concertFlag){
    //コンサートモード
    for(var i=0; i<4; i++)
      CharacterStateChange(i, "song");//モーション変更
  }

  $.ajax(
    {
      url: 'php/battlePHP/ActionSkillList.php',
      dataType:"json",
      type:"post",
      data:{
        notesdata:notes,
        time:Gametimer,
        lernid:memberid_,
        updatenotes:"alway"
      }
  }).done(function(data){  
    if(data["message"] === "コンサートモード<br>"){
      concertFlag=true;
    }
    StetasUpdate(memberid_,　data);
  }).fail(function(XMLHttpRequest, textStatus, errorThrown) {
    //alert('Action:error!!!');
　　console.log("XMLHttpRequest : " + XMLHttpRequest.status);
　　console.log("textStatus     : " + textStatus);
　　console.log("errorThrown    : " + errorThrown.message); 
  });
}


//ステータス表示更新 
function StetasUpdate(id, datas){ 
  const 
    noteshantei = datas["noteshantei"],
    score = datas["score"],
    combo = datas["combo"],
    gameover = datas["gameOverFlag"],
    playerSt = datas["memberdata"],
    //state = datas["motionState"],
    message = datas["message"];
              
    notes=datas["notesdata"];
    
    ScoreUpdate(noteshantei, score, combo);
      
    if(gameover === "Gameover"){
      gameEnd();
      return;
    }else if(!concertFlag){ 
      playerRenderStetas.map(function( memberSt, index, array){  
        memberSt.damage = memberSt.currentHp-(playerSt[index]["Characterparam"]["hp"]-playerSt[index]["Characterparam"]["currentDamage"]);
        memberSt.currentHp=playerSt[index]["Characterparam"]["hp"]-playerSt[index]["Characterparam"]["currentDamage"];//HP更新
        const memberhpasp = parseFloat(parseInt(memberSt.currentHp)/parseInt(memberSt.MaxHp));

        let id=index+1;
        CharacterStateChange(index, playerSt[index]["motion"]);//モーション変更
        $("#HPbox_member"+id).css("width",(memberhpasp)*100 +"%");//HPバー更新
        $("#member"+id+"stetas").html(memberSt.currentHp);//パーティHP表示
      });
      EnemyStetasUpdate(datas["enemydata"]);
    }else{
      //コンサートモード中は表示とか消して専用グラに差し替え
    }

    if(message != ""){
      initMessagetextTime=Gametimer;
      dispDamage=true;
      $('#battle_anounce_party').html(message);//行動メッセージ表示
    }      
}

function gameEnd(){//曲終わり、ゲームオーバー時に呼び出す
  audioMng.mainAudio.pause();
  GameStage="GameOver";
}

//ダブルタップの拡大防止策
document.addEventListener(EVENTNAME_TOUCHSTART, event=>{
  if(event.targetTouches > 1){
    event.preventDefault();
  }
}, {
  passive:false
});

let lastTouch=0;
document.addEventListener(EVENTNAME_TOUCHEND, event=>{
  const now = window.performance.now();
  if(now - lastTouch <= 500){
    event.preventDefault();
  }
  lastTouch=now;
},{
  passive:false
});