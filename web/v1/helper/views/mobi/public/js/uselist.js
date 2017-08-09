$(function () {    
	if ($(window).height() > $(document.body).height()) {
		$(document.body).height($(window).height());
	} 
    
    $('.filter').click(function(){
        hidedrop('.drop-cate');
    });
    
    $('.uselist-font span').click( function () {
        $('.drop-cate').css("display") == 'none' ? showdrop('.drop-cate') : hidedrop('.drop-cate');
    });

    $('.list-more a').click(function () {
        if ($(this).html() != "收起") {
            $(this).parents('.list').find("dl").addClass("showmore");
            $(this).html("收起");
        } else {
            $(this).parents('.list').find("dl").removeClass("showmore");
            $(this).html("更多…");
        }
    });
    
    $('.section-list').on('click','.show-intro',function () {
        showbrand($(this));
    });
});

var msgbox = '<div class="filter-box"><div class="filter"></div><div class="filter-con">{con}<div class="filter-con-close" onclick="closeNotice()"></div></div></div>';
function showNotice (text) {
    msgbox = msgbox.replace("{con}", text);
    $("body").append(msgbox);
}

function closeNotice () {
    $('.filter-box').remove();
}

function showdrop (obj) {
    $('.uselist-font i').removeClass('glyphicon-triangle-bottom');
    $('.uselist-font i').addClass('glyphicon-triangle-top');
	$(obj).show();
}

function hidedrop (obj) {
    $('.uselist-font i').addClass('glyphicon-triangle-bottom');
    $('.uselist-font i').removeClass('glyphicon-triangle-top');
	$(obj).hide();
}
function showbrand(obj) {
    var msgbox   = "<div class='show-intro-box'><div class='filter'></div><div class='intro-box'><div class='show-intro-close' onclick='closebrand()'></div><div class='s-intro'><h3>品牌介绍</h3><div>{con}</div></div></div></div>";
    var con      = obj.parents("li").find(".intro").html();
    var contents = msgbox.replace('{con}', con);
    $("body").append(contents);
}
function closebrand() {
    $('.show-intro-box').remove();
};

// loading 效果
var count = 0;	// loading 效果定义变量
function loading () {
	var loadingHTML = '<div class="loading"><div id="loading"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAmCAYAAACoPemuAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDE0IDc5LjE1MTQ4MSwgMjAxMy8wMy8xMy0xMjowOToxNSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGQzk0ODM2QTIwODkxMUU1QjNBRkRBNjhCRERERDIwQyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGQzk0ODM2QjIwODkxMUU1QjNBRkRBNjhCRERERDIwQyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkZDOTQ4MzY4MjA4OTExRTVCM0FGREE2OEJEREREMjBDIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkZDOTQ4MzY5MjA4OTExRTVCM0FGREE2OEJEREREMjBDIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+P/1diQAABchJREFUeNqsmH9InVUYx8/1Xk1Ei5IxUke1aGvWHEOClbii9cdWsr9aMJ2QGO6PGvtHBNHtsusIHAlhJVu4YTTbn8bQ1R8FzUXuD7fWtm6bgj9Q8QfOyHvx51X7Pi/PkWen8773VTrw8TnPeV/v+32f8+s5b6Cnp0etra0pKmS96sLfDQ6AN8AOsA1k8vU47AjoA7+trq7+DO6trKwoWGXa8vJyZSsh+hMIBJyHktVCZJ3vyYSpgl8Jm69cCq4/A0PsAUf4haLgIoR8DRsnQdRO1q2kaAHS6jr7IXAS1SHQxKKmQBuoAK+BLeAJZgseSm0VsG1gAgLyYZvAEOonQYhEeQn7T8REhMi8BP8KbCG33YA5B34ECX2vUaYJXOtlYUFwCCJqQDH4HJSDo6B/MxErge0FhWAAlKBtP+gkUbboWrqVWAGdELEflIABUAhuke8pTMIPLAMdqD4JKGJ74Xd5iXARpXSXMV1gL/gOgz8LtqOlpeWYVZglWodR/YbHVgSUglkx5mzj0CpMDnLBLCgDEYijsdbW3Nx8OFnEdoLLIAg+BWEzon4ESaQoWiIEYXAWBMHlpqamnW5jjCZCO2wWoO6rcxPlZ2y5iHkMXD8Fe4W7tb2xsTFki9jHPNAHwXE/otwEamE2MYlEwkGIOw4GUacJ8Yk5xjLBKX7QCRCzCUtJSbF2qRRExYyWFiMFab+6ujoG/wS31zc0NGTKiFWBbPCrnn0bxYyWFGNDC6RSW1tLs/UGyAZVUlgl23Nu0fFql0VHSgtYXl5+DFOcLqh/xl1b6az8+OECsc38YHaP3g3kLCNx5p6qffpxLWJpacmB6nKcyaVEvNA1+FMgv76+voAi9ja/+TWQkFGR0aG6DTNqFAkSs7i4uI4WKSMmu5JKJBJJsDgSeYAito+vXTf3SnP/1L7cfKlNR5AetLCwoObn5x1ktORiK1MoYxj8AvMh2EfCdnH7fSlILqBmXqYjqN9YC6XoxONxFYvFnDpFRi6wtjzPEPYnV18mYbnsDNlWdNs4kpEKBoOOT5Ganp52oEiRKEuCmWyLHWSbS8Ky2InZ7vSzcaenp6uOjg71PxStISskHrzmJcTcIzUUsbS0NFVXV6cyMjIcf2xsTLW2tjrjbLOFhJHKbI7cIy8xUpSelaFQSKWmpq7PSCo5OTkqHA47942Ojqru7m7V19fnR+h675GwMRb2ghTmlkBKYRQdjdyK5P25ubmqtLTUaSOR0WhUDQwMqImJCZvQ59mOkbAHvMi+Anr9ijLXMrkdOVsKLyGykMi8vDxnNo+Pj6vh4WFzuLzKE+QBCbsJPoDzFieInqKSCTNnom0Xof8hgYQsRUVFb05OTqrZ2dmbgf7+/gL8wx9onwR5tPoni5YUR90orW1XsL2sZRyH5ubmRmdmZrZC3B6K2F00RmFpvzwI2+mWd9m2Kq983xYt2wLO5SBm9VYQRSTv6uziEtsar25Llva4nd7dFlmjXsP3XZJpzwXwCBSD9/zkXhuJmE2kkVm8C1MMZsAFKSwOznK9mfN+5VegLYt1E2haiKK16wu+t4G0mMe3L2FvwW6HPe8WkWQHEdueaOtKYc+D7aj+Thpsh5EEH3RjfJaM2GalX4G2LrOIOgNTyl+IjmIcJ9w+ETyEPQYon6HDyZlkOf5GTuUWUafpEwJsGZabh8lO4lcpWSNx4DRoh5+5mY3YJWL0W+1CVAVEXfX77YJO4+9TiCnUqN9JNluTrWPs0+y7g3op//YRJAHf+v7aw+V7PgDfRv1F/spznb4CgaDfgU/PQFsJ/28X6vRbt2Hp05ZrEhcYGRlRXh/v+OMKndLDcJ/ma1N8/qRzwn0wDP7h+58Cz9GGDOiz1SHYZ/n3/qZxC/uV3vool7MKo1TEK6MQdXrgR3QGRdOuZAuv4f+F6kXYVvECKqkwH6LMh+6G+w7s62AH2KZTdF5uRkAf3B7Yn8A9t5d3E/avAAMAxuEKfDIGxGkAAAAASUVORK5CYII="></div></div>';
	$(document.body).append(loadingHTML);
	window.setTimeout(rotate, 100);
}
function rotate() {
	var elem4 = document.getElementById('loading');
        if(elem4){
            elem4.style.WebkitTransform = 'scale(0.5) rotate('+count+'deg)';
            if (count==360) { count = 0 }
            count+=45;
        }
	window.setTimeout(rotate, 100);
}
