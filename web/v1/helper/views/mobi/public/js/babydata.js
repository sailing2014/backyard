$(function () {
    if ($(window).height() > $(document.body).height()) {
        $(document.body).height($(window).height());
    }

    // 查看历史
    $('.heav .pull-right a').click(function () {
        showfilter('.filter-box');
        setTable();
    });
    $('.filter').click(function (event) {
        hidefilter('.filter-box');
    })

    // 身高开关
    $('.heav .switch').click(function () {
        var sw = $(this);
        if (sw.hasClass('switch-on')) {
            switch_handle(sw);
        } else {
            switch_auto(sw);
        }
    });

    // 体温报警开关
    $('.warn .switch').click(function () {
        var sw = $(this);
        if (sw.hasClass('switch-on')) {
            showfilter('.filter-box');
            warn_close(sw);
            $('.show-history').addClass('hidden');
            $('.warn-set').removeClass('hidden');
        } else {
            warn_open(sw);
            $('.warn-time').html('30分钟');
        }
    });

    // 体换体温监测方式
    $('.moudle-btn a').click(function () {
        var surface = $('.surface');
        var oxter = $('.oxter');
        if (surface.hasClass('hidden')) {
            // 体表温度
            surface.removeClass('hidden');
            oxter.addClass('hidden');
            $('.nav-2').html('体表<br>体温');
        } else {
            // 腋下温度
            surface.addClass('hidden');
            oxter.removeClass('hidden');
            $('.nav-2').html('腋下<br>体温');
        }
        setTable();
    });

    // 手动选择提醒时间
    $('.warn-set').find('li').click(function () {
        $('.warn-set').find('li').removeClass('active');
        $('.warn-time').html($(this).find('span').html());
        $(this).addClass('active');
    });

    // 温度help
    $('.switch-helper').click(function () {
        showhelper('.baby-helper')
    });

    // 温度help关闭
    $('.close-helper').click(function () {
        hidehelper('.baby-helper')
    });

    // 切换baby
    $('header>.baby-pic, header>.baby-font').click(function () {
        $(".baby-more").fadeToggle();
        if ($(".baby-more").hasClass('active')) {
            $(".baby-more").removeClass('active');
            $('header>.baby-font').find('.glyphicon').addClass('glyphicon-triangle-bottom');
            $('header>.baby-font').find('.glyphicon').removeClass('glyphicon-triangle-top');
        } else {
            $(".baby-more").addClass('active');
            $('header>.baby-font').find('.glyphicon').removeClass('glyphicon-triangle-bottom');
            $('header>.baby-font').find('.glyphicon').addClass('glyphicon-triangle-top');
        }
    });
});

// 自动
function switch_auto(obj) {
    obj.removeClass('switch-off');
    obj.addClass('switch-on');
    obj.find('i').html('自动');
    $('.heightcolor').removeClass('hidden');
    $('.recordheight').addClass('hidden');
    $('.notice-height').removeClass('hidden');
    $('.switch').addClass('pull-right');
    $('.switch').removeClass('mgauto');
}

// 手动
function switch_handle(obj) {
    obj.removeClass('switch-on');
    obj.addClass('switch-off');
    obj.find('i').html('手动');
    $('.heightcolor').addClass('hidden');
    $('.recordheight').removeClass('hidden');
    $('.notice-height').addClass('hidden');
    $('.switch').removeClass('pull-right');
    $('.switch').addClass('mgauto');
}

// 打开警报
function warn_open(obj) {
    obj.removeClass('switch-off');
    obj.addClass('switch-on');
}

// 关闭警报
function warn_close(obj) {
    obj.removeClass('switch-on');
    obj.addClass('switch-off');
}

// 关闭温度帮助
function hidehelper(obj) {
    $('body').removeClass('notouch');
    $(obj).addClass('hide');
    $(obj).addClass('hidden');
}

// 打开温度帮助
function showhelper(obj) {
    $('body').addClass('notouch');
    $(obj).removeClass('hide');
    $(obj).removeClass('hidden');
}

function hidefilter(obj) {
    $('body').removeClass('notouch');
    $(obj).addClass('hide');
    $(obj).addClass('hidden');
    $('.show-history').addClass('hidden');
    $('.warn-set').addClass('hidden');
}

function showfilter(obj) {
    $('body').addClass('notouch');
    $(obj).removeClass('hide');
    $(obj).removeClass('hidden');
    $('.show-history').removeClass('hidden');
}

// loading 效果
var count = 0;	// loading 效果定义变量
function loading() {
    //var loadingHTML = '<style>.loading{ position:absolute; width:100%; height:100%; left:0; top:0; background:rgba(0,0,0,.8);} #loading { position:absolute; left:50%; top:50%; width:50px; height:50px; margin:-25px 0 0 -25px; overflow:hidden;}</style><div class="loading"><div id="loading"><img src="public/images/min/circle.png"></div></div>';
    //$('.index-video-ready').append(loadingHTML);
    //window.setTimeout(rotate, 100);

    var loadingHTML = '<div class="loading"><div id="loading"><img src="../images/min/circle.png"></div></div>';
    $(document.body).append(loadingHTML);
    window.setTimeout(rotate, 100);
}
function rotate() {
    var elem4 = document.getElementById('loading');
    elem4.style.WebkitTransform = 'scale(0.5) rotate(' + count + 'deg)';
    if (count == 360) {
        count = 0
    }
    count += 45;
    window.setTimeout(rotate, 100);
}


// 分页方法
function loadpage(url, nowpagebox, showcontent, returnbox, extendsfunction) {
    // 滚到底部加载更多
    var flag = 0;
    $(window).scroll(function () {
        if ($(window).scrollTop() == $(document).height() - $(window).height()) {
            if (flag != 1) {
                flag = 1;
                loading();

                //获得当前频道页数
                var pages = Number($(nowpagebox).val()) + 1;

                $.get(url, function (result) {
                    // 模拟result 有数据和无数据的结果
                    if (pages <= 3) {
                        setTimeout(function () {
                            $("" + returnbox).append(showcontent + showcontent + showcontent);
                            $(nowpagebox).val(pages);
                            $('.loading').remove();
                            flag = 0;
                        }, 1000);
                    } else {
                        if (extendsfunction == 1) {
                            getcallback();
                        }
                        $('.loading').remove();
                    }
                });
            }
        }
    });
}


function checkview(obj) {
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

function js_date_time(unixtime) {
    var timestr = new Date(parseInt(unixtime));
    var datetime = timestr.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ").split(" ");
    return datetime[0];
}
