/**
 *  文件上传插件
 *  赵焱
 *  840712498@qq.com
 **/
(function () {
    let conf,
        _this,
        _this_file,
        fileList = {},
        fileEndList = {},
        fileUp = {};
    var wormUpload = function (data) {
            conf = data;
            addFileInput();
        };
    //  处理元素
    function addFileInput() {
        let multiple = conf.multiple ? true : false,
            ipt = multiple ? "<input class='wormui-upload-file' type='file' name='file' accept='' multiple=''>" : "<input class='wormui-upload-file' type='file' accept='' name='file'>";

        $('body').on('click',conf.monitor,function () {
            if(typeof conf.prepose == 'function'){
                if(conf.prepose(this) === false) {
                    return false;
                }
            }
            _this = this;
            _this_file = $(_this).next();
            if (!$(_this_file).hasClass(`wormui-upload-file`)) {
                $(_this).after(ipt);
                $(`.wormui-upload-file`).hide();
                _this_file = $(_this).next();
            }
            _this_file.click();
            _this_file.on('change',function () {
                addFileList($(this)[0].files);
                _this_file.off('change');
            });
        })
    }
    //  选择文件处理
    function addFileList(obj){
        let d = $(_this).data();
        if(conf.filenum && conf.filenum < obj.length){
            alert(`最多可选择${conf.filenum}个文件,请重新选择`);
            return false;
        }
        if(d.filenum && d.filenum < obj.length){
            alert(`最多可选择${d.filenum}个文件,请重新选择`);
            return false;
        }
        if(d.efilenum && d.efilenum < obj.length){
            alert(`最多可选择${d.efilenum}个文件,请重新选择`);
            return false;
        }
        if(!fileList[d.name]){
            fileList[d.name] = [];
        }
        let up = true;
        $.each(obj,function (index,item) {
            if(checkFile(item)){
                alert("选择的文件中包含不支持的格式");
                up = false;
                return false;
            }
            item.icon =  window.URL.createObjectURL(item);
            let prefix = item['icon'].split('/');
            item.md5 = prefix[prefix.length - 1];
            fileList[d.name].push(item);
            if(conf.filenum && conf.filenum < fileList[d.name].length){
                up = false;
                alert(`最多可选择${conf.filenum}个文件,请重新选择`);
                return false;
            }
            if(d.filenum && d.filenum < fileList[d.name].length){
                up = false;
                alert(`最多可选择${d.filenum}个文件,请重新选择`);
                return false;
            }
            if(typeof conf.before == 'function'){
                if(conf.before(item,_this) === false) {
                    up = false;
                }
            }else{
                let file_type = item.type,
                    file_exp = file_type.substr(file_type.lastIndexOf("/") + 1).toLowerCase();
                if (RegExp("jpg|jpeg|png|gif|bmp").test(file_exp)){
                    $(_this).before(`<div id="${item.md5}" style="width: 80px;display: flex;display: -webkit-flex; flex-direction:column;justify-content: center;align-items:center;position: relative;"><div style="width: 80px;height: 80px;border: 1px solid #dfdfdf;display: flex;display: -webkit-flex;justify-content: center;align-items:center"><img src="${item.icon}" style="width: auto;height: auto;max-width: 100%;max-height: 100%"></div><h6>${item.name}</h6><div id="${item.md5}por" style="position: absolute;width: 100%;height: 20px;"><div id="${item.md5}pors" style="width: 0;height: 20px;background: rgba(0,170,136,0.3);"></div></div></div>`);
                }else{
                    $(_this).before(`<div id="${item.md5}" style="width: 80px;display: flex;display: -webkit-flex;flex-direction:column;justify-content: center;align-items:center;position: relative;"><div style="width: 80px;height: 80px;border: 1px solid #dfdfdf;display: flex;display: -webkit-flex;justify-content: center;align-items:center"><img src="data:image/png;base64,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" style="width: auto;height: auto;max-width: 100%;max-height: 100%"></div><h6>${item.name}</h6><div id="${item.md5}por" style="position: absolute;width: 100%;height: 20px;"><div id="${item.md5}pors" style="width: 0;height: 20px;background: rgba(0,170,136,0.3);"></div></div></div>`);
                }
            }
        });
        if(!up){
            return false;
        }
        if(d.autoup){
            conf.autoup = d.autoup;
        }
        if(!conf.autoup){
            let upbtn = $(_this_file).next();
            if (!$(upbtn).hasClass(`${d.name}-upbtn`)) {
                $(_this_file).after(`<a class="${d.name}-upbtn" style="cursor: pointer;padding: 5px 10px;background:#dedede;border: 1px solid#aaa;line-height: 20px;margin-left: 5px;">开始上传</a>`);
                $(`.${d.name}-upbtn`).click(function () {
                    let t =  $(this).text();
                    if(t == '开始上传'){
                        fileUp[d.name] = true;
                        $(this).text("暂停上传");
                        upFile(d.name);
                    }else{
                        fileUp[d.name] = false;
                        $(this).text("开始上传");
                    }
                });
            }else {
                let t =  $(upbtn).text();
                if(t == "上传完毕"){
                    $(upbtn).text('开始上传');
                    $(upbtn).click(function () {
                        let t =  $(this).text();
                        if(t == '开始上传'){
                            fileUp[d.name] = true;
                            $(this).text("暂停上传");
                            upFile(d.name);
                        }else{
                            fileUp[d.name] = false;
                            $(this).text("开始上传");
                        }
                    });
                }
            }
        }else{
            fileUp[d.name] = true;
            upFile(d.name);
        }
        return false;
    }
    //  上传处理
    function upFile(name) {
        let up_list = fileList[name];
        if(up_list.length < 1){
            alert("请选择文件");
            return false;
        }
        $.each(up_list,function (index,item) {
            upAjax(name,item);
        });
        return false;
    }
    //  文件上传
    function fileUpAjax(obj) {
        let res_data;
        $.ajax({
            url: conf.url,
            type: "POST",
            data: obj,
            async:false,
            processData: false,
            contentType: false,
            success: function(res){
                res_data = res;
            }
        });
        return res_data;
    }
    function upAjax(name,obj,add_num = 0,up = 0) {
        if(!fileUp[name]){
            return false;
        }
        let size = obj.size,
            shardSize = 2 * 1024 * 1024,
            currShard = 0,
            total = 0,
            prefix = obj['name'].split('.');
        prefix = prefix[1];
        add_num = add_num * 1;
        if(add_num === 0){
            let arr = {file_check_name: obj.md5 + '.' + prefix},
                up_old = getFileUp(arr);
            if(up_old.code == '1'){
                return resSuccess(up_old,obj,name);
            }
        }
        //  查询是否需要切片
        let form = new FormData(),
            fileMd5 = new SparkMD5(),
            fileReader = new FileReader();
        form.append("name",obj.name);
        if(size > shardSize){
            let file_num = Math.ceil(size / shardSize),
                end = Math.min(size, (add_num + 1) * shardSize),
                file_slice = obj.slice(add_num * shardSize, end);
            total = file_num;
            currShard = add_num + 1;
            fileReader.readAsBinaryString(file_slice);
            fileReader.onload = function (e) {
                fileMd5.appendBinary(e.target.result);
                form.append("file", file_slice);  //slice方法用于切出文件的一部分
                form.append("md5", fileMd5.end());  //slice方法用于切出文件的一部分
                form.append("total", file_num);   //总片数
                form.append("currShard",currShard);        //当前是第几片
                form.append("prefix","part" + currShard);
                return upFileLoad(form,obj,name,up);
            };
        }else{
            fileReader.readAsBinaryString(obj);
            fileReader.onload = function (e) {
                form.append("file", obj);  //slice方法用于切出文件的一部分
                form.append("md5", obj.md5);  //slice方法用于切出文件的一部分
                form.append("total", total);   //总片数
                form.append("currShard",currShard);   //当前是第几片
                form.append("prefix",prefix);
                return upFileLoad(form,obj,name,up);
            };
        }
    }
    function upFileLoad(data,obj,name,up) {
        let currShard = data.get('currShard'),
            total = data.get('total'),
            size = obj.size,
            shardSize = 2 * 1024 * 1024;
        //  检测是否为切片文件
        if(size > shardSize){
            //  检测切片是否上传
            let arr = {file_check_name: data.get('md5') + '.' + data.get('prefix')},
                up_old = getFileUp(arr);
            if(!fileEndList[obj.md5]){
                fileEndList[obj.md5] = [];
            }
            if(up_old.code == '0'){
                let up_new = fileUpAjax(data);
                if(up_new.code == '1'){
                    fileEndList[obj.md5].push(data.get('md5') + '.' + data.get('prefix'));
                    return resFileS(total,currShard,name,obj);
                }else{
                    if(up > 4){
                        alert(form.get('name') + "上传失败");
                        return false;
                    }
                    currShard = currShard - 1;
                    up = up + 1;
                    return upAjax(name,obj,currShard,up);
                }
            }else{
                fileEndList[obj.md5].push(data.get('md5') + '.' + data.get('prefix'));
                return resFileS(total,currShard,name,obj);
            }
        }else{
            let up_new = fileUpAjax(data);
            if(up_new.code == '1'){
                return resSuccess(up_new,obj,name);
            }else{
                if(up > 4){
                    alert(data.get('name') + "上传失败");
                    return false;
                }
                up = up + 1;
                return upAjax(name,obj,currShard,up);
            }
        }
    }
    //  切片逻辑处理
    function resFileS(total,currShard,name,obj) {
        total = total * 1;
        currShard = currShard * 1;
        if(currShard < total){
            //  处理进度
            if(typeof conf.done == 'function'){
                obj.add = currShard;
                obj.total = total;
                conf.done(obj);
            }else{
                let u = obj.md5;
                let bai = currShard / total * 100;
                $(`#${u}pors`).width( bai + '%');
            }
            return upAjax(name,obj,currShard);
        }else{
            let arr = {
                    flieSet: "end",
                    name: obj.name,
                    type: obj.type,
                    size: obj.size,
                    md5: obj.md5,
                    file_list: fileEndList[obj.md5],
                };
            let res = getFileUp(arr);
            if(res.code == '1'){
                return resSuccess(res,obj,name);
            }else{
                alert(data.get('name') + "上传失败");
            }
        }
    }
    //  上传完成后
    function resSuccess(data,obj,name) {
       let new_list = [],
            new_limit = [];
       $.each(fileList[name],function (index,item) {
            if(item.md5 != obj.md5){
                new_list.push(item);
                new_limit.push('1');
            }
       });
       fileList[name] = new_list;
       if(new_limit.length < 1){
           delete fileEndList[obj.md5];
           $(`.${name}-upbtn`).text('上传完毕').off('click');
       }
        if(typeof conf.success == 'function'){
            conf.success(data,obj);
        }else {
            $(`#${obj.md5}pors`).width( '100%').text("上传完成").css('color','#fff');
        }
    }
    //  查询是否上传过及发送合并请求
    function getFileUp(obj) {
        let res_data;
        $.ajax({
            url: conf.url,
            type: "POST",
            data: obj,
            async:false,
            success: function(res){
                res_data = res;
            }
        });
        return res_data;
    }
    //  文件验证
    function checkFile(o){
        let d = $(_this).data(),
            upstatus = false;
        if(d.exp){
            let file_type = o.type,
                file_exp = file_type.substr(file_type.lastIndexOf("/") + 1).toLowerCase();
            switch (d.exp) {
                case "file":
                    if (!RegExp(file_exp).test(file_exp)){
                        upstatus = true;
                    }
                    break;
                case "video":
                    if (!RegExp("avi|mp4|wma|rmvb|rm|flash|3gp|flv|mp3|wav|mpeg").test(file_exp)){
                        upstatus = true;
                    }
                    break;
                default:
                    if (!RegExp("jpg|jpeg|png|gif|bmp").test(file_exp)){
                        upstatus = true;
                    }
                    break;
            }
        }
        return upstatus;
    }
    window.wormUpload = wormUpload;
})(window);
(function(factory){if(typeof exports==="object"){module.exports=factory()}else if(typeof define==="function"&&define.amd){define(factory)}else{var glob;try{glob=window}catch(e){glob=self}glob.SparkMD5=factory()}})(function(undefined){"use strict";var add32=function(a,b){return a+b&4294967295},hex_chr=["0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"];function cmn(q,a,b,x,s,t){a=add32(add32(a,q),add32(x,t));return add32(a<<s|a>>>32-s,b)}function md5cycle(x,k){var a=x[0],b=x[1],c=x[2],d=x[3];a+=(b&c|~b&d)+k[0]-680876936|0;a=(a<<7|a>>>25)+b|0;d+=(a&b|~a&c)+k[1]-389564586|0;d=(d<<12|d>>>20)+a|0;c+=(d&a|~d&b)+k[2]+606105819|0;c=(c<<17|c>>>15)+d|0;b+=(c&d|~c&a)+k[3]-1044525330|0;b=(b<<22|b>>>10)+c|0;a+=(b&c|~b&d)+k[4]-176418897|0;a=(a<<7|a>>>25)+b|0;d+=(a&b|~a&c)+k[5]+1200080426|0;d=(d<<12|d>>>20)+a|0;c+=(d&a|~d&b)+k[6]-1473231341|0;c=(c<<17|c>>>15)+d|0;b+=(c&d|~c&a)+k[7]-45705983|0;b=(b<<22|b>>>10)+c|0;a+=(b&c|~b&d)+k[8]+1770035416|0;a=(a<<7|a>>>25)+b|0;d+=(a&b|~a&c)+k[9]-1958414417|0;d=(d<<12|d>>>20)+a|0;c+=(d&a|~d&b)+k[10]-42063|0;c=(c<<17|c>>>15)+d|0;b+=(c&d|~c&a)+k[11]-1990404162|0;b=(b<<22|b>>>10)+c|0;a+=(b&c|~b&d)+k[12]+1804603682|0;a=(a<<7|a>>>25)+b|0;d+=(a&b|~a&c)+k[13]-40341101|0;d=(d<<12|d>>>20)+a|0;c+=(d&a|~d&b)+k[14]-1502002290|0;c=(c<<17|c>>>15)+d|0;b+=(c&d|~c&a)+k[15]+1236535329|0;b=(b<<22|b>>>10)+c|0;a+=(b&d|c&~d)+k[1]-165796510|0;a=(a<<5|a>>>27)+b|0;d+=(a&c|b&~c)+k[6]-1069501632|0;d=(d<<9|d>>>23)+a|0;c+=(d&b|a&~b)+k[11]+643717713|0;c=(c<<14|c>>>18)+d|0;b+=(c&a|d&~a)+k[0]-373897302|0;b=(b<<20|b>>>12)+c|0;a+=(b&d|c&~d)+k[5]-701558691|0;a=(a<<5|a>>>27)+b|0;d+=(a&c|b&~c)+k[10]+38016083|0;d=(d<<9|d>>>23)+a|0;c+=(d&b|a&~b)+k[15]-660478335|0;c=(c<<14|c>>>18)+d|0;b+=(c&a|d&~a)+k[4]-405537848|0;b=(b<<20|b>>>12)+c|0;a+=(b&d|c&~d)+k[9]+568446438|0;a=(a<<5|a>>>27)+b|0;d+=(a&c|b&~c)+k[14]-1019803690|0;d=(d<<9|d>>>23)+a|0;c+=(d&b|a&~b)+k[3]-187363961|0;c=(c<<14|c>>>18)+d|0;b+=(c&a|d&~a)+k[8]+1163531501|0;b=(b<<20|b>>>12)+c|0;a+=(b&d|c&~d)+k[13]-1444681467|0;a=(a<<5|a>>>27)+b|0;d+=(a&c|b&~c)+k[2]-51403784|0;d=(d<<9|d>>>23)+a|0;c+=(d&b|a&~b)+k[7]+1735328473|0;c=(c<<14|c>>>18)+d|0;b+=(c&a|d&~a)+k[12]-1926607734|0;b=(b<<20|b>>>12)+c|0;a+=(b^c^d)+k[5]-378558|0;a=(a<<4|a>>>28)+b|0;d+=(a^b^c)+k[8]-2022574463|0;d=(d<<11|d>>>21)+a|0;c+=(d^a^b)+k[11]+1839030562|0;c=(c<<16|c>>>16)+d|0;b+=(c^d^a)+k[14]-35309556|0;b=(b<<23|b>>>9)+c|0;a+=(b^c^d)+k[1]-1530992060|0;a=(a<<4|a>>>28)+b|0;d+=(a^b^c)+k[4]+1272893353|0;d=(d<<11|d>>>21)+a|0;c+=(d^a^b)+k[7]-155497632|0;c=(c<<16|c>>>16)+d|0;b+=(c^d^a)+k[10]-1094730640|0;b=(b<<23|b>>>9)+c|0;a+=(b^c^d)+k[13]+681279174|0;a=(a<<4|a>>>28)+b|0;d+=(a^b^c)+k[0]-358537222|0;d=(d<<11|d>>>21)+a|0;c+=(d^a^b)+k[3]-722521979|0;c=(c<<16|c>>>16)+d|0;b+=(c^d^a)+k[6]+76029189|0;b=(b<<23|b>>>9)+c|0;a+=(b^c^d)+k[9]-640364487|0;a=(a<<4|a>>>28)+b|0;d+=(a^b^c)+k[12]-421815835|0;d=(d<<11|d>>>21)+a|0;c+=(d^a^b)+k[15]+530742520|0;c=(c<<16|c>>>16)+d|0;b+=(c^d^a)+k[2]-995338651|0;b=(b<<23|b>>>9)+c|0;a+=(c^(b|~d))+k[0]-198630844|0;a=(a<<6|a>>>26)+b|0;d+=(b^(a|~c))+k[7]+1126891415|0;d=(d<<10|d>>>22)+a|0;c+=(a^(d|~b))+k[14]-1416354905|0;c=(c<<15|c>>>17)+d|0;b+=(d^(c|~a))+k[5]-57434055|0;b=(b<<21|b>>>11)+c|0;a+=(c^(b|~d))+k[12]+1700485571|0;a=(a<<6|a>>>26)+b|0;d+=(b^(a|~c))+k[3]-1894986606|0;d=(d<<10|d>>>22)+a|0;c+=(a^(d|~b))+k[10]-1051523|0;c=(c<<15|c>>>17)+d|0;b+=(d^(c|~a))+k[1]-2054922799|0;b=(b<<21|b>>>11)+c|0;a+=(c^(b|~d))+k[8]+1873313359|0;a=(a<<6|a>>>26)+b|0;d+=(b^(a|~c))+k[15]-30611744|0;d=(d<<10|d>>>22)+a|0;c+=(a^(d|~b))+k[6]-1560198380|0;c=(c<<15|c>>>17)+d|0;b+=(d^(c|~a))+k[13]+1309151649|0;b=(b<<21|b>>>11)+c|0;a+=(c^(b|~d))+k[4]-145523070|0;a=(a<<6|a>>>26)+b|0;d+=(b^(a|~c))+k[11]-1120210379|0;d=(d<<10|d>>>22)+a|0;c+=(a^(d|~b))+k[2]+718787259|0;c=(c<<15|c>>>17)+d|0;b+=(d^(c|~a))+k[9]-343485551|0;b=(b<<21|b>>>11)+c|0;x[0]=a+x[0]|0;x[1]=b+x[1]|0;x[2]=c+x[2]|0;x[3]=d+x[3]|0}function md5blk(s){var md5blks=[],i;for(i=0;i<64;i+=4){md5blks[i>>2]=s.charCodeAt(i)+(s.charCodeAt(i+1)<<8)+(s.charCodeAt(i+2)<<16)+(s.charCodeAt(i+3)<<24)}return md5blks}function md5blk_array(a){var md5blks=[],i;for(i=0;i<64;i+=4){md5blks[i>>2]=a[i]+(a[i+1]<<8)+(a[i+2]<<16)+(a[i+3]<<24)}return md5blks}function md51(s){var n=s.length,state=[1732584193,-271733879,-1732584194,271733878],i,length,tail,tmp,lo,hi;for(i=64;i<=n;i+=64){md5cycle(state,md5blk(s.substring(i-64,i)))}s=s.substring(i-64);length=s.length;tail=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];for(i=0;i<length;i+=1){tail[i>>2]|=s.charCodeAt(i)<<(i%4<<3)}tail[i>>2]|=128<<(i%4<<3);if(i>55){md5cycle(state,tail);for(i=0;i<16;i+=1){tail[i]=0}}tmp=n*8;tmp=tmp.toString(16).match(/(.*?)(.{0,8})$/);lo=parseInt(tmp[2],16);hi=parseInt(tmp[1],16)||0;tail[14]=lo;tail[15]=hi;md5cycle(state,tail);return state}function md51_array(a){var n=a.length,state=[1732584193,-271733879,-1732584194,271733878],i,length,tail,tmp,lo,hi;for(i=64;i<=n;i+=64){md5cycle(state,md5blk_array(a.subarray(i-64,i)))}a=i-64<n?a.subarray(i-64):new Uint8Array(0);length=a.length;tail=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];for(i=0;i<length;i+=1){tail[i>>2]|=a[i]<<(i%4<<3)}tail[i>>2]|=128<<(i%4<<3);if(i>55){md5cycle(state,tail);for(i=0;i<16;i+=1){tail[i]=0}}tmp=n*8;tmp=tmp.toString(16).match(/(.*?)(.{0,8})$/);lo=parseInt(tmp[2],16);hi=parseInt(tmp[1],16)||0;tail[14]=lo;tail[15]=hi;md5cycle(state,tail);return state}function rhex(n){var s="",j;for(j=0;j<4;j+=1){s+=hex_chr[n>>j*8+4&15]+hex_chr[n>>j*8&15]}return s}function hex(x){var i;for(i=0;i<x.length;i+=1){x[i]=rhex(x[i])}return x.join("")}if(hex(md51("hello"))!=="5d41402abc4b2a76b9719d911017c592"){add32=function(x,y){var lsw=(x&65535)+(y&65535),msw=(x>>16)+(y>>16)+(lsw>>16);return msw<<16|lsw&65535}}if(typeof ArrayBuffer!=="undefined"&&!ArrayBuffer.prototype.slice){(function(){function clamp(val,length){val=val|0||0;if(val<0){return Math.max(val+length,0)}return Math.min(val,length)}ArrayBuffer.prototype.slice=function(from,to){var length=this.byteLength,begin=clamp(from,length),end=length,num,target,targetArray,sourceArray;if(to!==undefined){end=clamp(to,length)}if(begin>end){return new ArrayBuffer(0)}num=end-begin;target=new ArrayBuffer(num);targetArray=new Uint8Array(target);sourceArray=new Uint8Array(this,begin,num);targetArray.set(sourceArray);return target}})()}function toUtf8(str){if(/[\u0080-\uFFFF]/.test(str)){str=unescape(encodeURIComponent(str))}return str}function utf8Str2ArrayBuffer(str,returnUInt8Array){var length=str.length,buff=new ArrayBuffer(length),arr=new Uint8Array(buff),i;for(i=0;i<length;i+=1){arr[i]=str.charCodeAt(i)}return returnUInt8Array?arr:buff}function arrayBuffer2Utf8Str(buff){return String.fromCharCode.apply(null,new Uint8Array(buff))}function concatenateArrayBuffers(first,second,returnUInt8Array){var result=new Uint8Array(first.byteLength+second.byteLength);result.set(new Uint8Array(first));result.set(new Uint8Array(second),first.byteLength);return returnUInt8Array?result:result.buffer}function hexToBinaryString(hex){var bytes=[],length=hex.length,x;for(x=0;x<length-1;x+=2){bytes.push(parseInt(hex.substr(x,2),16))}return String.fromCharCode.apply(String,bytes)}function SparkMD5(){this.reset()}SparkMD5.prototype.append=function(str){this.appendBinary(toUtf8(str));return this};SparkMD5.prototype.appendBinary=function(contents){this._buff+=contents;this._length+=contents.length;var length=this._buff.length,i;for(i=64;i<=length;i+=64){md5cycle(this._hash,md5blk(this._buff.substring(i-64,i)))}this._buff=this._buff.substring(i-64);return this};SparkMD5.prototype.end=function(raw){var buff=this._buff,length=buff.length,i,tail=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],ret;for(i=0;i<length;i+=1){tail[i>>2]|=buff.charCodeAt(i)<<(i%4<<3)}this._finish(tail,length);ret=hex(this._hash);if(raw){ret=hexToBinaryString(ret)}this.reset();return ret};SparkMD5.prototype.reset=function(){this._buff="";this._length=0;this._hash=[1732584193,-271733879,-1732584194,271733878];return this};SparkMD5.prototype.getState=function(){return{buff:this._buff,length:this._length,hash:this._hash}};SparkMD5.prototype.setState=function(state){this._buff=state.buff;this._length=state.length;this._hash=state.hash;return this};SparkMD5.prototype.destroy=function(){delete this._hash;delete this._buff;delete this._length};SparkMD5.prototype._finish=function(tail,length){var i=length,tmp,lo,hi;tail[i>>2]|=128<<(i%4<<3);if(i>55){md5cycle(this._hash,tail);for(i=0;i<16;i+=1){tail[i]=0}}tmp=this._length*8;tmp=tmp.toString(16).match(/(.*?)(.{0,8})$/);lo=parseInt(tmp[2],16);hi=parseInt(tmp[1],16)||0;tail[14]=lo;tail[15]=hi;md5cycle(this._hash,tail)};SparkMD5.hash=function(str,raw){return SparkMD5.hashBinary(toUtf8(str),raw)};SparkMD5.hashBinary=function(content,raw){var hash=md51(content),ret=hex(hash);return raw?hexToBinaryString(ret):ret};SparkMD5.ArrayBuffer=function(){this.reset()};SparkMD5.ArrayBuffer.prototype.append=function(arr){var buff=concatenateArrayBuffers(this._buff.buffer,arr,true),length=buff.length,i;this._length+=arr.byteLength;for(i=64;i<=length;i+=64){md5cycle(this._hash,md5blk_array(buff.subarray(i-64,i)))}this._buff=i-64<length?new Uint8Array(buff.buffer.slice(i-64)):new Uint8Array(0);return this};SparkMD5.ArrayBuffer.prototype.end=function(raw){var buff=this._buff,length=buff.length,tail=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],i,ret;for(i=0;i<length;i+=1){tail[i>>2]|=buff[i]<<(i%4<<3)}this._finish(tail,length);ret=hex(this._hash);if(raw){ret=hexToBinaryString(ret)}this.reset();return ret};SparkMD5.ArrayBuffer.prototype.reset=function(){this._buff=new Uint8Array(0);this._length=0;this._hash=[1732584193,-271733879,-1732584194,271733878];return this};SparkMD5.ArrayBuffer.prototype.getState=function(){var state=SparkMD5.prototype.getState.call(this);state.buff=arrayBuffer2Utf8Str(state.buff);return state};SparkMD5.ArrayBuffer.prototype.setState=function(state){state.buff=utf8Str2ArrayBuffer(state.buff,true);return SparkMD5.prototype.setState.call(this,state)};SparkMD5.ArrayBuffer.prototype.destroy=SparkMD5.prototype.destroy;SparkMD5.ArrayBuffer.prototype._finish=SparkMD5.prototype._finish;SparkMD5.ArrayBuffer.hash=function(arr,raw){var hash=md51_array(new Uint8Array(arr)),ret=hex(hash);return raw?hexToBinaryString(ret):ret};return SparkMD5});

