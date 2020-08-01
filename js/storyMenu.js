    
    let selectStoryIndex=0; //章
    let moveX=100;
    var btnCnt=-1, pageCnt=0; //btnCnt=各章のめくり数カウント
    let pages = 0;
    let questChapterPages=[];//章ごとのクエストページ数
    let chapterCnt=0;
    let questParam=[];  //食えsと情報
    let questTitlePage=[];//章タイトルページindex


    function Init(questData){
        ChangeBackImg(0,0);

        questParam = questData["questData"];
        questTitlePage = questData["questTitlePageNo"];
        questChapterPages = questData["questChapterPages"]; 
        chapterCnt = questChapterPages.length; //章の数
        pages=questData["questPages"].length+2;

        const lastPage= $('<div />',{"class":"quest-info-end"}).html("更新待ち");
        
        for(var i=0; i < questData["questPages"].length; i++){
            let setQuestElm = $('<div />',{"class":"quest-page-contents"}).html(questData["questPages"][i]);
            $('#magazine').append(setQuestElm);
        }
        $('#magazine').append(lastPage);

        $('#magazine').turn("pages",pages+2);

        //選択中のページの案内ビュー
        InitPagePoints(questData);
        //章リスト生成
        InitChapterList(questData["questTitlePageNo"]);

        moveX=window.parent.screen.width/(pages+1);

        $('#magazine').turn({
            width: 500,
            height: 400,
            left: 20+"%",
            //transform: "translate3d( 0px, 50px,0px)",
            elevation: 50,
            gradients: true
            //disable:false
        });

        PageViewMove();
    }

    function InitChapterList(data){
        for(var i =0; i < data.length; i++){
            let liEllm=$('<li />',{"value": i, "onclick": "SkipChapter("+i+")"}).html("第"+(i+1)+"章");
            $('.chapter-list-view').append(liEllm);
        }
    }
    
    function InitPagePoints(data){
        let chapter_id=0;
        const setAnnounceP= $('<div />',{"id":"anounce-point-title", "style":"left:"+((1/pages)*100)+"%"});
        $('.page-anounce-point').append(setAnnounceP);

        for(var i=0,page_index=0; i< $('#magazine').turn("pages")/2; i++){
            let pageClassName="anounce-point";
            if(page_index === 0){
                pageClassName="anounce-point-title";
            } 

            const setAnnounceP= $('<div />',{"id":pageClassName, "style":"left:"+((i+2)/pages)*100+"%"});
            $('.page-anounce-point').append(setAnnounceP);

            page_index++;
            if(page_index > data["questChapterPages"][chapter_id]){
                page_index=0;
                chapter_id++;
            }
        }
    }

    //章のタイトルページまで飛ぶ
    function SkipChapter(id){        
        selectStoryIndex=id;
        questCnt=QuestIdCounter(id,0)-1;
        btnCnt=0;
        pageCnt=questTitlePage[id];

        SetQuestIdSession();
        $('#magazine').turn('page', questTitlePage[id]*2-1);
        PageViewMove();
    }

    function GetQuestDate(){
        $.ajax({
            url:"./php/storyMenutest.php",
            type:"post",
            success:function(data){
                Init(data);
            },
            error:(function(XMLHttpRequest, textStatus, errorThrown) {
            　　console.log("XMLHttpRequest : " + XMLHttpRequest.status);
            　　console.log("textStatus     : " + textStatus);
            　　console.log("errorThrown    : " + errorThrown.message);              
            })
        });
    }

    //バトルページに遷移
    function LocateBattleScene(id){
        console.log("battle start");
        $.ajax({
        url:"php/storyMenutest.php",
        type:"post",
        data:{
            panelId:id,
            quest_start:true,
        },
        success:function(data){
            //クエスト開始を送る
        },
        error:(function(XMLHttpRequest, textStatus, errorThrown) {
        　　console.log("XMLHttpRequest : " + XMLHttpRequest.status);
        　　console.log("textStatus     : " + textStatus);
        　　console.log("errorThrown    : " + errorThrown.message);              
            })
        });
    }

    let questCnt=-2; //めくったクエストページ数

    $(".prev-btn").click(()=>{
        pageCnt--;

        //1章タイトルページまたは最初のページで操作したか
        if(selectStoryIndex <= 0 && btnCnt <= 0){
            //最新クエストページへ
            btnCnt = questChapterPages[chapterCnt-1];
            questCnt = questParam.length-1;
            pageCnt = pages/2;
            selectStoryIndex = chapterCnt-1;
            ChangeBackImg(0,questCnt);
            $('#magazine').turn('page',$('#magazine').turn('pages')-1);

            SetQuestIdSession();
        }else if(btnCnt === 0){//前の章に戻る
            if(selectStoryIndex > 0) selectStoryIndex--;
            btnCnt=questChapterPages[selectStoryIndex];
            questCnt = QuestIdCounter(selectStoryIndex+1,0)-1;

            $('#magazine').turn('previous');
        }else{
            btnCnt--;
            if(btnCnt > 0) questCnt--;
            ChangeBackImg(questCnt,questCnt-1);
            SetQuestIdSession();
            $('#magazine').turn('previous');
        }

        /*
        if(btnCnt > 1 && !isTitlePage()){
            btnCnt--;
            pageCnt--;
            questCnt--;
            SetQuestIdSession();

            if(questCnt < questParam.length && questCnt >=0 )
                ChangeBackImg(questParam[questCnt+1]["backImg"],questParam[questCnt]["backImg"]);

            $('#magazine').turn('previous');
        }else if(selectStoryIndex > 0 && isTitlePage()){
            //前の章
            selectStoryIndex--;
            pageCnt--;
            btnCnt = questChapterPages[selectStoryIndex];
            $('#magazine').turn('previous');
        }else{
            //最後のページへ
            selectStoryIndex = chapterCnt-1;
            pageCnt = $('#magazine').turn('pages')/2;
            //btnCnt = 1;
            btnCnt=0;//questCnt=QuestIdCounter(questChapterPages.length,0)+questChapterPages.length-1; //questParam.length-1;
            SetQuestIdSession();
            
            ChangeBackImg(questParam[0]["backImg"],questParam[questCnt]["backImg"]);

            $('#magazine').turn('page',$('#magazine').turn('pages'));
        }*/

        PageViewMove();
    });

    $(".next-btn").click(()=>{
        pageCnt++;
        //最新クエスト又は最終ページで捜査したか
        if(questCnt >= questParam.length-1 || isLastPage()){
            btnCnt=1;
            questCnt=0;
            pageCnt=2;
            selectStoryIndex=0;

            SetQuestIdSession();
            ChangeBackImg(questParam.length-1,0);
            $('#magazine').turn('page',4);
        }else if(btnCnt >= questChapterPages[selectStoryIndex]){//章の最終クエストページで操作したか
            //次章へ
            btnCnt=0;
            selectStoryIndex++;
            //questCnt=QuestIdCounter(selectStoryIndex,0);

            ChangeBackImg(questCnt-1,questCnt);
            $('#magazine').turn('next');
        }else{
            btnCnt++;
            questCnt++;
            
            ChangeBackImg(questCnt-1,questCnt);
            SetQuestIdSession();
            $('#magazine').turn('next');
        }

        /*
        if(isLastPage()){
            //最初のクエストID=0のページへ
            btnCnt=1;
            pageCnt=2;
            questCnt=0;
            selectStoryIndex=0;

            ChangeBackImg(questParam[questParam.length-1]["backImg"],questParam[0]["backImg"]);
            $('#magazine').turn('page',4);

        }else if( btnCnt > questChapterPages[selectStoryIndex]){
            //次の章へ
            btnCnt=0;
            pageCnt++;
            
            selectStoryIndex++;
            $('#magazine').turn('next');
        }else{
            
            //btnCnt++;
            pageCnt++;
            questCnt++;
            
            SetQuestIdSession();
            if(questCnt < questParam.length && questCnt > 0)
                ChangeBackImg(questParam[questCnt-1]["backImg"],questParam[questCnt]["backImg"]);

            $('#magazine').turn('next');
        }*/

        PageViewMove();
    });

    function PageViewMove(){
        if(pageCnt <= 0 || isTitlePage() || isLastPage())
            $('.quest-start-btn').attr('hidden',true);
        else
            $('.quest-start-btn').attr('hidden',false);

        anime({
            targets: '.st0',
            duration: 2000,
            left: ((pageCnt+1)/pages)*100+"%" //moveX*pageCnt*2
        });
    }

    function isLastPage(){
        return ($('#magazine').turn('page') >= pages);
    }

    function isTitlePage(){
        for(var i=0; i < chapterCnt; i++){
            if( btnCnt === 0)
                return true;
        }
        return false;
    }

    function QuestIdCounter(chapter_index,offset){
        let sum=offset;
        for(var i=0; i< chapter_index; i++){
            sum += questChapterPages[i];
        }        
        return sum;
    }

    function SetQuestIdSession(){
        if(questCnt < 0)return;

        $.ajax({
        url:"./php/storyMenutest.php",
        type:"post",
        data:{
            panelId:questCnt
        },
        success:function(data){
        },
        error:(function(XMLHttpRequest, textStatus, errorThrown) {
        　　console.log("XMLHttpRequest : " + XMLHttpRequest.status);
        　　console.log("textStatus     : " + textStatus);
        　　console.log("errorThrown    : " + errorThrown.message);              
            })
        });
    }

    const duration=1000;

    function ChangeBackImg(bIndex,aIndex){
        if(bIndex < 0 || bIndex > questParam.length-1 || aIndex <0 || aIndex > questParam.length-1) return;

        $('.prev-btn').attr('hidden',true);
        $('.next-btn').attr('hidden',true);
        
        //透過度を交互に入れ替える
        const before='img/characters/'+questParam[bIndex]["backImg"];
        const after='img/characters/'+questParam[aIndex]["backImg"];

        if( parseInt($(".before-img").css('opacity')) < 1){

            ChangeOpacity('.after-img','.before-img',before,after);

        }else if(parseInt($(".after-img").css('opacity')) < 1){

            ChangeOpacity('.before-img','.after-img',before,after);
        }
        delayCall(duration/1000,()=>{
            $('.next-btn').attr('hidden',false);
            $('.prev-btn').attr('hidden',false);
        });
    }

    function ChangeOpacity(beforeSel,afterSel,beforeImg, afterImg){
        $(beforeSel+"-back").attr('src', beforeImg);
        $(afterSel+"-back").attr('src', afterImg);

        $(afterSel).animate({opacity: 1},{duration: duration});
        $(beforeSel).animate({opacity: 0},{duration: duration});
    }

    function delayCall(sec,callBack){
        setTimeout(callBack,sec*1000);
    }

    $(window).on("load",function(){
        GetQuestDate();
    });