var i = 0;
var timeset;

function loading () {
    $('.loadingbox ul').find('li').removeClass('active');
    $('.loadingbox ul').find('li:eq('+i+')').addClass('active');
    i++;
    if (i > 4) {
        i = 0;
        timeset = setTimeout("loading()", 500);
    } else {
        timeset = setTimeout("loading()", 500);
    }
}
loading();

function removeLoading () {
    clearTimeout(timeset);
    $('.page-loading').remove();
}

var thisurl = window.location.href;
if (thisurl.indexOf('dev.') > -1) {
    var domain = 'http://dev.qbb.qiwocloud1.com';
} else if (thisurl.indexOf('test.') > -1) {
    var domain = 'http://test.qbb.qiwocloud1.com';
} else {
    var domain = 'http://qbb.qiwocloud1.com';
}
var shareurl = domain + '/v1/api/diary/info';
var couturl  = domain + '/v1/api/diary/getUserThatLaudedDiary';


function getQueryString(name) { 
var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
var r = window.location.search.substr(1).match(reg); 
if (r != null) return unescape(r[2]); return null; 
}

function getContents() {
    var params = {};
    params['babyid'] = 1000000;
    params['did'] = getQueryString("did");

    $.ajax({
        type: "POST",
        url: shareurl,
        contentType: "application/json", //必须有
        dataType: "json", //表示返回值类型，不必须
        data: JSON.stringify(params),  //相当于 //data: "{'str1':'foovalue', 'str2':'barvalue'}",
        success: function (data) {
            if (data['_status']['_code'] == 200) {
                var rs = data.diary_list[0];
                var con = rs.detail;
                var users = rs.user;

                // 用户信息
                $('.share-top-pic img').attr('src', users['image']);
                $('header h3').html(users['nickname']);
                $('.share-addr').html(users['area']);
                $('.share-name').html(users['baby']['name']);
                $('.share-old').html(SetRemainTime(users['baby']['birthday'])); // 需转换成多大了
                $('.share-times span').html(formatDateFull(rs['d_time']));
                
                // 分享内容
                if (con['d_video_url']) {
                    //$('.share-main').html('<video id="tenvideo_video_player_0" x-webkit-airplay="true" webkit-playsinline="true" preload="none" src="'+con['d_video_url']+'"></video>');
                    $('.share-main').html('<video controls width="100%" poster="'+con['d_video_thumb_url']+'"><source src="'+con['d_video_url']+'" type="video/mp4" /></video>');
                } else {
                    $('.share-main').html('<img src='+con['d_img_list'][0]['img_url']+' />');
                }
                // 标签
                if (con['tags']) {
                    var tagshtml = '';
                    var windowwidth = $(window).width();
                    for (var i=0; i<con['tags'].length; i++) {
                        if (con['tags'][i]['coord']) {
                            var position = con['tags'][i]['coord'].split(',');
                            var sites = position[2] == 1 ? 'class="right"' : 'class="left"';
                            var vleft = (15 / windowwidth * 100) + Number(position[0]);
                            tagshtml += '<div '+sites+' style="left:'+vleft+'%; top:'+position[1]+'%;">'+con['tags'][i]['define']+'</div>';
                        }
                    }
                    $('.share-tags-main').html(tagshtml);
                }
                removeLoading();
            } else {
                getContents();
            }
        },
        error: function (msg) {
            if (msg.status == 500) {
                getContents();
            }
        }
    });
    
    getCount(params);
}

function getCount(params) {
    // 点赞
    params['diary_id'] = params['did'];
    params['page'] = 0;
    params['size'] = 8;
    $.ajax({
        type: "POST",
        url: couturl,
        contentType: "application/json", //必须有
        dataType: "json", //表示返回值类型，不必须
        data: JSON.stringify(params),  //相当于 //data: "{'str1':'foovalue', 'str2':'barvalue'}",
        success: function (data) {
            if (data['_status']['_code'] == 200) {
                var arr = data['data'];
                if (data['count'] > 0) {
                    var html = "";
                    for (var i = 0; i<arr.length; i++) {
                        html += '<li><img src="'+arr['image']+'" onerror="this.src=\'public/images/img-test-2.jpg\'" /></li>';
                    }
                    html += '<li>...</li><li><span>'+data['count']+'</span></li>';
                    $('.share-users ul').html(html);
                } else {
                    $('.share-users').hide();
                }
            } else {
                getCount(params);
            }
        },
        error: function (msg) {
            if (msg.status == 500) {
                getCount(params);
            }
        }
    });
}

$(function () {
    getContents();
});

// 格式化时间  年/月/日
function formatDateFull(now)   {
    now = now + '000';
    var tt=new Date(Number(now));
    var year = tt.getFullYear();
    var month = tt.getMonth()+1;//js从0开始取 
    var date = tt.getDate(); 
    return year+"年"+month+"月"+date+"日";
}

function SetRemainTime(times) {
    var html = '';
    
    var timestamp = Date.parse(times);

    // 宝宝出生日期
    var tt=new Date(times);
    var year = tt.getFullYear();
    var month = tt.getMonth()+1;//js从0开始取 
    var date = tt.getDate(); 
    
    // 今天
    var time = Date.parse(new Date());
    var t = new Date();
    var nowyear = t.getFullYear();
    var nowmonth = t.getMonth()+1;//js从0开始取 
    var nowdate = t.getDate(); 

    // 宝宝出生日期是否小于当前日期    
    var SysSecond = time - timestamp; 

    if (SysSecond < 0) {
        html = '您的宝宝还没出生哦';
    } else {
        var months  = (nowyear * 12 + nowmonth) - (year * 12 + month);  //计算宝宝出生到现在总共有多少个月
        var alldays = Math.floor((SysSecond / 3600) / 24); //计算天 

        var oldyear = Math.floor(months / 12);
        var oldmonth = months % 12; // 几个月
        var oldday;

        // 如果大于1岁
        if (oldyear >= 1) {
            html = oldyear + '岁';
        }
        
        // 如果大于1个月
        if (oldmonth >= 1) {
            // 两个日期相减，如果今天大于宝宝出生月份的日期，直接取几个月几天
            if (nowdate > date) {
                oldday = nowdate - date;
            } 
            // 如果两个日期相等，显示整月
            else if (nowdate < date) {
                oldmonth = oldmonth - 1;
                var d1 = monthDay(year,month) - date;
                oldday = d1 + nowdate;
            }
            html += oldmonth + '个月';
            if (nowdate != date) {
                html += oldday + '天';
            }
        } 
        // 小于1个月时，显示宝宝多少天
        else {
            oldday = nowdate - date;
            html += oldday + '天';
        }
    }
    return html;
} 

function monthDay(y,n) {
    var day = 30;
    if (n == 1 || n == 3 || n == 5 || n == 7 || n == 8 || n == 10 || n == 12) {
        day = 31;
    } else if (n == 2) {
        n = y % 4 == 0 ? 29 : 28;
    }
    return day;
}