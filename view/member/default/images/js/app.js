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
        phocode: function(o) {
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
                data: { phone: phones, title: d.title, type: d.types },
                url: "/api/smscode/phonecode.html",
                success: function(res) {
                    lock = true;
                    layer.msg(res.msg);
                }
            });
        },
        settime: function(val) {
            let serverTime = new Date().getTime(),
                endTime = new Date(serverTime + 1000 * 60).getTime();
            layutil.countdown(endTime, serverTime, function(date, serverTime, timer) {
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
        postUrl: function(url, data = '', fun = '') {
            layer.load(1);
            $.post(url, data, function(res) {
                layer.closeAll('loading');
                if (typeof fun == "function") {
                    fun(res);
                } else {
                    layer.msg(res.msg, {}, function() {
                        if (res.code == '1') {
                            return true;
                        } else if (res.code == '0') {
                            return false;
                        }
                    })
                }
            }).fail(function() {
                layer.closeAll('loading');
                layer.alert('系统错误，请稍后再试！');
            });
        },
        out: function (o) {
            see.postUrl('/home/login/qulogin.html', '', function (res) {
                layer.msg(res.msg, {time: 500}, function () {
                    window.location.href = res.url;
                });
            });
        }
    }
    layform.on('submit(fabiaoneirong)', function(data) {
        let weizhi = data.field.title.indexOf('#'),
            huati = '',
            fabiaotitle = '';
        if(weizhi!==-1) {
            let _title= data.field.title.substring(weizhi+1);
            var patrn = /[`~!@#$%^&*()_\-+=<>?:\s"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘'，。、]/im;
            if(_title.match(patrn)) {
                let indedx = _title.match(patrn).index;//匹配到#后面的字符
                if(indedx) {
                    huati = data.field.title.substring(weizhi,indedx+1);
                    fabiaotitle = data.field.title.substring(indedx+1);
                }
            }else {
                huati = _title;
            }
        }else {
            fabiaotitle = data.field.title;
        }
        data.field.keywords = huati;
        data.field.title = fabiaotitle;
        let uri = `/api/${data.field.m}/article/save.html`;
        if(fabiaotitle=='') {
            layer.msg('请输入标题');
            return false;
        }
        if(data.field.content=='') {
            layer.msg('请输入内容');
            return false;
        }
        see.postUrl(uri, data.field, function(res) {
            layer.msg(res.msg, { time: 500 }, function() {
                if (res.code == '1') {
                    window.location.reload();
                }else if (res.code == '10000') {
                    window.location.href = '/home/login.html';
                }
            });
        });
        return false;
    });
    layform.on('submit(regbtn)', function(data) {
        let uri = data.form.action;
        see.postUrl(uri, data.field, function(res) {
            layer.msg(res.msg, { time: 500 }, function() {
                if (res.code == '1' && res.url) {
                    window.location.href = res.url;
                }
            });
        });
        return false;
    });
    $('.member').each(function () {
        if(window.location.pathname+window.location.search== $(this).attr('href')){
            $(this).addClass('ll-borbom4 ll-bold cx-text-black');
            $(this).siblings().removeClass('ll-borbom4 ll-bold cx-text-black');
        }
    });
    $('.price').each((index,item) => {
        let pri = parseFloat($(item).html()).toFixed(2);
        $(item).html(pri);
    });
    $('body').on('click','.cx-click',function () {
        let a = $(this),
            b = a.data('type');
        see[b] ? see[b].call(this, a) : ""
    });

    exports('app', {});
})