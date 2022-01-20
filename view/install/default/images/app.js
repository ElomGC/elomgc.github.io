layui.define(['layer','form','table','laydate','element'], function(exports) {
    let layer = layui.layer,
        layform = layui.form,
        element = layui.element;

    layform.on('submit(databasebtn)', function(data){
        layer.load(1);
        let uri = data.form.action;
        $.post(uri,data.field,function (res) {
            layer.closeAll('loading');
            if(res.code == '0'){
               layer.msg(res.msg);
            }else{
                window.location.href = res.url;
            }
        }).fail(function () {
            layer.closeAll('loading');
            layer.alert('系统错误，请稍后再试！');
        });
        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
    });
    exports("app", {});
});