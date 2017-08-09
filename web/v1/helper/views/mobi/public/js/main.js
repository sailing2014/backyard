$(function () {
    if ($(window).height() > $(document.body).height()) {
        $(document.body).height($(window).height());
    }
    loading();
});

var v_count = 0;
function v_rotate() {
    var elem3 = document.getElementById('video-div3');
    if (elem3) {
        elem3.style.MozTransform = 'scale(0.5) rotate('+v_count+'deg)';
        elem3.style.WebkitTransform = 'scale(0.5) rotate('+v_count+'deg)';
        if (v_count==360) { v_count = 0 }
        v_count+=45;
    }
    window.setTimeout(v_rotate, 100);
}

function setClick(){
    var handle = arguments[0];
    var title  = arguments[1];
    var param1 = arguments[2];
    var header = arguments[3];
    var reload = arguments[4];
//    console.log(handle,title,param1,header,reload);
    var browserName=navigator.userAgent;
    if (/(iphone|ipad|ipod|ios)/i.test(browserName)) {
        if(handle == 'href' || handle == 'supply'){
            if(param1.indexOf('http') < 0){
                param1 = 'http://' + document.domain + param1;
            }
//            alert(param1);
        }
        iosclick(handle,title,param1,header,reload);
    } else if (/(android)/i.test(browserName)) {
        if(handle == 'href' || handle == 'supply'){
            if(param1.indexOf('http') < 0){
                param1 = 'http://' + document.domain + param1;
            }
        }
        //alert('Android:[handle:'+handle+']\n[title:'+title+'][param1:'+param1+'][header:'+header+'][reload:'+reload+']');
        window.bbcare_android.onTestClick(handle,title,param1,header,reload);
    } else {
        if(handle == 'href' || handle == 'supply'){
            window.location.href = param1;
        }
    };
}

/**
 * @param element 区块
 * @param header 头部是否允许显示
 * @param reload 页面是否允许刷新
 */
function setEvent(){
    //console.log(arguments);
    var element = arguments[0];
    var header,reload;
    if(arguments[1] != undefined){
        header = arguments[1];
    }else{
        header = "1";
    }
    if(arguments[2] != undefined){
        reload = arguments[2];
    }else{
        reload = "1";
    }
    //console.log(element,header,reload);
    $(element).each(function(i,e){
        var title,
            title0 = $(e).data('title');
        $(e).on('click','a',function(){
            var self = $(this),
                href = self.attr('href'),
                title1 = self.data('title'),
                title2 = self.text().trim();
            //console.log(title1,title2);
            if(title0 != undefined){
                title = title0;
            }else if(title1 != undefined){
                title = title1;
            }else{
                title = title2;
            }
            //console.log(title1,title2,title);
            $( /#|javascript:;|\s/.test(href)==false )
            {
                setClick('href',title,href,header,reload);
                //console.log('href',title,href);
            }
            return false;
        });
    });
}

function setSupply(){
    //console.log(arguments);
    var element = arguments[0];
    var header,reload;
    if(arguments[1]!=undefined){
        header = arguments[1];
    }else{
        header = "1";
    }
    if(arguments[2]!=undefined){
        reload = arguments[2];
    }else{
        reload = "1";
    }
    //console.log(element,header,reload);
    $(element).each(function(i,e){
        $(e).on('click','a',function(){
            var self = $(this),
                href = self.attr('href');
            $( /#|javascript:;|\s/.test(href)==false )
            {
                setClick('supply','',href,header,reload);
            }
            return false;
        });
    });
}

// loading 效果
var count = 0;	// loading 效果定义变量
function loading () {
    //var loadingHTML = '<style>.loading{ position:absolute; width:100%; height:100%; left:0; top:0; background:rgba(0,0,0,.8);} #loading { position:absolute; left:50%; top:50%; width:50px; height:50px; margin:-25px 0 0 -25px; overflow:hidden;}</style><div class="loading"><div id="loading"><img src="public/images/min/circle.png"></div></div>';
    //$('.index-video-ready').append(loadingHTML);
    //window.setTimeout(rotate, 100);
    //$('.loading').show();
    window.setTimeout(rotate, 100);
}
function rotate() {
    var elem4 = document.getElementById('loading');
    if (elem4) {
    elem4.style.WebkitTransform = 'scale(0.5) rotate('+count+'deg)';
    if (count==360) { count = 0 }
    count+=45;
    }
    window.setTimeout(rotate, 100);
}

