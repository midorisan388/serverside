

            function connection(method,path,data_){
                $.ajax({
                    type:method,
                    url:"http://"+location.host+"/serverside"+path,
                    header:data_,
                    success:function(data){
                        $('body').html(data);
                        console.log("成功");
                    },
                    error:function(XMLHttpRequest, textStatus, errorThrown){
                        alert('error!!!');
                        　　console.log("XMLHttpRequest : " + XMLHttpRequest.status);
                        　　console.log("textStatus     : " + textStatus);
                        　　console.log("errorThrown    : " + errorThrown.message);
                        console.log("失敗");
                    }
                  });
            }