// 页面加载效果所需
var m = 0;
var timeset;
var localurl = window.location.href;
var babydata_flag = false;

// get params
var uid         = getcookie('uid');
var entity_id   = getcookie('entity_id');
var token       = getcookie('token');
var gid         = getcookie('gid');

// test params
//var uid         = 10000034;
//var entity_id   = 45;
//var token       = "sess::760d55f3fde40e8b1f03b3c0f950ca8a";

//var sss = "<div style=' width:100%; padding:10px; border:1px solid #F00;'>"+uid+" ===== "+entity_id+" ===== "+gid+" ===== "+token+" ===== </div>";
//$("body").append(sss);

var forms = {};
forms.uid       = uid;
forms.entity_id = entity_id;
forms.token     = token;
forms.gid       = gid;

if (localStorage.getItem('babydata_flag_' + entity_id)) {
    babydata_flag = localStorage.getItem('babydata_flag_' + entity_id);
} else {
    babydata_flag = false;
    localStorage.setItem('babydata_flag_' + entity_id, babydata_flag);
}

function get_suggestionId (con) {
    var data = con ? con : data_type;
    var suggestionId = 24;
    if (localurl.indexOf('dev.') > -1) {
        switch (data) {
            case 1:             // 室温
                break;
            case 2:             // 湿度
                break;
            case 3:
                suggestionId = 150;   // 空气
                break;
            case 4:
                suggestionId = 149;   // 体重
                break;
            case 5:
                suggestionId = 148;   // 身高
                break;
            case 6:             // 体温
                break;
            case 7:             // 心跳
                break;
        }
    } else if (localurl.indexOf('test.') > -1) {
        switch (data) {
            case 1:             // 室温
                break;
            case 2:             // 湿度
                break;
            case 3:
                suggestionId = 131;   // 空气
                break;
            case 4:
                suggestionId = 130;   // 体重
                break;
            case 5:
                suggestionId = 129;   // 身高
                break;
            case 6:             // 体温
                break;
            case 7:             // 心跳
                break;
        }
    } else {
        switch (data) {
            case 1:             // 室温
                break;
            case 2:             // 湿度
                break;
            case 3:
                suggestionId = 236;   // 空气
                break;
            case 4:
                suggestionId = 235;   // 体重
                break;
            case 5:
                suggestionId = 234;   // 身高
                break;
            case 6:             // 体温
                break;
            case 7:             // 心跳
                break;
        }
    }
    return suggestionId;
}
var thisid = get_suggestionId();

// set api

// local
/*
var getsingle         = 'code/getbaby.php';
var getchart          = 'code/get_baby_chart.php';
var setismanualheight = 'code/setheight.php';
var getismanualheight = 'code/getheight.php';
var getentity         = 'code/getentity.php';
var setisunderarm     = 'code/setisunderarm.php';
var getisunderarm     = 'code/getisunderarm.php';
var selectheight      = 'code/selectheight.php';
var getcontentbytags  = 'code/getcontentbytags.php';
var getsuggestion     = 'code/get_suggestion.php?catid={thisid}';
var getalldata        = 'code/getalldata.php';
*/

// severs
var getsingle         = '/v1/helper/index.php?s=mobi&c=babydata&a=getsingle';
var getchart          = '/v1/helper/index.php?s=mobi&c=babydata&a=getchart';
var setismanualheight = '/v1/helper/index.php?s=mobi&c=babydata&a=setismanualheight';
var getismanualheight = '/v1/helper/index.php?s=mobi&c=babydata&a=getismanualheight';
var getentity         = '/v1/helper/index.php?s=mobi&c=babydata&a=getentity';
var setisunderarm     = '/v1/helper/index.php?s=mobi&c=babydata&a=setisunderarm';
var getisunderarm     = '/v1/helper/index.php?s=mobi&c=babydata&a=getisunderarm';
var selectheight      = '/v1/helper/index.php?s=mobi&c=babydata&a=selectheight';
var getcontentbytags  = '/v1/helper/?s=api&c=tags&a=getcontentbytags';
var getsuggestion     = '/v1/helper/api.php?c=helper&a=getlist&catid='+thisid+'&page=1&pagesize=4';
var getalldata        = '/v1/helper/index.php?s=mobi&c=babydata&a=getalldata';
var notifydata        = '/v1/helper/index.php?s=mobi&c=babydata&a=notifydata';


// 设置表格参数
var d1 = [];    // 曲线参数：  [ 日期 ]
var tableData = [];    // 曲线参数：  [ 数值 ]
var listdate = "";
var color = "#3dbaf4";
var morelink = '#';
var units = '';
var titles = '';
var series = '';
var icon = '';

function getUnit(con) {
    var data = con ? con : data_type;
    var rs = {};
    switch (data) {
        case 1:
            rs.units = '℃';
            rs.titles = '最近室温数据';
            rs.series = '室温';
            rs.icon = 'icon-temp';
            break;
        case 2:
            rs.units = '%';
            rs.titles = '最近湿度数据';
            rs.series = '湿度';
            rs.icon = 'icon-moisture';
            break;
        case 3:
            rs.units = '';
            rs.titles = '最近空气数据';
            rs.series = '空气';
            rs.icon = 'icon-air';
            break;
        case 4:
            rs.units = 'kg';
            rs.titles = '最近体重数据';
            rs.series = '体重';
            rs.icon = 'icon-weight';
            break;
        case 5:
            rs.units = 'cm';
            rs.titles = '最近身高数据';
            rs.series = '身高';
            rs.icon = 'icon-height';
            break;
        case 6:
            rs.units = '℃';
            rs.titles = '最近体温数据';
            rs.series = '体温';
            rs.icon = 'icon-babyheat';
            break;
        case 7:
            rs.units = '次/分';
            rs.titles = '最近心跳数据';
            rs.series = '心跳';
            rs.icon = 'icon-heartbeat';
            break;
        default:
            rs.units = '';
            rs.titles = '';
            rs.series = '';
            rs.icon = '';
            break;
    }
    return rs;
}
var arr = getUnit();
units    = arr.units;
titles   = arr.titles;
series   = arr.series;
icon     = arr.icon;

// 读取本地缓存数据
var obj_single         = localStorage.getItem("single_" + entity_id + "_" + data_type);   // 宝宝单个数据
var obj_chart          = localStorage.getItem("chart_" + entity_id + "_" + data_type);    // 宝宝多条数据
var obj_recommend      = localStorage.getItem("recommend_" + series);   // 推荐阅读
var obj_suggestion     = localStorage.getItem("suggestion_" + thisid);  // 健康建议
var obj_get_height     = localStorage.getItem("is_get_height_" + entity_id);         // 取得是否接受设备上传身高
var obj_get_underarm   = localStorage.getItem("is_underarm_" + entity_id);           // 取得是否腋下状态
var obj_get_alarm_time = localStorage.getItem("alarm_time_" + entity_id);            // 取得提醒时间
    
$(function () {
	if ($(window).height() > $(document.body).height()) {
		$(document.body).height($(window).height());
	}
    
    if (data_type == 6) {
        var showtop = $(window).height() - 60;
        $('.close-wxd').css({"top": showtop + "px"});
    }
    
    if (localurl.indexOf('select-height') == -1) {
        if (data_type == 5) {
            if (obj_get_height != 1) {
                switch_handle($('.heav .switch'), 1);
            }
        }
        
        // 加载缓存
        loadCache();
    
        // 加载当前页内容
        fun_getsingle();
        fun_getchart();
        fun_recommend(series);
        fun_suggestion(thisid);
        if (data_type == 6) { fun_getisunderarm();}
    }

    // 上传身高
    $('.saveheight').click(function () {
        forms.value = sessionStorage.getItem('newheight') * 10;
        $('.saving').removeClass('hidden');
        fun_selectheight();
    });

    // 查看历史记录
    $('.history a').click(function () {
        showhistory();
        $('.filter').click(function (event) {
            hidehistory();
        });
    });
    
    // 身高开关
    $('.heav .switch').click(function () {
        var sw = $(this);
        if (sw.hasClass('switch-on')) {
            switch_handle(sw);
            $('.heightcolor').addClass('hidden');
            $('.recordheight').removeClass('hidden');
            $('.notice-height').addClass('hidden');
            $('.switch').removeClass('pull-right');
            $('.switch').addClass('mgauto');
        } else {
            switch_auto(sw);
        }
    });
    
        
    // 关闭体温监测
    $('.moudle-btn a').click(function () {
        $('.surface').removeClass('hidden');
        $('.oxter').addClass('hidden');
        forms.is_underarm = 0;
        localStorage.setItem('is_underarm_' + entity_id, forms.is_underarm);
        fun_setisunderarm(forms);
    });
    
    // 体温报警开关
    $('.warn .switch').click(function () {
        var sw = $(this);
        obj_get_underarm     = localStorage.getItem("is_underarm_" + entity_id);
        forms.is_underarm = obj_get_underarm;
        if (sw.hasClass('switch-on')) {
            showWarnSet();
            forms.alarm_time = 60*60;   // 点击就默认一小时提醒
            $('.warn-time').html('1小时');
        } else {
            hideWarnSet();
            $('.warn .switch').addClass('switch-on');
            $('.warn .switch').removeClass('switch-off');
            $('.warn-time').html('30分钟');
            forms.alarm_time  = 60*30;
        }
        fun_setisunderarm(forms);
        
        $('.warn-box .filter').click(function () {
            hideWarnSet();
        });
    });
    
    // 手动选择提醒时间
    $('.warn-set').find('li').click(function () {
        $('.warn-set').find('li').removeClass('active');
        $('.warn-time').html($(this).find('span').html());
        
        forms.is_underarm = obj_get_underarm;
        
        if($(this).find('span').html() == '1小时') {
            forms.alarm_time = 60*60;
            fun_setisunderarm(forms);
        } else if ($(this).find('span').html() == '2小时') {
            forms.alarm_time = 60*60*2;
            fun_setisunderarm(forms);
        } else {
            forms.alarm_time = 30*60;
            fun_setisunderarm(forms);
            warn_open(forms);
        }
        
        $(this).addClass('active');
    });
    
    // 显示温心带
    $('.switch-helper').click(function () {
        showwxd();
    });
    
    // 打开体温监测，显示体温测量帮助
    $('.circle-border').click(function () {
        showhelper();
        
    });
    
    // 关闭体温测量帮助
    $('.close-helper').click(function () {
        hidehelper();
        $('.surface').addClass('hidden');
        $('.oxter').removeClass('hidden');
        forms.is_underarm = 1;
        localStorage.setItem('is_underarm_' + entity_id, 1);
        fun_setisunderarm(forms);
        beginCount(60*5);
    });
    
    // 获取设备信息
    getNewRecords();
});

// 自动
function switch_auto (obj) {
    obj.removeClass('switch-off');
    obj.addClass('switch-on');
    obj.find('i').html('自动');
    $('.heightcolor').removeClass('hidden');
    $('.recordheight').addClass('hidden');
    $('.notice-height').html('宝宝身高超过70cm，可选择手动输入');
    $('.notice-height').removeClass('hidden');
    $('.switch').addClass('pull-right');
    $('.switch').removeClass('mgauto');
    fun_setismanualheight("1");
}

// 手动
function switch_handle (obj, notsend) {
    obj.removeClass('switch-on');
    obj.addClass('switch-off');
    obj.find('i').html('手动');
    $('.notice-height').html($('.recordheight').html());
    if (notsend != 1) {
        fun_setismanualheight("0");
    }   
}

/*
 * loading效果
*/
function loading () {
    $('.loadingbox ul').find('li').removeClass('active');
    $('.loadingbox ul').find('li:eq('+m+')').addClass('active');
    m++;
    if (m > 4) {
        m = 0;
        timeset = setTimeout("loading()", 500);
    } else {
        timeset = setTimeout("loading()", 500);
    }
}
loading();

/*
 * 获取cookie
*/
function getcookie(objname){
    var arrstr = document.cookie.split("; ");
    for(var i = 0;i < arrstr.length;i ++){
        var temp = arrstr[i].split("=");
        if(temp[0] == objname) { 
            return unescape(temp[1]);
        }
    }
}

// 删除loading效果
function removeLoading () {
    if (data_type == 6) {
        if (is_getsingle && is_getchart && is_recommend && is_suggestion && is_getisunderarm) {
            $('.page-loading').remove();
            addhtml();
            loadAllGet();
        }
    } else {
        if (is_getsingle && is_getchart && is_recommend && is_suggestion) {
            $('.page-loading').remove();
            addhtml();
            loadAllGet();
        }
    }
}

// 加载其他页面内容
function loadAllGet() {
    if (!babydata_flag || babydata_flag == 'false') {
        is_getsingle         = false;
        is_getchart          = false;
        is_recommend         = false;
        is_suggestion        = false;
        is_getismanualheight = false;
        fun_getalldata();
    }
}

function loadCache() {
    if (data_type == 6) {
        if (obj_single && obj_chart && obj_recommend && obj_suggestion && obj_get_underarm) {
            $('.page-loading').remove();
            addhtml();
        }
    } else {
        if (obj_single && obj_chart && obj_recommend && obj_suggestion) {
            $('.page-loading').remove();
            addhtml();
        }
    }
}

/*
 * 加载数据的方法
*/

// 宝宝单个数据
var is_getsingle = false;
var isingle_i = 0;
function fun_getsingle(con, status, overload) {
    forms.data_type = con ? con : data_type;
    $.post(getsingle, forms, function (data) {
        var records = JSON.stringify(data);
        is_getsingle = true;
        if (records.length > 5) {
            localStorage.setItem("single_" + entity_id + "_" + forms.data_type, records);
        }
        if (status != 1) {
            removeLoading();
        }
        if (overload == 1 && con != undefined) {
            fun_getalldata();
        }
    });
}
//fun_getsingle();

// 宝宝多条数据
var is_getchart = false;
var chart_i = 0;
function fun_getchart(con, status, overload) {
    forms.data_type = con ? con : data_type;
    $.post(getchart, forms, function (data) {
        var records = JSON.stringify(data);
        is_getchart = true;
        if (records.length > 5) {
            localStorage.setItem("chart_" + entity_id + "_" + forms.data_type, records);
        }
        if (status != 1) {
            removeLoading();
        }
        if (overload == 1) {
            fun_getalldata();
        }
    });
}
//fun_getchart();

// 推荐阅读
var is_recommend = false;
function fun_recommend (tags, status, overload) {
    // local
    /*
    $.post(getcontentbytags, function (data) {
        is_recommend = true;
        localStorage.setItem("recommend_" + tags, data);
        if (status != 1) {
            removeLoading();
        }
        if (overload == 1) {
            fun_getalldata();
        }
    });
    */
    
    // 服务器读取数据方法
    var params = {'tags': tags};
    $.ajax({
        type: "POST",
        url: getcontentbytags,
        contentType: "application/json", //必须有
        dataType: "json", //表示返回值类型，不必须
        data: JSON.stringify(params),  //相当于 //data: "{'str1':'foovalue', 'str2':'barvalue'}",
        success: function (data) {
            is_recommend = true;
            localStorage.setItem("recommend_" + tags, JSON.stringify(data));
            if (status != 1) {
                removeLoading();
            }
            if (overload == 1) {
                fun_getalldata();
            }
        }
    });
}
//fun_recommend("牛奶");

// 健康建议
var is_suggestion = false;
function fun_suggestion (thisid, status, overload) {
    getsuggestion = getsuggestion.replace('{thisid}', thisid);
    $.get(getsuggestion, function (data) {
        is_suggestion = true;
        localStorage.setItem("suggestion_" + thisid, JSON.stringify(data)); // 本地存储
        if (status != 1) {
            removeLoading();
        }
        if (overload == 1) {
            fun_getalldata();
        }
    });
}
//fun_suggestion();

// 设置是否接受设备上传身高
var is_setismanualheight = false;
function fun_setismanualheight(value) {
    forms.is_auto = value;
    localStorage.setItem('is_get_height_' + entity_id, forms.is_auto);
    $.post(setismanualheight, forms, function (data) {
        is_setismanualheight = true;
    });
}

// 取得是否接受设备上传身高
var is_getismanualheight = false;
function fun_getismanualheight () {
    $.post(getismanualheight, forms, function (data) {
        is_getismanualheight = true;
        localStorage.setItem('is_get_height_' + entity_id, data);
    });
}

// 设置当前是否腋下体温模式
var is_setisunderarm = false;
function fun_setisunderarm (forms) {
    localStorage.setItem('is_underarm_' + entity_id, forms.is_underarm);
    localStorage.setItem('alarm_time_' + entity_id, forms.alarm_time);
    $.post(setisunderarm, forms, function (data) {
        is_setisunderarm = true;
    });
}

// 取得当前是否腋下体温模式
var is_getisunderarm = false;
function fun_getisunderarm (status, overload) {
    $.post(getisunderarm, forms, function (data) {
        is_getisunderarm = true;
        var is_underarm = data.is_underarm ? data.is_underarm : 0;
        var alarm_time = data.alarm_time ? data.alarm_time : 60*30;
        localStorage.setItem('is_underarm_' + entity_id, is_underarm);
        localStorage.setItem('alarm_time_' + entity_id, alarm_time);
        if (status != 1) {
            removeLoading();
        }
        if (overload == 1) {
            fun_getalldata();
        }
    });
}

// 上传身高数据
function fun_selectheight () {
    forms.value = sessionStorage.getItem('newheight') * 10;
    
    // 更新本地缓存
    var arr_data = JSON.parse(obj_single);
    if (arr_data) {
        arr_data.value = sessionStorage.getItem('newheight');
        var json_data = JSON.stringify(arr_data);
        localStorage.setItem("single_" + entity_id + "_" + data_type, json_data);
    }
    $.post(selectheight, forms, function (data) {
        window.location.href = "height.html";
        //fun_setismanualheight("0", 1); // 上传成功后，重新设置身高为自动获取
        //fun_getsingle();
    });
}

// 从设备拉数据到云服务器
function fun_notifydata () {
    $.post(notifydata, forms, function (data) {});
}

// 渲染HTML
function addhtml() {
    obj_single         = localStorage.getItem("single_" + entity_id + "_" + data_type);     // 宝宝单个数据
    obj_chart          = localStorage.getItem("chart_" + entity_id + "_" + data_type);      // 宝宝多条数据
    obj_recommend      = localStorage.getItem("recommend_" + series);                       // 推荐阅读
    obj_suggestion     = localStorage.getItem("suggestion_" + thisid);                      // 健康建议
    obj_get_height     = localStorage.getItem("is_get_height_" + entity_id);                // 取得是否接受设备上传身高
    obj_get_underarm   = localStorage.getItem("is_underarm_" + entity_id);                  // 取得是否腋下状态
    obj_get_alarm_time = localStorage.getItem("alarm_time_" + entity_id);                   // 取得提醒时间
    obj_update_height  = localStorage.getItem("update_height");                             // 取得上传的身高
    
    var arr_single;
    var arr_chart;
    var arr_recommend;
    var arr_suggestion;
    try {
        arr_single     = JSON.parse(obj_single);
        arr_chart      = JSON.parse(obj_chart);
        arr_recommend  = JSON.parse(obj_recommend);
        arr_suggestion = JSON.parse(obj_suggestion);
    }catch (e) {
        if (localurl.indexOf('select-height') < 0) {
            // 加载当前页内容
            fun_getsingle();
            fun_getchart();
            fun_recommend(series);
            fun_suggestion(thisid);
            if (data_type == 6) { fun_getisunderarm();}
        }
    }
    // 单条数据
    var datavalue = 0;
    var datarange = 0;
    if (arr_single) {
        datavalue = arr_single.value == undefined ? 0 : arr_single.value;
        datarange = arr_single.normal_range == undefined ? 0 : arr_single.normal_range;
    }
    if (data_type == 3) {
        $('.heav-body h3').html(fomatAir(datavalue));
    } else {
        $('.heav-body h3').html(datavalue + '<sup>'+units+'</sup>');
        $('.heav-body p').html(datarange + units);
        if ((data_type == 7 && (datavalue > 200 || datavalue == 0)) || (data_type == 6 && (datavalue == 31 || datavalue == 0))) {
            $('.heav-body h3').html('<font style="font-size:60px">预热中</font>');
        }
    }
    
    if (data_type == 5) {
        if (sessionStorage.getItem('newheight')) {
            $('.heav-body h3').html(sessionStorage.getItem('newheight') + '<sup>'+units+'</sup>');
            sessionStorage.setItem('newheight', '')

        }
        $('.recordheight a').attr('href', 'select-height.html?val=' + datavalue);
        if (obj_get_height != 1) {
            $('.notice-height a').attr('href', 'select-height.html?val=' + datavalue);
        }
    }
    
    if (arr_single) {
        $('.heav-body .notice').html(arr_single.tips);
    
        if (arr_single.is_unnormal != 1) {
            $('.heav-body .notice').addClass('normal');
        } else {
            // 显示成长建议
            if(data_type == 3 || data_type == 4 || data_type == 5){
                $('.suggestion').removeClass('hidden');
            }
        }
    }

    if (data_type == 6) {
        // 体温模块，根据状态判断显示内容
        if (obj_get_underarm == 1) {
            $('.surface').addClass('hidden');
            $('.oxter').removeClass('hidden');
        } else {
            $('.surface').removeClass('hidden');
            $('.oxter').addClass('hidden');
        }

        //判断警报开关
        if (obj_get_alarm_time) {
            if (obj_get_alarm_time == 7200) {
                $('.warn-time').html('2小时');
                warn_close($('.warn .switch'));
            } else if (obj_get_alarm_time == 3600) {
                $('.warn-time').html('1小时');
                warn_close($('.warn .switch'));
            } else {
                $('.warn-time').html('30分钟');

                warn_open($('.warn .switch'));
            }
        }
    }

    // 成长建议
    if (arr_suggestion) {
        if (arr_suggestion['status']['code'] == 200) {
            var li_on;
            var sgtit = '';
            var sgcon = '';
            for (var i=0; i<arr_suggestion['data'].length; i++) {
                if (i == 0) {
                    li_on = ' class="on"';
                } else {
                    li_on = '';
                }
                sgtit += '<li'+ li_on +'><a href="javascript:;">'+arr_suggestion['data'][i]['title']+'</a></li>';
                sgcon += '<div class="con">'+arr_suggestion['data'][i]['content']+'</div>';
            }
            $('#tabBox1 .hd ul').html(sgtit);
            $('#tabBox1 .bd').html(sgcon);
            $(".con").width($(window).width() - 40);
            TouchSlide( { slideCell:"#tabBox1"});
        }
    }
    
    // 推荐阅读
    if (arr_recommend) {
        if (arr_recommend['status']['code'] == 200) {
            var recList = arr_recommend['data'];
            var reccon = '';
            var conlength = recList.length > 4 ? 4 : recList.length;
            for (var i=0; i<conlength; i++) {
                reccon += '<li><a href="'+recList[i]['url']+'"><div class="helpmore-img"><img src="'+recList[i]['thumb']+'" onerror="this.src=\'public/images/img-test-2.jpg\'"></div><h4>'+recList[i]['title']+'</h4></a></li>';
            }
            $('.rec-list ul').html(reccon);
        }
    }
    
    // 多条数据
    if (JSON.stringify(arr_chart).length > 5) {
        // 处理表格数据
        var maxY = 0;
        var minY = 0;
        
        // 重置变量
        d1 = [];
        tableData = [];
        listdate = "";
        
        arr_chart = arr_chart.reverse();
        var j = arr_chart.length - 1;
        var strlimit = '';
        if ($('.heav-body p span').html() != undefined) {
            strlimit = $('.heav-body p span').html().split('~');
        }
        for (var i=0; i<arr_chart.length; i++) {
            
            // 图表模式数据
            d1.push(formatDate(arr_chart[i][0]));
            
            if (data_type == 6) {
                var rs = [];
                rs.push(formatDate(arr_chart[i][0]));
                rs.push(arr_chart[i][1]['min']);
                rs.push(arr_chart[i][1]['max']);
                tableData.push(rs);
            } else {
                tableData.push(arr_chart[i][1]);
            }
            
            // 列表模式数据
            var minlimit = '';
            var maxlimit = '';
            var minnotice = '';
            var maxnotice = '';
            if (data_type == 3) {
                if (arr_chart[j][1] > 1) {
                    minlimit = '<i class="glyphicon glyphicon-exclamation-sign"></i>';
                    minnotice = ' style="color:#f00;"';
                }
                listdate += '<li><span class="pull-left">'+formatDateFull(arr_chart[j][0])+'</span><p class="pull-right" ' + minnotice + '>'+ minlimit + fomatAir(arr_chart[j][1])+'<sub>'+units+'</sub></p></li>';
            } else if (data_type == 6) {
                if (strlimit) {
                    // 得出最大值和最小值
                    if (arr_chart[i][1]['max'] > maxY) {
                        maxY = arr_chart[i][1]['max'];
                    }
                    if (arr_chart[i][1]['min'] < minY || minY == 0) {
                        minY = arr_chart[i][1]['min'];
                    }
                    if (arr_chart[i][1]['max'] > strlimit[1] || arr_chart[i][1]['max'] < strlimit[0]) {
                        maxlimit = '<i class="glyphicon glyphicon-exclamation-sign"></i>';
                        maxnotice = ' style="color:#f00;"';
                    }
                    if (arr_chart[i][1]['min'] > strlimit[1] || arr_chart[i][1]['min'] < strlimit[0]) {
                        minlimit = '<i class="glyphicon glyphicon-exclamation-sign"></i>';
                        minnotice = ' style="color:#f00;"';
                    } 
                }
                listdate += '<li><span class="pull-left">'+formatDateFull(arr_chart[j][0])+'</span><div class="pull-right"><p ' + minnotice + '>'+ minlimit + arr_chart[i][1]['min']+'<sub>'+units+'</sub></p><p ' + maxnotice + '>' + maxlimit + arr_chart[i][1]['max']+'<sub>'+units+'</sub></p></div></li>';
            } else {
                if ($('.heav-body p span').html() != undefined) {
                    // 得出最大值和最小值
                    if (arr_chart[i][1] > maxY) {
                        maxY = arr_chart[i][1];
                    }
                    if (arr_chart[i][1] < minY || minY == 0) {
                        minY = arr_chart[i][1];
                    }
                    if (arr_chart[j][1] > strlimit[1] || arr_chart[j][1] < strlimit[0]) {
                        minlimit = '<i class="glyphicon glyphicon-exclamation-sign"></i>';
                        minnotice = ' style="color:#f00;"';
                    }
                }
                listdate += '<li><span class="pull-left">'+formatDateFull(arr_chart[j][0])+'</span><p class="pull-right" ' + minnotice + '>'+ minlimit + arr_chart[j][1]+'<sub>'+units+'</sub></p></li>';
            }
            j--;
        }
        var controlY = setTickets (minY, maxY);
        
        // 数据塞入表格
        var tithtml = '<div class="his-tit"><a href="javascript:;" class="pull-right" onclick="checkview(this)">看列表</i></a><i class="icon-table '+icon+'"></i>'+titles+'</div>';
        // 图表
        var pichtml = '<div class="demo-container"><span class="unit">'+units+'</span><span class="setmonths"></span><div id="placeholder" class="demo-placeholder"></div></div>';
        // 列表
        var listhtml = '<div class="demo-list hidden"><ul>' + listdate + '</ul><div class="clearfix"></div></div>';

        var sethtml = tithtml + pichtml + listhtml;
        $('.show-history').html(sethtml);
        
        if (data_type == 3) {
            $('#placeholder').highcharts({
                chart:{height:240},
                title: {
                    text: "　"
                },
                xAxis: {
                    tickmarkPlacement: 'on',
                    gridLineWidth: 1,//默认是0，即在图上没有纵轴间隔线  
                    gridLineDashStyle: 'dash',
                    categories: d1
                },
                legend: {
                    enabled: false
                },
                yAxis: {
                    reversed: true,
                    title: false,
                    tickInterval:1,
                    labels: {
                        formatter:function(){
                            return fomatAir(this.value);

                        }
                    }
                },
                tooltip: {
                    formatter: function () {
                        var s = '<span style="font-size: 10px">' + this.x + '</span>';
                        $.each(this.points, function () {
                            s += '<br/><span style="fill:#7cb5ec" x="8" dy="15">●</span>';
                            s += '<span dx="0"> ' + this.series.name + ': </span>';
                            s += '<span style="font-weight:bold" dx="0">' + fomatAir(this.y) + '</span>';
                        });
        
                        return s;
                    },
                    shared: true
                },
                credits: {
                    text : '',
                    href : '',
                },
                series: [{
                    name: series,
                    data: tableData
                }]
            });
        } else if (data_type == 6) {
            $('#placeholder').highcharts({
                title: {
                    text: "　"
                },
                chart: {
                    type: 'arearange',
                    zoomType: 'x',
                    height:240
                },
                yAxis: {
                    title: {
                        text: null
                    },
                    max:controlY['maxY'], // 定义Y轴 最大值  
                    min:controlY['minY'], // 定义最小值  
                    tickInterval:controlY['tick'], // 刻度值  
                    tickLength: "1px"
                },
                xAxis: {
                    tickmarkPlacement: 'on',
                    gridLineWidth: 1,
                    gridLineDashStyle: 'dash',
                    categories: d1
                },
                tooltip: {
                    crosshairs: true,
                    shared: true,
                    valueSuffix: units
                },
                legend: {
                    enabled: false
                },
                credits: {
                    text : '',
                    href : '',
                },
                series: [{
                    name: series,
                    data: tableData
                }]
        
            });
        } else {
            $('#placeholder').highcharts({
                chart:{height:240},
                title: {
                    text: "　"
                },
                xAxis: {
                    tickmarkPlacement: 'on',
                    gridLineWidth: 1,//默认是0，即在图上没有纵轴间隔线  
                    gridLineDashStyle: 'dash',
                    categories: d1
                },
                legend: {
                    enabled: false
                },
                yAxis: {
                    title: false,
                    max:controlY['maxY'], // 定义Y轴 最大值  
                    min:controlY['minY'], // 定义最小值  
                    tickInterval:controlY['tick'], // 刻度值  
                    tickLength: 1
                },
                tooltip: {
                    valueSuffix: units
                },
                credits: {
                    text : '',
                    href : '',
                },
                series: [{
                    name: series,
                    data: tableData
                }]
            })
        }
        
        $('.history').show();
    }
}

/*
 * 功能函数
*/

// 格式化时间  月-日
function formatDate(times)   {    
    var now=new Date(times);      
    var month=now.getMonth()+1;
    var date=now.getDate();
    return month+"-"+date;
}

// 格式化时间  年/月/日
function formatDateFull(now)   {   
    var tt=new Date(now).toLocaleString().replace(/年|月/g, "/").replace(/日/g, " ").split(" ");
    return tt[0];
}

// 设置表格上限，下限，格数

function setTickets (minNum, maxNum) {
    var arr = []
    arr['minY'] = Math.floor(minNum);
    arr['maxY'] = Math.ceil(maxNum);
    arr['tick'] = (arr['maxY'] - arr['minY']) / 2;
    return arr;
}

// 获得当前月份中文形式
function getChinaMonth()
{
     var date=new Date;
     var month=date.getMonth()+1;
     var cm;
     switch (month) {
        case 1:
            cm = '一月';
            break;
        case 2:
            cm = '二月';
            break;
        case 3:
            cm = '三月';
            break;
        case 4:
            cm = '四月';
            break;
        case 5:
            cm = '五月';
            break;
        case 6:
            cm = '六月';
            break;
        case 7:
            cm = '七月';
            break;
        case 8:
            cm = '八月';
            break;
        case 9:
            cm = '九月';
            break;
        case 10:
            cm = '十月';
            break;
        case 11:
            cm = '十一月';
            break;
        case 12:
            cm = '十二月';
            break;
     }
     return cm;
}

// 隐藏历史记录
function hidehistory () {
    $('body').removeClass('notouch');
	$('.filter-box').addClass('hidden');
    $('.show-history').addClass('hidden');
}

// 显示历史记录
function showhistory () {
    $('body').addClass('notouch');
	$('.filter-box').removeClass('hidden');
    $('.show-history').removeClass('hidden');
}

// 切换图片，列表显示模式
function checkview (obj) {
    var piccon = $('.demo-container');
    var listcon = $('.demo-list');
    if (piccon.hasClass('hidden')) {
        // 看图表
        listcon.addClass('hidden');
        piccon.removeClass('hidden');
        $(obj).html('看列表');
    } else {
        // 看列表
        piccon.addClass('hidden');
        listcon.removeClass('hidden');
        $(obj).html('看图表');
    }
}

// 重置空气等级
function fomatAir(v) {
    var n;
    if (v > 0) {
        if (v <= 1) {
           n = '优秀';
        } else if  (v <= 2) {
           n = '良好';
        } else if  (v <= 3) {
           n = '中等';
        } else if  (v <= 4) {
           n = '较差';
        }
    }
    return n;
}

// 打开温心带
function showwxd () {
    $('body').addClass('notouch');
	$('.baby-wxd').removeClass('hidden');
    
    TouchSlide({ 
        slideCell:"#slideWxd",
        titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
        mainCell:".bd ul", 
        //effect:"leftLoop", 
        autoPage:true //自动分页
    });
}

// 隐藏温心带
function hidewxd () {
    $('body').removeClass('notouch');
	$('.baby-wxd').addClass('hidden');
}

// 显示测量体温帮助
function showhelper () {
    $('body').addClass('notouch');
	$('.baby-helper').removeClass('hidden');
}

// 隐藏温心带
function hidehelper () {
    $('body').removeClass('notouch');
	$('.baby-helper').addClass('hidden');
}

// 打开警报
function warn_open (obj) {
    obj.removeClass('switch-off');
    obj.addClass('switch-on');
}

// 关闭警报
function warn_close (obj) {
    obj.removeClass('switch-on');
    obj.addClass('switch-off');
}
    
// 显示报警设置内容
function showWarnSet () {
    $('body').addClass('notouch');
    $('.warn-box').removeClass('hidden');
    $('.warn .switch').removeClass('switch-on');
    $('.warn .switch').addClass('switch-off');
}

// 隐藏报警设置内容
function hideWarnSet () {
    $('body').removeClass('notouch');
    $('.warn-box').addClass('hidden');
}

// 接收URL参数
function getQueryString(name) { 
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
    var r = window.location.search.substr(1).match(reg); 
    if (r != null) return unescape(r[2]); return null; 
}

// 体温测量倒计时
function beginCount (countTime) {
    if(InterValObj) clearInterval(InterValObj);
    var times = Date.parse(new Date()) + countTime * 1000;
    var obj = $('.countDown');
    SetRemainTime (obj, times);
    InterValObj = window.setInterval(function(){ SetRemainTime (obj, times);},1000);  
}
var InterValObj;
 
//将时间减去1秒，计算天、时、分、秒 
function SetRemainTime(obj, times) { 
    var timestamp = Date.parse(new Date());
    var SysSecond = (times - timestamp)/1000;
    
    if (SysSecond > 0) { 
        SysSecond = SysSecond - 1; 
        var second = Math.floor(SysSecond % 60);             // 计算秒
        var minite = Math.floor((SysSecond / 60) % 60);      //计算分
        var con = '';
        if (minite<1) {
            con = "测量倒计时：<span>" + second + "秒</span>"; 
        } else {
            con ="测量倒计时：<span>" +  minite + "分" + second + "秒</span>"; 
        }
        obj.html(con); 
    } else {//剩余时间小于或等于0的时候，就停止间隔函数 
        window.clearInterval(InterValObj); 
        //这里可以添加倒计时时间为0后需要执行的事件 
        obj.html('测量完成');
    } 
} 

/*
 * 30秒更新数据，1分钟显示数据
 */
var passtime = 0;
var InterValRecords
function getNewRecords () {
    if(InterValRecords) clearInterval(InterValRecords);
    if (passtime == 30) {
        fun_notifydata();   // 让设备发数据给云
    }
    
    if (passtime == 59) {
        fun_getsingle(data_type); // 查询云数据，更新到页面
        passtime = 0;
    }
    
    passtime++;
    InterValRecords = window.setInterval("getNewRecords()",1000);  
}

/*
 * 消息推送
 */

var abnormal;

// 接收异常推送
function getNotice() {
    // 取得数据
    $.post(getalldata, forms, function (data) {
        if (typeof(data) == "object") {
            var hasProp = false;  
            for (var prop in data){  
                hasProp = true;  
                break;  
            }
            if (hasProp){
                // 遍历判断是否有异常数据
                for (var i = 0; i < data.length; i++) {
                    // 如果是身高，判断level，否则判断value
                    if (data[i]['level'] > 0) {
                        setAbnormal(data[i]['type'], data[i]['time']);
                    }
                }
                setNotice();
            }
        }
    });
}

// 添加异常到本地
var local_arr = new Object();
var local_abnormal;
function setAbnormal (type, times) {
    abnormal = localStorage.getItem('abnormal_' + entity_id);
    if (abnormal) {
        local_arr = JSON.parse(abnormal);  // 读取本地异常数据
    }

    if (local_arr) {
        if (!local_arr[type]) {
            add_local(type, times);
        } else {
            if (local_arr[type]['time'] != times) {
                add_local(type, times);
            }
        }
    } else {
        add_local(type, times);
    }
}

function add_local(type, times) {
    var new_data = new Object();
    new_data["entity_id"] = entity_id;
    new_data["type"] = type;
    new_data["time"] = times;
    new_data["isview"] = 0;
    local_arr[type] = new_data;
    localStorage.setItem('abnormal_' + entity_id, JSON.stringify(local_arr)); // 本地存储
}

// 设置查看异常状态
function setNotice() {
    // 读取本地存储异常数据
    abnormal = localStorage.getItem('abnormal_' + entity_id);
    var local_abnormal = JSON.parse(abnormal);  // 读取本地异常数据
    if (local_abnormal) {
        // 遍历本地数据
        for(var el in local_abnormal){
            if (local_abnormal[el]) {
                if (local_abnormal[el]['type'] == forms.data_type) {
                    local_abnormal[el]['isview'] = 1;
                } else {
                    if (local_abnormal[el]['isview'] != 1) {
                        setHtml(local_abnormal[el]['type']);
                    }
                }
            }
        }
        localStorage.setItem('abnormal_' + entity_id, JSON.stringify(local_abnormal)); // 本地存储
    }
}


// 返回HTML标记
function setHtml (type) {
    abnormal = localStorage.getItem('abnormal_' + entity_id);
    var local_abnormal = JSON.parse(abnormal);  // 读取本地异常数据
    if (type == local_abnormal[type]['type'] && local_abnormal[type]['isview'] != 1) {
        var cur;
        switch (Number(type)) {
            case 1:
                cur = 5;
                break;
            case 2:
                cur = 7;
                break;
            case 3:
                cur = 4;
                break;
            case 4:
                cur = 2;
                break;
            case 5:
                cur = 3;
                break;
            case 6:
                cur = 1;
                break;
            case 7:
                cur = 0;
                break;
        }
    }
    var notice = '<i class="glyphicon glyphicon-exclamation-sign"></i>';
    $("nav").find("li:eq(" + cur + ")").find("a").append(notice);
}

var InterValNotice;
$(function () {
    if(InterValNotice) clearInterval(InterValNotice);
    abnormal = localStorage.getItem('abnormal_' + entity_id);
    if (abnormal) {
        setNotice();
    }
    getNotice();
    InterValNotice = window.setInterval("getNotice()",10000);  
});

// 读取其他页面数据
var mn = 1;
var getall = 1;
function fun_getalldata() {
    if (mn < 8) {
        var webflag = localStorage.getItem('flag_' + entity_id + '_' + mn);

        if (webflag != 1) {
            if (getall == 1) {
                fun_getperdata(mn);
                getall = 2;
                mn++;
            } else {
                if (is_getsingle && is_getchart && is_recommend && is_suggestion) {
                    is_getsingle         = false;
                    is_getchart          = false;
                    is_recommend         = false;
                    is_suggestion        = false;
                    if (mn == 6 && is_getisunderarm) {
                        is_getismanualheight = false;
                        var localflag = mn - 1;
                        localStorage.setItem('flag_' + entity_id + '_' + localflag, 1);
                        fun_getperdata(mn);
                        mn++;
                    } else {
                        var localflag = mn - 1;
                        localStorage.setItem('flag_' + entity_id + '_' + localflag, 1);
                        fun_getperdata(mn);
                        mn++;
                    }
                }
            }
        } else {
            mn++;
            fun_getalldata();
        }
    } else {
        babydata_flag = 1;
        localStorage.setItem('babydata_flag_' + entity_id, babydata_flag);
    }
}

function fun_getperdata(n) {
    // 获得当前想的主题
    var arr = getUnit(n);
    var per_series = arr['series'] ? arr['series'] : '';

    // 获得成长建议ID
    var per_id = get_suggestionId(n);
    
    // 查询其他页面数据
    fun_getsingle(n, 1, 1);
    fun_getchart(n, 1, 1);
    fun_recommend(per_series, 1, 1);
    fun_suggestion(per_id, 1, 1);
    if (n == 6) { fun_getisunderarm(1, 1);}
}

// 加载失败
function loadfailed() {
    $('.page-loading ul').hide();
    $('.page-loading p').html('加载失败');
}