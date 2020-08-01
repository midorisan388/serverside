
let panelId=-1;
let select_cdata,partydata;

$(window).on('load',function(){
    for(var index =1; index<=4; index++){
      $(`#member_${index}`).html(
       `<div class="party_joinbtn griditem" id="joinmember_${index}"><button id="patyu_join_func" onclick="setPartyNewData(${index-1})">編成する</button></div>
        <div class="character_faceimg gridview griditem" id="member_face${index}">
            <img draggable=false class="character_fimg" id="face_img_member_${index}" alt="顔グラ~" src="img/characters/103601/1036010101.png">
            <div class="party_skill gridview griditem">
                  <div class="party_skill_name" id="party_skill_name_${index}">スキル名</div>
                  <div class="party_skill_dic" id="party_skill_dic_${index}">スキル説明</div>
            </div>
        </div>
        <div class="party_member_stetas gridview" id="party_stetas_${index}">
                <div class="party_param_val party_name" id="party_name_${index}">キャラ名</div><div class="party_param_val party_job" id="party_job_${index}">ジョブ</div>
                <div class="party_param_val party_type" id="party_type_${index}">戦闘タイプ</div><div class="party_param_val party_race" id="party_race_${index}">種族</div>
                <div class="party_param_val party_hp" id="party_hp_${index}">HP:</div>
                <div class="party_param_val party_pow" id="party_pow_${index}">攻撃力:</div>
                <div class="party_param_val party_def" id="party_def_${index}">防御力:</div>
                <div class="party_param_val party_magic" id="party_magic_${index}">魔力:</div>
                <div class="party_param_val party_mental" id="party_mental_${index}">精神力:</div>
        </div>`
      );
    }
      $.ajax({
         url:"./php/PartyMemberChange.php",
         type:"post",
          success:function(data){
              
            select_cdata = data["selectharacter"];
            partydata = data["partystetas"];
            panelId = partydata[0]["character_id"];

                 if(partydata[0]["character_id"] != null){
                    init_faceimg= "img/characters/"+partydata[0]["character_img_id"]+"/"+partydata[0]["character_img_id"]+"0201.png";
                 }else{
                    init_faceimg= "img/characters/103601/1036010201.png";
                 }

              if(data["selectharacter"] != 0)setSelectCharacter(select_cdata);
              setPartyStetas(partydata);
              setStetasPanel(data);
              $("#character_standimg").attr("src",init_faceimg);
           },
          error:(function(XMLHttpRequest, textStatus, errorThrown) {
              alert("a!!");
        　　console.log("XMLHttpRequest : " + XMLHttpRequest.status);
        　　console.log("textStatus     : " + textStatus);
        　　console.log("errorThrown    : " + errorThrown.message);
          })
      });
  });


  function setStetasPanel(data_){
      $("#select_characters_dropdown").html(data_["listpanel"]);
  }

  function setSelectCharacter(sedata_){      
      if(sedata_ != null && sedata_ != undefined){
          const
              select_stetas = sedata_["stetas"],
              select_skill = sedata_["character_skilldata"],
              select_faceimg= "img/characters/"+sedata_["character_img_id"]+"/"+sedata_["character_img_id"]+"0201.png";

          $("#character_standimg").attr("src",select_faceimg);
          $("#select_name").html(sedata_["character_name"]);
          $("#select_job").html("ジョブ:"+sedata_["character_job"]);
          $("#select_type").html("タイプ:"+sedata_["character_battleType"]);
          $("#select_race").html("種族:"+sedata_["character_race"]);
          $("#select_hp").html("HP:"+select_stetas["hp"]);
          $("#select_pow").html("攻撃力:"+select_stetas["pow"]);
          $("#select_deff").html("防御力:"+select_stetas["def"]);
          $("#selectmagic").html("魔力:"+select_stetas["magic"]);
          $("#select_mental").html("精神力:"+select_stetas["mental"]);
          $("#select_skill_name").html(select_skill["skill_name"]);
          $("#select_skill_ct").html('CT:'+select_skill["skill_ct"]);
          $("#select_skill_func").html(select_skill["skill_discription"]);
      }else{
          $("#character_standimg").attr("src","");
          $("#select_name").html("編成しない");
          $("#select_job").html("");
          $("#select_type").html("");
          $("#select_race").html("");
          $("#select_hp").html("");
          $("#select_pow").html("");
          $("#select_deff").html("");
          $("#selectmagic").html("");
          $("#select_mental").html("");
          $("#select_skill_name").html("");
          $("#select_skill_ct").html("");
          $("#select_skill_func").html("");
      }
  }

  function setPartyStetas(pdata_){
      for(let i=1; i<=4; i++){
          if(pdata_[i-1] != null && pdata_[i-1] != undefined){
              const
                  party_param = pdata_[i-1],
                  party_stetas=party_param["stetas"],
                  party_skill=party_param["character_skilldata"],
                  party_faceimg= "img/characters/"+party_param["character_img_id"]+"/"+party_param["character_img_id"]+"0101.png";

              const heretxt = (function(){
                  /*
                  <div class="party_param_val party_name" id="party_name_${i}"></div><div class="party_param_val party_job" id="party_job_${i}"></div>
                  <div class="party_param_val party_type" id="party_type_${i}"></div><div class="party_param_val party_race" id="party_race_${i}"></div>
                  <div class="party_param_val party_hp" id="party_hp_${i}"></div>
                  <div class="party_param_val party_pow" id="party_pow_${i}"></div>
                  <div class="party_param_val party_def" id="party_def_${i}"></div>
                  <div class="party_param_val party_magic" id="party_magic_${i}"></div>
                  <div class="party_param_val party_mental" id="party_mental_${i}"></div>
                  */
              }).toString().match(/(?:\/\*(?:[\s\S]*?)\*\/)/).pop().replace(/^\/\*/, "").replace(/\*\/$/, "");

              $('.party_stetas_'+i).html(heretxt);

              $("#face_img_member_"+i).attr("src",party_faceimg);
              $('#party_name_'+i).html(party_param["character_name"]+ "("+party_param["character_lv"]+")");
              $('#party_job_'+i).html("ジョブ:"+party_param["character_job"]);
              $('#party_type_'+i).html("タイプ:"+party_param["character_battleType"]);
              $('#party_race_'+i).html("種族:"+party_param["character_race"]);

              $('#party_hp_'+i).html("HP:"+party_stetas["hp"]);
              $('#party_pow_'+i).html("攻撃力:"+party_stetas["pow"]);
              $('#party_def_'+i).html("防御力:"+party_stetas["def"]);
              $('#party_magic_'+i).html("魔力:"+party_stetas["magic"]);
              $('#party_mental_'+i).html("精神力:"+party_stetas["mental"]);

              $('#party_skill_name_'+i).html(party_skill["skill_name"]);
              $('#party_skill_dic_'+i).html(party_skill["skill_discription"]);
          }else{
              $("#face_img_member_"+i).attr("src","");
              $('#party_name_'+i).html("");
              $('#party_job_'+i).html("");
              $('#party_type_'+i).html("");
              $('#party_race_'+i).html("");

              $('#party_hp_'+i).html("");
              $('#party_pow_'+i).html("");
              $('#party_def_'+i).html("");
              $('#party_magic_'+i).html("");
              $('#party_mental_'+i).html("");

              $('#party_skill_name_'+i).html("");
              $('#party_skill_dic_'+i).html("");
          }
      }
  }

  function selectCharacterpanel(){
      const selecter = document.getElementById("select_characters_dropdown");
      panelId =  selecter.value;

      $.ajax({
         url:"./php/PartyMemberChange.php",
         type:"post",
         data:{
          select_cId:panelId
         },
          success:function(data){
              const select_cdata = data["selectharacter"];

             setStetasPanel(data);
             setSelectCharacter(select_cdata);
             panelId = data["selectharacter"]["character_id"];
           },
          error:(function(XMLHttpRequest, textStatus, errorThrown) {
              alert("a!!");
        　　console.log("XMLHttpRequest : " + XMLHttpRequest.status);
        　　console.log("textStatus     : " + textStatus);
        　　console.log("errorThrown    : " + errorThrown.message);
          })
      });
  }

  function setPartyNewData(partyId_){  
     if(isParty(panelId) || panelId === -1){
         alert("このキャラクターは既に編成されているか無効な値です");
         return;
     }  
    
      $.ajax({
         url:"./php/PartyMemberChange.php",
         type:"post",
         data:{
          select_partyId:partyId_
         },
          success:function(data){
             select_cdata = data["selectharacter"];
             partydata = data["partystetas"];

             setStetasPanel(data);
             setSelectCharacter(select_cdata);
             setPartyStetas(data["partystetas"]);
             alert(data["message"]);

             panelId=-1;
           },
          error:(function(XMLHttpRequest, textStatus, errorThrown) {
              alert("a!!");
        　　console.log("XMLHttpRequest : " + XMLHttpRequest.status);
        　　console.log("textStatus     : " + textStatus);
        　　console.log("errorThrown    : " + errorThrown.message);
          })
      });
  }

  function isParty(id){
      console.log(id);
      
      for(var i=0; i<4; i++){
          console.log(partydata[i]["character_id"]);
          
          if(id === partydata[i]["character_id"]){
              return true;
          }
      }
  }