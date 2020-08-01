const screenOrientation=function(){
    let winWidth,winHeight;

    winWidth=$(window).width();
    winHeight=$(window).height();

    if(winWidth >= winHeight){
        //横画面
        return "landscape";
    }else{
        //縦画面
        return "portrait";
    }
}

function checkMedia(){

    if(DEVICE==='Tablet'){
        // スマホ（iPhone・Androidスマホ）の場合の処理を記述
       // $(window).on("load orientationchange resize", function(){
            if(screenOrientation() === "landscape") {
                $(".orientationView").html();
                $("#screenMes").html(screenOrientation());
                return true;
            } else {
                $("#screenMes").html("横画面にしてください:"+DEVICE);
                return false;
            }
        //});
    }else{
        // PC・タブレットの場合の処理を記述
            if(screenOrientation() === "landscape") {
                $(".orientationView").html();
                $("#screenMes").html();
                return true;
            } else {
                $("#screenMes").html("横画面にしてください:"+DEVICE);
                return false;
            }
    }
   
}

$(function(){
   checkMedia();
});
