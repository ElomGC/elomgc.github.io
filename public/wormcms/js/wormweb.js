/**
 * Created by 84071 on 2017-12-04.
 */
layui.define('layer', function(exports){
    let $ = layui.jquery,
        layer = layui.layer;
    let label = {
        editlabel:function (o) {
            let d = $(o).data(),
                g = .8 * $(window).width(),
                k = .9 * $(window).height();
            layer.open({
                title: `编辑标签-${d.title}`,
                type: 2,
                area: [g + "px", k + "px"],
                content: d.uri,
                cancel: function(index, layero){
                    location.reload()
                }
            });
            return false;
        },
        // post提交数据
        postUrl:function (url,data = '',fun = '') {
            layer.load(1);
            $.post(url,data,function (res) {
                layer.closeAll('loading');
                if(typeof fun == "function"){
                    fun(res);
                } else {
                    layer.msg(res.msg,{},function () {
                        if(res.code == '1'){
                            return true;
                        }else if (res.code == '0'){
                            return false;
                        }
                    })
                }
            }).fail(function () {
                layer.closeAll('loading');
                layer.alert('系统错误，请稍后再试！');
            });
        },
    };
    $('body.cx-bodydbclick').dblclick(function () {
        label.postUrl('/api/label/webor.html','',function (res) {
            if(res.code == '1'){
                layer.confirm(res.msg, function(index){
                    location.reload();
                    layer.close(index);
                },function (index) {
                    label.postUrl('/api/label/webor.html','',function (res) {});
                    layer.close(index);
                });
            }
        });
    });

    $('body').on('click','.cx-label',function () {
        let a = $(this),
            b = a.data('type');
        label[b] ? label[b].call(this, a) : ""
    });

    exports('wormweb', {});
});