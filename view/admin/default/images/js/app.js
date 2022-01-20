layui.define(['layer','form','table','laydate','element'], function(exports) {
    let layer = layui.layer,
        layform = layui.form,
        laytable = layui.table,
        element = layui.element,
        laydate = layui.laydate;
    let see = {
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
        //  折叠菜单点击
        foldtab:function (o) {
            let d = $(o).data();
            if($(`.map-left-dd${d.cid}`).hasClass(`open`)){
                $(`.map-left-dd${d.cid}`).removeClass(`open`).find(`.map-left-dd`).removeClass(`open`);
            }else{
                $(`.map-left-dd`).removeClass(`open`);
                $(`.map-left-dl dt`).removeClass(`open`).children(`i`).addClass(`cx-iconunfold`).removeClass(`cx-iconfold`);
                $(`.map-left-dd${d.cid}`).addClass(`open`).parents(`.map-left-dd`).addClass(`open`);
                see.foldtabdt(o);
            }
            return false;
        },
        //  更改所有标题
        foldtabdt:function(o){
            $(o).addClass(`open`).children(`i`).removeClass(`cx-iconunfold`).addClass(`cx-iconfold`);
            let upd = $(o).closest(`dd`).closest(`dl`).children(`dt`);
            if(upd.length > 0){
                see.foldtabdt(upd);
            }
            return false;
        },
        //  左侧菜单点击
        rightbody:function (o) {
            let d = $(o).data();
            $(`.map-left-ddu`).removeClass(`open`);
            $(o).addClass(`open`);
            $(`.tabiframe`).attr('src',d.uri);
            let upd = $(o).parents(`dl`),
                menu = [];
            $.each(upd,function (index,item) {
                let t = $(item).children(`dt`);
                if(t.length > 0){
                    menu.push($(t).text())
                }
            });
            menu.reverse();
            upd = '';
            $.each(menu,function (index,item){
                upd += `<i class="cx-icon cx-iconright cx-mag-lr5"></i><span>${item}</span>`;
            });
            menu = $(o).text();
            upd += `<i class="cx-icon cx-iconright cx-mag-lr5"></i><span class="cx-text-black-8">${menu}</span>`;
            $(`.map-headernav-box`).html(upd);
            layui.sessionData('tabiframe', {
                key: 'taburl',
                value: {uri:d.uri,nav:upd}
            });
            return false;
        },
        //  监听刷新事件
        readWin:function () {
            let ifuri = layui.sessionData('tabiframe');
            if(ifuri.taburl){
                $(`.tabiframe`).attr('src',ifuri.taburl.uri);
                $(`.map-headernav-box`).html(ifuri.taburl.nav);
            }
        },
        //  刷新子窗口
        readmaprbody:function () {
            let d = $(".tabiframe");
            $(d).attr('src', $(d).attr('src'));
        },
        //  权限选择
        authupbox:function (dom,val) {
            //  向上选择
            let u_dom = $(dom).closest(`.groupauth-list`).prev(),
                ipts = $(u_dom).find("input[name='auth[]']");
            if(val){
                $(ipts).prop("checked",true);
                return false;
            }else{
                let s_dom = $(dom).closest(`h3`).siblings().find("input:checked").length;
                if(s_dom == 0){
                    $(ipts).prop("checked",false);
                }
            }
            layform.render('checkbox');
            let ups = $(ipts).closest(`h3`).closest(`.groupauth-list`).prev();
            if(ups.length > 0){
                see.authupbox(ipts,val);
            }
        },
        //  退出
        loginqu:function (o) {
            let d = $(o).data();
            see.postUrl(d.uri,'',function (res) {
                if(res.code == '1'){
                    window.location.reload();
                }
            });
        },
        //  删除
        delelement:function (o) {
            let d = $(o).data();
            $(d.cid).remove();
            $(d.name).val('');
        },
        //
        layifuri:function (o){
            let un = parent.layer.getFrameIndex(window.name),
                d = $(o).data();
            parent.layer.iframeSrc(un, d.uri)
        },
        //  弹出窗口
        addopen:function (o) {
            let d = $(o).data(),
                w = $(window).width() * 0.9,
                h = $(window).height() * 0.9;
            let index = layer.open({
                type: 2,
                title: d.title,
                area: [w + 'px',h + 'px'],
                skin:'addopen',
                offset: 't',
                maxmin: true,
                anim: 1,
                content: d.uri,
                success: function (layero, index) {
                    // layer.iframeAuto(index)
                },
                restore:function (item) {
                    $(item).css('top','0');
                    $(".layui-layer-min").css('display','none');
                    layer.iframeAuto(index)
                }
            });
            if(d.full == 'y') {
                layer.full(index);
            }
        },
    };
    see.readWin();
    layform.on('submit(upbutton)', function(data){
        let uri = data.form.action;
        see.postUrl(uri,data.field,success = function (res) {
            layer.msg(res.msg,{},function () {
                if (res.code == '1') {
                    parent.location.reload();
                }
            });
        });
        return false;
    });
    layform.on('submit(thisbutton)', function(data){
        let uri = data.form.action;
        see.postUrl(uri,data.field,success = function (res) {
            layer.msg(res.msg);
        });
        return false;
    });
    layform.on('submit(newbutton)', function(data){

    });
    layform.on('checkbox(authcheckbox)', function(data){
        let n_dom = $(data.elem).closest(`h3`).next();
        //  向下选择
        if($(n_dom).hasClass(`groupauth-list`)){
            let ids_list = $(n_dom).find("input[name='auth[]']");
            $.each(ids_list,function (index,item) {
                $(item).prop("checked",data.elem.checked ? true : false);
            });
        }
        see.authupbox(data.elem,data.elem.checked);
        layform.render('checkbox');
    });
    layform.on('submit(login)', function(data){
        let uri = data.form.action;
        see.postUrl(uri,data.field,success = function (res) {
            layer.msg(res.msg);
            if (res.code == '1') {
                window.location.href = res.url;
            }
        });
        return false;
    });
    //  渲染日期
    $('._date').each(function(){
        laydate.render({
            elem: this,
            trigger: 'click'
        });
    });
    //  渲染日期
    $('._time').each(function(){
        laydate.render({
            elem: this,
            type: 'time',
            trigger: 'click'
        });
    });
    //  渲染日期
    $('._datetime').each(function(){
        laydate.render({
            elem: this,
            type:'datetime',
            trigger: 'click'
        });
    });
    $("body").on('click', ".cx-click", function () {
        let a = $(this),
            b = a.data("type");
        see[b] ? see[b].call(this, a) : ""
    });
    exports("app", {});
    window.wormui = see;
});
