let
  member_combCount=[0,0,0,0],
  maxComb_=0,
  comb_=0;


  function ScoreInit(){
    document.getElementById("MISS").innerHTML= 0;
    document.getElementById("GOOD").innerHTML= 0;
    document.getElementById("GREAT").innerHTML= 0;
    document.getElementById("PARF").innerHTML= 0;

    document.getElementById("Score").innerHTML= 0;
    document.getElementById("COMB").innerHTML= 0;
    
    for(var i=0;i<4;i++){
      if(member_combCount[i] > 100)member_combCount[i]=100;
      $("#Comb_member"+(i+1)).html(member_combCount[i] +" %");
    }
  }
  
  function ScoreUpdate(judgedata, score, combo){
    document.getElementById("MISS").innerHTML= judgedata["MISS"];
    document.getElementById("GOOD").innerHTML= judgedata["GOOD"];
    document.getElementById("GREAT").innerHTML= judgedata["GREAT"];
    document.getElementById("PARF").innerHTML= judgedata["PARF"];;

    document.getElementById("Score").innerHTML= score;
    document.getElementById("COMB").innerHTML= combo;


    for(var i=0;i<4;i++){
      if(member_combCount[i] > 100)member_combCount[i]=100;
      $("#Comb_member"+(i+1)).html(member_combCount[i] +" %");
    }
  }
