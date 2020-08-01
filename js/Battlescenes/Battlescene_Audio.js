class AudioManager{
      
  audioDurarion=0;//再生時間
  audio_data="";
  audio_vol=0;
  se_vol=0;
  mainAudio;
  
  constructor(data){
    this.audio_data=data;
    var optionCookie = document.cookie;
    var optionCookieArray = optionCookie.split(";");
    
    optionCookieArray.forEach(cArray=> {
      if(cArray[0]=="game_audio") this.audio_vol = cArray[1];
      if(cArray[0]=="se_audio") this.se_vol = cArray[1];
    });
  }

  AudioSetup(){
    //演奏開始
    this.mainAudio = new Audio(this.audio_data);
    this.mainAudio.volume = 0.5;//this.audio_vol;
    this.audioDurarion = this.audio_data.duration;
    this.mainAudio.play();
    this.SEpop();
  }

  SEpop(){
    const battle_se_tap = "audio/se/sword.mp3";
    var  tapse = new Audio(battle_se_tap);
    tapse.volume = 0.5;//this.se_vol;
    tapse.play();
 }
}