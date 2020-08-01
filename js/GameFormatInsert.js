$.ajax(
  {
    //画面の向きを検出して案内を表示する
    url:"GameFormat.html",
    dataType:"html",
    cache:false,
    async:false,
    success:function(data){
        $("body").append(data);
    },
    error:(function(XMLHttpRequest, textStatus, errorThrown) {
      　　console.log("XMLHttpRequest : " + XMLHttpRequest.status);
      　　console.log("textStatus     : " + textStatus);
      　　console.log("errorThrown    : " + errorThrown.message);              
    })
});