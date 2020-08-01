const faceimgurl = "img/Eventimg/";
var selif_mode="selif";

function EventScene_text(){
    $.ajax({
        url:"./php/getSeliftext.php",
        dataType:"json",
        type:"post"
    }).done(function(data){
        const serif_data = data;//テキスト整頓済み　表示
        $('#remake').html(serif_data["owner"]);
        $('#selif').html(serif_data["body"]);
        selif_mode=serif_data['mode'];

        //選択肢が続く場合表示
        var sid=0;
        var select_lists="";
        if( selif_mode === "end"){
            location.href="./MyPage.html";
        }else if( selif_mode === "select"){
         while(serif_data['selects_count'] > sid){
             //選択肢リスト生成
             select_lists += "<div class=selectlis style=z-index:1000; id=select-"+sid+">"+serif_data['selects'][sid]+"</div>";
             sid++;
         }
         $('#selects').html(select_lists);
        }else{
            $('#selects').html("");
        }
    }).fail(function(XMLHttpRequest, textStatus, errorThrown) {
        alert('error!!!');
    　　console.log("XMLHttpRequest : " + XMLHttpRequest.status);
    　　console.log("textStatus     : " + textStatus);
    　　console.log("errorThrown    : " + errorThrown.message); 
    });
}

$(function(){
    $('div .selectlis').on('click',function(){
        alert("click");
        EventScene_text();
    });
   $(document).on('click',function(){
        if(selif_mode === "selif"){
            EventScene_text();
        }
    });
})