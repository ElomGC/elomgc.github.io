layui.define(['layer', 'form','element','carousel','util','laydate','laytpl','laypage'], function(exports) {
    let $ = layui.jquery,
        layer = layui.layer,
        layelement = layui.element,
        laycarousel = layui.carousel,
        layutil = layui.util,
        layform = layui.form,
        laytpl = layui.laytpl,
        laydate = layui.laydate,
        laypage = layui.laypage;
    let see = {
        phocode: function (o) {
            let d = $(o).data(),
                num = $(d.phone).length - 1,
                phones = $(d.phone).eq(num).val();
            let lock = true;
            if (phones == '') {
                layer.msg('手机不得为空');
                return false;
            }
            if (!phones.match(/^(((13[0-9]{1})|(15[0-35-9]{1})|(16[0-35-9]{1})|(17[0-9]{1})|(18[0-9]{1})|(19[0-9]{1}))+\d{8})$/)) {
                layer.msg('手机号码格式不正确1');
                return false;
            }
            see.settime();
            if (!lock) return;
            lock = false;
            $.ajax({
                async: true,
                type: 'post',
                dataType: "json",
                data: {phone: phones, title: d.title, type: d.types},
                url: "/api/smscode/phonecode.html",
                success: function (res) {
                    lock = true;
                    layer.msg(res.msg);
                }
            });
        },
        settime: function (val) {
            let serverTime = new Date().getTime(),
                endTime = new Date(serverTime + 1000 * 60).getTime();
            layutil.countdown(endTime, serverTime, function (date, serverTime, timer) {
                if (date[3] == '0') {
                    $("#phocode").addClass("cx-text-green cx-click");
                    $('#phocode').html('获取验证码');
                } else {
                    $("#phocode").removeClass("cx-text-green cx-click");
                    $('#phocode').html('重新发送(' + date[3] + ')');
                }
            });
        },
        // post提交数据
        postUrl: function (url, data = '', fun = '') {
            layer.load(1);
            $.post(url, data, function (res) {
                layer.closeAll('loading');
                if (typeof fun == "function") {
                    fun(res);
                } else {
                    layer.msg(res.msg, {}, function () {
                        if (res.code == '1') {
                            return true;
                        } else if (res.code == '0') {
                            return false;
                        }
                    })
                }
            }).fail(function () {
                layer.closeAll('loading');
                layer.alert('系统错误，请稍后再试！');
            });
        },
        menu: function (o) {
            $(o.data().cid).toggleClass('cx-hidden-l')
            $(o.data().icon).toggleClass('cx-hidden')
        },
        gengduolanmu:function (o) {
            let d = $(o).data(),
                w = $(window).width() * 0.8;
            if(!$(d.cid).hasClass('open')){
                $(d.cid).addClass('open')
            }
            layer.open({
                type:1,
                title: false,
                area:[w + 'px'],
                content: $(d.cid),
                end:function () {
                    $(d.cid).removeClass('open')
                }
            });
        },
    }
    layform.on('submit(formDemo)', function(data) {
        let uri = data.form.action;
        see.postUrl(uri, data.field, function(res) {
            layer.msg(res.msg, { time: 500 }, function() {
                if (res.code == '1' && res.url) {
                    window.location.reload();
                }
            });
        });
        return false;
    });
    $('.ll-nav-hover').each(function () {
        if(window.location.pathname == $(this).find('a').attr('href')){
            $(this).find('a').addClass('ll-color ll-bold');
            $(this).siblings().find('a').removeClass('ll-color ll-bold')
        }
    });
    $('.leftpart').each(function () {
        if(window.location.pathname == $(this).find('a').attr('href')){
            $(this).find('a').addClass('ll-bg-color cx-text-white');
            $(this).siblings().find('a').removeClass('ll-bg-color cx-text-white')
        }
    });
    $('.ll-part-hover').each(function () {
        if(window.location.pathname == $(this).attr('href')){
            $(this).addClass('ll-color');
            $(this).siblings().find('a').removeClass('ll-color')
        }
    });
    //  定义幻灯片高度
    $(".in-huandeng").each(function() {
        let w = $(this).width();
        let h = w / 2;
        if (h > 500) {
            $(this).height('500px');
        } else {
            $(this).height(h + 'px');
        }
    });
    var gallerySwiper = new Swiper('#gallery', {
        spaceBetween: 10,
        loop : true,
        navigation: {
            nextEl: '.leftpre',
            prevEl: '.rightpre',
            disabledClass: 'my-button-disabled',
        },
    })
    var swiper = new Swiper('#guanyu', {
        slidesPerView: 1,
        spaceBetween: 5,
        centeredSlides: true,
        loop: true,
        autoplay:true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
    laycarousel.render({
        elem: '#inhdp',
        width: '100%',
        height: '100%',
        arrow: 'hover'
    });
    layform.on('submit(formDemo)', function(data) {
        let uri = data.form.action;
        see.postUrl(uri, data.field, function(res) {
            layer.msg(res.msg, { time: 500 }, function() {
                if (res.code == '1' && res.url) {
                    window.location.reload();
                }
            });
        });
        return false;
    });
    if (!(/msie [6|7|8|9]/i.test(navigator.userAgent))){
        (function(){
            window.scrollReveal = new scrollReveal({reset: true});
        })();
    };
    $('body').on('click','.cx-click',function () {
        let a = $(this),
            b = a.data('type');
        see[b] ? see[b].call(this, a) : ""
    });
    exports('app', {});
})