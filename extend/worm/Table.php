<?php
declare(strict_types = 1);
namespace worm;

use app\common\model\AddChinacode;
use app\common\model\Chinacode;
use app\facade\hook\Common;
use app\facade\wormview\Upload;
use think\facade\Route;

class Table {

    public function getLayuiTable($filed_data){
        $res = [
            'field' => empty($filed_data['file']) ? '' : $filed_data['file'],
            'title' => empty($filed_data['title']) ? '' : $filed_data['title'],
            'hide' => empty($filed_data['hidden']) ? false : $filed_data['hidden'],
            'sort' => empty($filed_data['sort']) ? false : $filed_data['sort'],
            'edit' => $filed_data['type'] == 'edit' ? true : false,
            'align' => empty($filed_data['textalign']) ? 'left' : $filed_data['textalign'],
        ];
        if(!empty($filed_data['minwidth'])){
            $res['minWidth'] = $filed_data['minwidth'];
        }elseif(!empty($filed_data['width'])){
            $res['width'] = $filed_data['width'];
        }
        if(!empty($filed_data['fixed'])){
            $res['fixed'] = $filed_data['fixed'];
        }
        switch ($filed_data['type']){
            case 'checkbox':
                $res['type'] = 'checkbox';
                break;
            case 'btn':
                $res['templet'] = "#{$filed_data['filed']}";
                break;
            case 'switch':
                $res['templet'] = "#{$filed_data['file']}";
                break;
            case 'link':
                $res['templet'] = "#{$filed_data['file']}";
                break;
            case 'radio':
                $typeid = empty($filed_data['type_edit']) ? $filed_data['file'] : $filed_data['file'].$filed_data['type_edit'];
                $typeid = empty($filed_data['file_edit']) ? $typeid : $filed_data['file_edit'].$typeid;
                $res['templet'] = "#{$typeid}";
                break;
        }
        return $res;
    }
    /**
     * 转换字段到表格
     * @param $filed_list
     * @param $data
     * @return array
     */
    public function getTableFiled($filed_list,$data){
        $res = [
            'type' => $filed_list['type'],
            'file' => $filed_list['file'],
            'title' => $filed_list['title'],
            'class' => empty($filed_list['class']) ? null : $filed_list['class'],
            'width' => empty($filed_list['width']) ? null : $filed_list['width'],
            'value' => '',
        ];
        if(empty($data)){
            return $res;
        }
        $res['value'] = empty($data[$filed_list['file']]) && !isset($data[$filed_list['file']]) ? null : $data[$filed_list['file']];
        switch ($filed_list['type']){
            case 'text':
                if(!empty($filed_list['type_edit']) && in_array($filed_list['type_edit'],['date','datetime','time'])){
                    if($filed_list['type_edit'] == 'date'){
                        $res['value'] =  empty($res['value']) ? '-' : date('Y-m-d',(int) $res['value']);
                    } elseif($filed_list['type_edit'] == 'datetime') {
                        $res['value'] = empty($res['value']) ? '-' : date('Y-m-d H:i:s',(int) $res['value']);
                    }else {
                        $res['value'] = empty($res['value']) ? '-' : date('H:i:s',(int) $res['value']);
                    }
                }
                break;
            case 'fold':
                if(!empty($filed_list['type_edit']) && $filed_list['type_edit'] == 'link'){
                    $filed_list['type'] = 'link';
                    $_res = $this->getTableFiled($filed_list,$data);
                    $res['value'] = $_res['value'];
                }
                break;
            case 'link':
                $v_class = empty($filed_list['class_value']) ? null : $filed_list['class_value'];
                $filed_list['uri'] = $this->EditUri($filed_list['uri'],$data);
                if(!empty($filed_list['none']) && isset($data[$filed_list['none']['file']]) && $data[$filed_list['none']['file']] == $filed_list['none']['value']){
                    $res['value'] = '';
                }else {
                    $target = empty($filed_list['target']) ? '' : " target={$filed_list['target']}";
                    $res['value'] = "<a class='{$v_class}' href='{$filed_list['uri']}' {$target}>{$res['value']}</a>";
                }
                break;
            case 'edit':
                $data['id'] = empty($filed_list['id_edit']) ? $data['id'] : $data[$filed_list['id_edit']];
                $v_class = empty($filed_list['class_value']) ? null : $filed_list['class_value'];
                $res['value'] = "<input class='cx-ipt {$v_class} _fast' name='{$res['file']}' value='{$res['value']}' data-value='{$res['value']}' data-id='{$data['id']}' />";
                break;
            case 'radio':
                $res['value'] = !isset($filed_list['radio'][$data[$filed_list['file']]]) ? '' : $filed_list['radio'][$data[$filed_list['file']]];
                break;
            case 'switch':
                $data['id'] = empty($filed_list['id_edit']) ? $data['id'] : $data[$filed_list['id_edit']];
                $switch = $filed_list['switch'][$data[$filed_list['file']]];
                $switch_value = empty($switch['value']) && !isset($switch['value']) ? null : $switch['value'];
                $switch_value = empty($switch['class']) ? $switch_value : "<i class='cx-icon {$switch['class']} cx-text-f16'></i> {$switch_value}";
                $res['value'] = !empty($filed_list['type_edit']) && $filed_list['type_edit'] ? $switch_value : "<a class='_switch' data-name='{$res['file']}' data-id='{$data['id']}' title='更改{$res['title']}'>{$switch_value}</a>";
                break;
            case 'img':
                if(!empty($filed_list['type_edit']) && $filed_list['type_edit'] == 'array'){
                    $res['value'] = unserialize($res['value']);
                }else if(!empty($filed_list['type_edit']) && $filed_list['type_edit'] == 'array_see'){
                    $img_list = empty($res['value']) ? [] : json_decode($res['value'],true);
                    $img_val = '';
                    $width = !empty($filed_list['width']) ? $filed_list['width'] : null;
                    foreach ($img_list as $k => $v){
                        $v['uri'] = Upload::editadd($v['uri'],false);
                        $img_val .= "<img src='{$v['uri']}' width='{$width}' alt=''>";
                    }
                    $res['value'] = $img_val;
                }
                break;
            case 'imgsee':
                $width = !empty($filed_list['width']) ? $filed_list['width'] : null;
                $res['value'] = Upload::editadd($res['value'],false);
                $res['value'] = empty($res['value']) ? '' : "<img src='{$res['value']}' class='cx-img-responsive' width='{$width}' alt=''>";
                break;
            case 'btn':
                $data['id'] = empty($filed_list['id_edit']) ? $data['id'] : $data[$filed_list['id_edit']];
                $uri = null;
                $uri = url('create');
                $res['value'] = "<a class='cx-button-s cx-bor-green' href='{$uri}?pid={$data['id']}'>{$res['title']}</a>";
                break;
        }
        return $res;
    }
    /**
     * 转换按钮
     * @param $filed_list
     * @param $data
     * @return array
     */
    public function getTableBtn($filed_list,$data){
        $title = null;
        if(empty($filed_list['title'])){
            $title = $filed_list['type'] == 'edit' ? "编辑" : $title;
            $title = $filed_list['type'] == 'del' ? "删除" : $title;
        }
        $res = array(
            'title' => empty($filed_list['title']) ? $title : $filed_list['title'],
            'width' => empty($filed_list['width']) ? in_array($filed_list['type'],['edit','del','trash']) ? '60' : '100' : $filed_list['width'],
            'value' => '',
        );
        if(empty($data)){
            return $res;
        }
        $uri = null;
        $filed_list['uri'] = empty($filed_list['uri']) ? null : $this->EditUri($filed_list['uri'],$data);
        $icon = empty($filed_list['icon']) ? null : "<i class='cx-icon {$filed_list['icon']}'></i>";
        $class = empty($filed_list['class']) ? null : $filed_list['class'];
        $data['id'] = empty($filed_list['id_edit']) ? $data['id'] : $data[$filed_list['id_edit']];
        switch ($filed_list['type']){
            case 'abutton':
                $class = empty($class) ? "cx-button-s cx-bor-blue" : $class;
                $res['value'] = !empty($filed_list['text']) && $filed_list['text'] == true ? "<a class='{$class}' href='{$filed_list['uri']}'>{$icon}</a>" : "<a class='{$class}' href='{$filed_list['uri']}'>{$icon}{$res['title']}</a>";
                break;
            case 'radio':
                $_data = empty($filed_list['data'][$data[$filed_list['file']]]) ? null : $filed_list['data'][$data[$filed_list['file']]];
                $res['value'] = null;
                if(!empty($_data)){
                    $_data['uri'] = $this->EditUri($_data['uri'],$data);
                    $class = empty($_data['class']) ? "cx-button-s cx-bor-blue" : $_data['class'];
                    $icon = empty($_data['icon']) ? null : "<i class='cx-icon {$_data['icon']}'></i>";
                    $res['title'] = empty($_data['text']) ? $res['title'] : $_data['text'];
                    $res['value'] = !empty($filed_list['text']) && $filed_list['text'] == true ? "<a class='{$class}' href='{$_data['uri']}'>{$icon}</a>" : "<a class='{$class}' href='{$_data['uri']}'>{$icon}{$res['title']}</a>";
                }
                break;
            case 'edit':
                $uri = empty($filed_list['uri']) ? url('edit',array('id' => $data['id']))->build() : $filed_list['uri'];
                $res['value'] = "<a href='{$uri}'><i class='cx-icon cx-iconbianji cx-text-f16'></i></a>";
                break;
            case 'del':
                $_uri = empty($filed_list['uri']) ? '' : "data-uri='{$filed_list['uri']}'";
                $res['value'] = "<a class='cx-text-red _delete' data-id='{$data['id']}' {$_uri}><i class='cx-icon cx-icondelete cx-text-f16'></i></a>";
                break;
            case 'trash':
                $res['value'] = "<a class='cx-text-green _trash' data-id='{$data['id']}'><i class='cx-icon cx-iconhuanyuan cx-text-f16'></i></a>";
                break;
        }
        return $res;
    }
    /**
     * 转换字段到表单
     * @param $filed_list
     * @param $data
     * @return array
     */
    public function getFormFiled($filed_list,$data){
        //  检测是否为必填项
        $required = !empty($filed_list['required']) && $filed_list['required'] === true ? true : false;
        $disabled = !empty($filed_list['disabled']) && $filed_list['disabled'] === true ? true : false;
        $res = array(
            'title' => $required ? "<label class='cx-label' for='{$filed_list['file']}'>{$filed_list['title']}(<i class='cx-text-red'>*</i>)</label>" : "<label class='cx-label' for='{$filed_list['file']}'>{$filed_list['title']}</label>",
            'value' => '',
            'type' => empty($filed_list['type_edit']) ? 'text' : $filed_list['type_edit'],
            'group' => empty($filed_list['type_group']) ? '基本信息' : $filed_list['type_group'],
        );
        //  拼接验证参数
        if($required){
            $required_list = !empty($filed_list['required_list']) ? explode('|',$filed_list['required_list']) : array();
            array_push($required_list,'required');
            $required_list = implode('|',array_unique($required_list));
            $required = " required lay-verify='{$required_list}'";
        }else{
            $required = null;
        }
        $required = $required ? $required : null;
        $disabled = $disabled ? "disabled" : null;
        //  更改默认提示
        $filed_list['title'] = !empty($filed_list['title_edit']) ? $filed_list['title_edit'] : "{$filed_list['title']}";
        //  添加默认值
        $filed_list['default'] = empty($filed_list['default']) && !isset($filed_list['default']) ? null : $filed_list['default'];
        //  添加小图标
        $type_unit = empty($filed_list['type_unit']) ? null : ' / '.$filed_list['type_unit'];
        switch ($filed_list['type']){
            case 'map':
                $default = isset($data[$filed_list['file']]) ? json_decode($data[$filed_list['file']],true) : [];
                $default['title'] = empty($default['title']) ? '' : $default['title'];
                $default['latitude'] = empty($default['latitude']) ? '' : $default['latitude'];
                $default['longitude'] = empty($default['longitude']) ? '' : $default['longitude'];
                $res['value'] = "<input type='text' name='{$filed_list['file']}[title]' id='{$filed_list['file']}maptxt' placeholder='请选择地图位置' class='cx-ipt {$filed_list['file']}maptxt' value='{$default['title']}'>
<input type='hidden' name='{$filed_list['file']}[latitude]' placeholder='请选择地图位置' required class='ipt {$filed_list['file']}latitude' value='{$default['latitude']}'>
<input type='hidden' name='{$filed_list['file']}[longitude]' placeholder='请选择地图位置' required class='ipt {$filed_list['file']}longitude' value='{$default['longitude']}'>
<div class='layout' style='height: 400px;'>
    <div id='{$filed_list['file']}maps' style='height: 100%;'></div>
    <div id='{$filed_list['file']}mapsend' style='height: 100%;display: none;'></div>
</div>
<script>
var map = new BMap.Map('{$filed_list['file']}maps'),
    top_left_navigation = new BMap.NavigationControl(),
    t = new BMap.Autocomplete({'input':'{$filed_list['file']}maptxt','location':map}),
    myCity = new BMap.LocalCity();
    myCity.get(myFun);
    let _l = $(`.{$filed_list['file']}latitude`).val(),
        _y = $(`.{$filed_list['file']}longitude`).val();
    function myFun(result){
        if(_l != '' && _y != ''){
            map.centerAndZoom(new BMap.Point(_l,_y),15);  //  设置地图级别
            map.clearOverlays();
            let marker = new BMap.Marker(new BMap.Point(_l,_y));
            map.addOverlay(marker);    //添加标注
            marker.disableDragging();
            $(`.{$filed_list['file']}maptxt`).val('{$default['title']}');
        }else{
            map.centerAndZoom(result.name,15);
        }
    }
    map.addControl(top_left_navigation);    //  添加控制条
    map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放
    map.addEventListener('click',function(e){
        autoAdd(e.point);
    });
     t.addEventListener('onhighlight', function(e) {  //鼠标放在下拉列表上的事件
        var str = '',
        _value = e.fromitem.value,
        value = '';
        if (e.fromitem.index > -1) {
            value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
        }
        str = 'FromItem < br />index = ' + e.fromitem.index + ' < br />value = ' + value;
        value = '';
        if (e.toitem.index > -1) {
            _value = e.toitem.value;
            value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
        }
        str += '< br />ToItem < br />index = ' + e.toitem.index + ' < br />value = ' + value;
        G('{$filed_list['file']}mapsend').innerHTML = str;
    });
    function G(id) {
        return document.getElementById(id);
    }
    var myValue;
    t.addEventListener('onconfirm', function(e) {    //鼠标点击下拉列表后的事件
        var _value = e.item.value;
        myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
        G('{$filed_list['file']}mapsend').innerHTML = 'onconfirm < br />index = ' + e.item.index + ' < br />myValue = ' + myValue;
        setPlace();
    });
    function setPlace(){
        map.clearOverlays();    //清除地图上所有覆盖物
        function myFun(){
            var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果
            map.centerAndZoom(pp, 18);
            autoAdd(pp);
        }
        var local = new BMap.LocalSearch(map, { //智能搜索
            onSearchComplete: myFun
        });
        local.search(myValue);
    }
                //  自动添加标注
    function autoAdd(points = '') {
        map.clearOverlays();
        let marker = new BMap.Marker(points),
            geoc = new BMap.Geocoder();
        map.addOverlay(marker);    //添加标注
        marker.disableDragging();
        geoc.getLocation(points, function(rs){
			var addComp = rs.addressComponents;
			$(`.{$filed_list['file']}maptxt`).val(addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber);
		});
        $(`.{$filed_list['file']}latitude`).val(points.lng);
        $(`.{$filed_list['file']}longitude`).val(points.lat);
    }
</script>";
                break;
            case 'bgccont':
                $type_radio = $filed_list['form_geturitype'] == '1' ? 'true' : 'false';
                $initValue = '';
                if(!empty($data[$filed_list['file']])) {
                    $initValue = $filed_list['form_geturitype'] == '1' ? "'{$data[$filed_list['file']]}'" : explode(',', $data[$filed_list['file']]);
                }
                $_title = "title";
                $_value = "id";
                if(!empty($filed_list['form_js'])){
                    $_js = explode(',',$filed_list['form_js']);
                    if(!empty($_js)){
                        $_njs = [];
                        foreach ($_js as $k => $v){
                            $v = explode('=',$v);
                            if(count($v) < 2){
                                continue;
                            }
                            $_njs[$v['0']] = $v['1'];
                        }
                        $_title = empty($_njs['title']) ? $_title : $_njs['title'];
                        $_value = empty($_njs['value']) ? $_title : $_njs['value'];
                    }
                }
                $uri = url($filed_list['form_geturi'])->build();
                $res['value'] = "<input type='hidden' class='{$filed_list['file']}val' name='{$filed_list['file']}' value=''><div id='_{$filed_list['file']}'></div>
<script>
let _{$filed_list['file']} = xmSelect.render({
el: `#_{$filed_list['file']}`,
radio: {$type_radio},
clickClose: {$type_radio},
initValue: [{$initValue}],
prop: {name: '{$_title}',value: '{$_value}'},
paging: true,
pageEmptyShow: false,
filterable: true,
pageRemote: true,
remoteSearch: true,
remoteMethod: function(val, cb, show){
    if(!val){
        return  cb([]);
    }
    _{$filed_list['file']}post(\"{$uri}\",{key:val},function(res){
         let ress = res.data;
           ress.data = ress.total == '0' ? [{id: val,title: '创建 - ' + val}] : ress.data;
           ress.last_page = ress.total == '0' ? 1 : ress.last_page;
           cb(ress.data, ress.last_page);
        },function(){
            cb([], 0);
        }
    );
},
on:function(data){
    let arr = [];
    \$.each(data.arr,function(index,item){
        arr.push(item.{$_value});
    });
    \$(`.{$filed_list['file']}val`).val(arr.toString());
},}),
_{$filed_list['file']}post = function (uri,data = '',success = '',error = '') {
    $.ajax({
        type: \"post\",
        dataType: \"json\",
        data: data,
        url: uri,
        success:function (res) {
            if(typeof success == 'function'){
                success(res);
            }
        },
        error:function (res) {
            if(typeof error == 'function'){
                error(res);
            }
        }
    });
};
_{$filed_list['file']}post(\"{$uri}\",'',function(res){
    let ress = res.data.data;
    _{$filed_list['file']}.update({
		data: ress,
		autoRow: true,
	})
});
</script>";
                break;
            case 'callcont':
                $default = isset($data[$filed_list['file']]) ? $data[$filed_list['file']] : $filed_list['default'];
                $type_radio = $filed_list['form_geturitype'] == '1' ? 'true' : 'false';
                $initValue = '';
                if(!empty($data[$filed_list['file']])) {
                    $initValue = $filed_list['form_geturitype'] == '1' ? "'{$data[$filed_list['file']]}'" : $data[$filed_list['file']];
                }
                $_title = "title";
                $_value = "id";
                if(!empty($filed_list['form_js'])){
                    $_js = explode(',',$filed_list['form_js']);
                    if(!empty($_js)){
                        $_njs = [];
                        foreach ($_js as $k => $v){
                            $v = explode('=',$v);
                            if(count($v) < 2){
                                continue;
                            }
                            $_njs[$v['0']] = $v['1'];
                        }
                        $_title = empty($_njs['title']) ? $_title : $_njs['title'];
                        $_value = empty($_njs['value']) ? $_title : $_njs['value'];
                    }
                }
                $uri = url($filed_list['form_geturi'])->build();
                $res['value'] = "<input type='hidden' class='{$filed_list['file']}val' name='{$filed_list['file']}' value=''><div id='_{$filed_list['file']}'></div>
<script>
let _{$filed_list['file']} = xmSelect.render({
el: `#_{$filed_list['file']}`,
radio: {$type_radio},
clickClose: {$type_radio},
initValue: [{$initValue}],
prop: {name: '{$_title}',value: '{$_value}'},
paging: true,
pageEmptyShow: false,
filterable: true,
pageRemote: true,
remoteSearch: true,
remoteMethod: function(val, cb, show){
    if(!val){
        return  cb([]);
    }
    _{$filed_list['file']}post(\"{$uri}\",{key:val},function(res){
         let ress = res.data;
           cb(ress.data, ress.last_page);
        },function(){
            cb([], 0);
        }
    );
},
on:function(data){
    let arr = [];
    \$.each(data.arr,function(index,item){
        arr.push(item.{$_value});
    });
    \$(`.{$filed_list['file']}val`).val(arr.toString());
},}),
_{$filed_list['file']}post = function (uri,data = '',success = '',error = '') {
    $.ajax({
        type: \"post\",
        dataType: \"json\",
        data: data,
        url: uri,
        success:function (res) {
            if(typeof success == 'function'){
                success(res);
            }
        },
        error:function (res) {
            if(typeof error == 'function'){
                error(res);
            }
        }
    });
};
_{$filed_list['file']}post(\"{$uri}\",{{$_value}:'{$default}'},function(res){
    let ress = res.data.data;
    _{$filed_list['file']}.update({
		data: ress,
		autoRow: true,
	})
});
</script>";
                break;
            case 'editor':
                $default = isset($data[$filed_list['file']]) ? Upload::editadd($data[$filed_list['file']],false) : $filed_list['default'];
                $res['value'] = "<div class='layout {$filed_list['file']}edit cx-bor' style='height: 400px;'>{$default}</div><textarea class='cx-hidden' name='{$filed_list['file']}' id='{$filed_list['file']}edit' >{$default}</textarea><script>newckeditor(\"{$filed_list['file']}edit\")</script>";
                break;
            case 'icon':
                $default = isset($data[$filed_list['file']]) ? $data[$filed_list['file']] : $filed_list['default'];
                $del_style = empty($default) ? "display: none;" : null;
                $icon_default = empty($default) ? "cx-icon cx-iconadd" : $default;
                $res['value'] = "<div class='layout cx-fex-l cx-fex-itemsc'><div style='width: 50px;height: 50px;' class='cx-bor cx-bor-ra cx-bor-black-3'><div class='layout cx-fex-c cx-fex-itemsc' style='height: 100%;'><i class='{$icon_default} {$filed_list['file']}-img' style='font-size: 3rem; line-height: 1'></i></div><input type='hidden' class='{$filed_list['file']}-val' name='{$filed_list['file']}' value='{$default}'></div><div class='cx-fex-c cx-fex-itemsc cx-mag-l10'><a class='cx-button-s cx-bg-green' onclick=\"addicon('{$filed_list['file']}')\"><i class='cx-mag-r5 cx-icon cx-iconupload'></i>选择图标</a><a class='cx-button-s cx-bg-red cx-mag-l5 {$filed_list['file']}-del' style='{$del_style}' onclick=\"delicon('{$filed_list['file']}')\">删除图标</a></div></div>";
                break;
            case 'text':
                $_type = empty($filed_list['type_edit']) ? 'text' : $filed_list['type_edit'];
                $default = isset($data[$filed_list['file']]) ? $data[$filed_list['file']] : $filed_list['default'];
                $default = $_type == 'password' ? null : $default;
                $_date = null;
                if($_type == 'time'){
                    $_date =  '_time';
                    $_type =  'text';
                    $default = empty($default) ? '00:00:00' : $default;
                }else if($_type == 'date'){
                    $_date =  '_date';
                    $_type =  'text';
                    $default = !empty($default) && is_int($default) ? date('Y-m-d',$default) : '';
                }else if($_type == 'datetime'){
                    $_date =  '_datetime';
                    $_type =  'text';
                    $default = !empty($default) && is_int($default) ? date('Y-m-d H:i:s',$default) : '';
                }
                $res['value'] = "<input id='{$filed_list['file']}' class='cx-ipt {$_date}' type='{$_type}' {$disabled} placeholder='请输入{$filed_list['title']}' $required name='{$filed_list['file']}' value='{$default}'>";
                if(!empty($type_unit)){
                    $res['value'] = "<div class='layout cx-form-itemnowarp'>{$res['value']}<div class='cx-ipt-icon cx-bg-white-4'>{$type_unit}</div></div>";
                }
                break;
            case 'textarea':
                $default = isset($data[$filed_list['file']]) ? $data[$filed_list['file']] : $filed_list['default'];
                $rows = empty($filed_list['rows']) ? '3' : $filed_list['rows'];
                $res['value'] = "<textarea id='{$filed_list['file']}' rows='{$rows}' class='cx-ipt' placeholder='请输入{$filed_list['title']}' $required name='{$filed_list['file']}'>{$default}</textarea>";
                break;
            case 'rescont':
                $default = isset($data[$filed_list['file']]) ? $data[$filed_list['file']] : $filed_list['default'];
                $rows = empty($filed_list['rows']) ? '3' : $filed_list['rows'];
                $res['value'] = "<textarea id='{$filed_list['file']}' rows='{$rows}' class='cx-ipt' placeholder='请输入{$filed_list['title']}' $required name='{$filed_list['file']}'>{$default}</textarea>";
                break;
            case 'checkbox':
                $default = isset($data[$filed_list['file']]) ? $data[$filed_list['file']] : $filed_list['data']['default'];
                $default = !isset($default) ? [] : $default;
                $default = is_array($default) ? $default : explode(',',$default);
                foreach ($filed_list['data']['list'] as $k => $v){
                    $checked = in_array($k,$default) ? 'checked' : '';
                    $res['value'] .= "<input type='checkbox' name='{$filed_list['file']}[]' value='{$k}' title='{$v} {$type_unit}' {$checked} {$required} lay-filter='{$filed_list['file']}'>";
                }
                break;
            case 'checkboxlist':
                $_default = isset($data[$filed_list['file']]) ?  $data[$filed_list['file']] : '';
                $default = isset($data[$filed_list['file']]) ?  explode(',',$data[$filed_list['file']]) : [];
                $_list = $filed_list['data'];
                $_uri = empty($filed_list['uri']) ? '' : $filed_list['uri'];
                $_value = '';
                foreach ($_list['data'] as $k => $v){
                    $checked = in_array($v['id'],$default) ? 'checked' : '';
                    $_value .= "<li class='cx-pad-tb5'><input type='checkbox' value='{$v['id']}' title='{$v['title_display']}' {$checked} lay-skin='primary' lay-filter='{$filed_list['file']}'></li>";
                }
                $res['value'] = "<div class='layout cx-bor cx-bor-black-1' style='height:300px;overflow-y: auto;'><ul class='layout cx-fex-l cx-fex-column cx-pad-lr15 _{$filed_list['file']}list'>{$_value}</ul></div><input type='hidden' class='{$filed_list['file']}val' name='{$filed_list['file']}' {$required} value='{$_default}'><div id='{$filed_list['file']}page' class='layout cx-fex-l cx-fex-itemsc'></div><script>
layui.use(['laypage','form'], function(){ let laypage = layui.laypage, layform = layui.form, {$filed_list['file']}UrlPapram = function (name) { let reg = new RegExp(\"(^|&)\" + name + \"=([^&]*)(&|$)\"), r = window.location.search.substr(1).match(reg); if (r != null) return unescape(r[2]); return null; };";
                if(!empty($_uri)) {
                    $res['value'] .= " laypage.render({ elem: '{$filed_list['file']}page', count: '{$_list['total']}', limit: '{$_list['per_page']}', groups: '0', layout: ['prev','next'], jump: function(obj, first){ let url = '{$_uri}'; if(url.indexOf('page=') > 0){ let v = {$filed_list['file']}UrlPapram('page'); url = v != null ? url.replace(`page=\${v}`,`page=\${obj.curr}`) : url.replace(`page=`,`page=\${obj.curr}`); }else{ url = url.indexOf(\"?\") > 0 ? url + `&page=\${obj.curr}` : url + `?page=\${obj.curr}`; } if(!first){ \$.get(url,'',function(res) { if(res.code == '1'){ let _data = \$(`.{$filed_list['file']}val`).val(); _data = _data.split(','); \$(`._{$filed_list['file']}list`).html(''); \$.each(res.data.data,function(index,item) { let _checked = _data.includes(String(item.id)) ? `checked` : ''; \$(`._{$filed_list['file']}list`).append(`<li class='cx-pad-tb5'><input type='checkbox' value='\${item.id}' title='\${item.title}' \${_checked} lay-skin='primary' lay-filter='{$filed_list['file']}'></li>`); }); layform.render('checkbox'); } }); } } });";
                }
                $res['value'] .= " layform.on('checkbox({$filed_list['file']})', function(data){ let _data = \$(`.{$filed_list['file']}val`).val(); _data = _data.split(','); if(data.elem.checked){ _data.push(data.value); } else { \$.each(_data,function(index,item) { if(item == data.value){ _data.splice(index,1);  } }); } \$(`.{$filed_list['file']}val`).val(_data.toString()); }); }); </script>";
                break;
            case 'radio':
                $default = isset($data[$filed_list['file']]) ? $data[$filed_list['file']] : $filed_list['data']['default'];
                foreach ($filed_list['data']['list'] as $k => $v){
                    $checked = $default == $k ? 'checked' : '';
                    $res['value'] .= "<input type='radio' name='{$filed_list['file']}' value='{$k}' title='{$v} {$type_unit}' {$checked} {$required} lay-filter='{$filed_list['file']}'>";
                }
                if(!empty($filed_list['file_link'])){
                    $file_link = json_encode($filed_list['file_link'],JSON_UNESCAPED_UNICODE);
                    $res['value'] .= "<script> layui.use('form', function(){ let form = layui.form, l_d = {$file_link}; function radio{$filed_list['file']}(o){ \$.each(l_d,function(index,item){ if(o == index){ return true;  } \$.each(item,function(index1,item1){
    \$(`input[name='\${item1}']`).closest(`.cx-form-group`).addClass(`cx-hidden`);
    \$(`textarea[name='\${item1}']`).closest(`.cx-form-group`).addClass(`cx-hidden`);
    \$(`input:checkbox[name='\${item1}[]']`).closest(`.cx-form-group`).addClass(`cx-hidden`);
    \$(`select[name='\${item1}']`).closest(`.cx-form-group`).addClass(`cx-hidden`); });
}); \$.each(l_d[o],function(index,item){
    \$(`input[name='\${item}']`).closest(`.cx-form-group`).removeClass(`cx-hidden`);
    \$(`textarea[name='\${item}']`).closest(`.cx-form-group`).removeClass(`cx-hidden`);
    \$(`input:checkbox[name='\${item}[]']`).closest(`.cx-form-group`).removeClass(`cx-hidden`);
    \$(`select[name='\${item}']`).closest(`.cx-form-group`).removeClass(`cx-hidden`);
}); } form.on('radio({$filed_list['file']})', function(data){ radio{$filed_list['file']}(data.value);}); radio{$filed_list['file']}({$default}); }); </script>";
                }
                break;
            case 'select':
                $default = isset($data[$filed_list['file']]) ? $data[$filed_list['file']] : $filed_list['data']['default'];
                $res['value'] = "<select name='{$filed_list['file']}' {$required} {$disabled} lay-filter='{$filed_list['file']}'>";
                $default_edit = !empty($filed_list['data']['default_edit']) ? true : false;
                if(!$default_edit){
                    $res['value'] .= "<option value=''>{$filed_list['title']}</option>";
                }
                foreach ($filed_list['data']['list'] as $k => $v){
                    $selected = $default == $k ? 'selected' : '';
                    $res['value'] .= "<option value=\"{$k}\" {$selected}>{$v} {$type_unit}</option>";
                }
                $res['value'] .= "</select>";
                if(!empty($filed_list['file_link'])){
                    $file_link = json_encode($filed_list['file_link'],JSON_UNESCAPED_UNICODE);
                    $res['value'] .= "<script> layui.use('form', function(){ let form = layui.form, l_d = {$file_link}; 
function select{$filed_list['file']}(o){
    \$.each(l_d,function(index,item){
        if(o == index){ return true;  }
        \$.each(item,function(index1,item1){ \$(`input[name='\${item1}']`).closest(`.cx-form-group`).addClass(`cx-hidden`); \$(`textarea[name='\${item1}']`).closest(`.cx-form-group`).addClass(`cx-hidden`);  \$(`select[name='\${item1}']`).closest(`.cx-form-group`).addClass(`cx-hidden`); });  }); 
    if(l_d[o]){
        \$.each(l_d[o],function(index,item){ \$(`input[name='\${item}']`).closest(`.cx-form-group`).removeClass(`cx-hidden`); \$(`textarea[name='\${item}']`).closest(`.cx-form-group`).removeClass(`cx-hidden`);   \$(`select[name='\${item}']`).closest(`.cx-form-group`).removeClass(`cx-hidden`); }); 
    }
}
    form.on('select({$filed_list['file']})', function(data){ select{$filed_list['file']}(data.value);}); select{$filed_list['file']}(\"{$default}\"); }); </script>";
                }
                break;
            case 'upload':
                $accept = empty($filed_list['upload_accept']) ? 'image' : $filed_list['upload_accept'];
                $autoup = empty($filed_list['upload_autoup']) ? null : " data-autoup='{$filed_list['upload_autoup']}'";
                $upnum = empty($filed_list['upload_filenum']) ? null : " data-filenum='{$filed_list['upload_filenum']}'";
                $_like = empty($filed_list['like']) || !isset($filed_list['like']) && !$filed_list['like'] ? null : " data-like='1'";
                //  处理显示模板
                $data[$filed_list['file']] = empty($data[$filed_list['file']]) ? $filed_list['default'] : $data[$filed_list['file']];
                $list_temp = null;
                if(!empty($data[$filed_list['file']])){
                    if(!empty($filed_list['type_edit']) && $filed_list['type_edit'] == 'array'){
                        $new_filelist = is_array($data[$filed_list['file']]) ? $data[$filed_list['file']] : json_decode($data[$filed_list['file']],true);
                        foreach ($new_filelist as $k => $v){
                            $uri = empty($v['uri']) ? null : Upload::editadd($v['uri'],false);
                            $_likes = empty($_like) ? null : "<div class='layout cx-form-itemnowarp' style='flex-grow: 0'>
        <div class='cx-ipt-icon cx-text-f12'>链接</div>
        <input type='text' class='{$filed_list['file']}{$k}like cx-ipt-s' name='{$filed_list['file']}[{$k}][like]' value='{$v['like']}'>
    </div>";
                            if($accept != 'image'){
                                $_likes = '';
                            }
                            $_img = "<img class='{$filed_list['file']}{$k}img cx-img-responsive'  src='{$uri}' onerror=\"this.src='this.src='/public/wormcms/img/imgnone.jpg'\" alt=''>";
                            if($accept == 'video'){
                                $_img = "<video class='{$filed_list['file']}{$k}video cx-img-responsive'  src='{$uri}' controls='controls'>您的浏览器不支持 video 标签</video>";
                            }
                            $list_temp .= "<div class='cx-xs8 {$filed_list['file']}{$k}box cx-pad-a5'>
<div class='layout cx-fex-l cx-bor'>
<div class='cx-xl8 cx-borright'>
<div class='cx-bg-img1x1'>
    <div class='cx-bg-img cx-fex-c cx-fex-c'>{$_img}</div>
</div>
</div>
<div class='cx-xl16 cx-fex-a cx-fex-column cx-form-group cx-pad-l5'>
    <div class='layout cx-form-itemnowarp' style='flex-grow: 0'>
        <div class='cx-ipt-icon cx-text-f12'>名称</div>
        <input type='text' class='{$filed_list['file']}{$k}title cx-ipt-s' name='{$filed_list['file']}[{$k}][title]' value='{$v['title']}'>
    </div>
    {$_likes}
   <div class='layout cx-form-itemnowarp' style='flex-grow: 0'>
        <div class='cx-ipt-icon cx-text-f12'>排序</div>
        <input type='text' class='{$filed_list['file']}{$k}sort cx-ipt-s' name='{$filed_list['file']}[{$k}][sort]' value='{$v['sort']}'>
    </div>
     <div class='layout cx-form-itemnowarp' style='flex-grow: 0'>
        <div class='cx-ipt-icon cx-text-f12'>大小</div>
        <input type='text' disabled class='cx-ipt-s' value='{$this->FileSize($v['size'])}'>
    </div>
     <div class='layout cx-form-itemnowarp' style='flex-grow: 0'>
        <a class='cx-button-s cx-bg-red cx-click' data-type='delelement' data-cid='.{$filed_list['file']}{$k}box' data-name='.{$filed_list['file']}{$k}val'>删除</a>
    </div>
    <input type='hidden' class='{$filed_list['file']}{$k}size' name='{$filed_list['file']}[{$k}][size]' value='{$v['size']}'>
</div>
</div></div><input type='hidden' class='{$filed_list['file']}{$k}val' name='{$filed_list['file']}[{$k}][uri]' value='{$uri}'>";
                        }
                    }else{
                        $uri = empty($data[$filed_list['file']]) ? null : Upload::editadd($data[$filed_list['file']],false);
                        $list_temp = "<div class='cx-xs3 {$filed_list['file']}box cx-pad-a5'><div class='cx-media-img'><img class='{$filed_list['file']}img' src='{$uri}' alt=''  onerror=\"this.src='/public/wormcms/img/imgnone.jpg'\"><input type='hidden' class='{$filed_list['file']}val' name='{$filed_list['file']}' value='{$uri}'></div><a class='cx-button-s cx-bg-red cx-click' data-type='delelement' data-cid='.{$filed_list['file']}box'>删除</a></div>";
                    }
                }
                $list_temp = "<div class='layout {$filed_list['file']}-list cx-fex-l'>{$list_temp}</div>";
                $res['value'] = "<div class='layout cx-fex-l cx-fex-column'><div class='layout cx-fex-l cx-fex-itemsc'><a class='cx-button cx-bor-green _upload' data-name='{$filed_list['file']}' data-exp='{$accept}' {$_like} {$upnum} {$autoup}>上传</a></div>{$list_temp}</div>";
                break;
            case 'addchina':
                $default = empty($data[$filed_list['file']]) ? '0' : $data[$filed_list['file']];
                $_default = empty($data['_'.$filed_list['file']]) ? '0' : $data['_'.$filed_list['file']];
                $_list = $filed_list['data'];
                if(!empty($default)){
                    $cMdoel = new AddChinacode();
                    $_list = $cMdoel->getPuplist($_default);
                }
                $_select = '';
                foreach ($_list as $k => $v){
                    $_select .= "<select class='{$filed_list['file']}' data-name='{$filed_list['file']}' lay-filter='{$filed_list['file']}select'><option value=''>请选择...</option>";
                    foreach ($v['list'] as $k1 => $v1){
                        $_selected = !empty($v['default']) && $v1['id'] == $v['default'] ? 'selected' : '';
                        $_select .= "<option {$_selected} value='{$v1['id']}'>{$v1['title']}</option>";
                    }
                    $_select .= "</select>";
                }
                $_uri = url('api/addchinacode/index');
                $res['value'] = "<div class='layout cx-fex-l cx-fex-itemsc'>{$_select}<input type='hidden' class='{$filed_list['file']}val' name='{$filed_list['file']}' value='{$_default}'></div>
<script>
layui.use('form', function(){
  let layform = layui.form,
      {$filed_list['file']}get = function(o,successCallback = '') {
        $.get('{$_uri}',{pid:o},function(res) {
              successCallback && successCallback(res);
          });
      };
  layform.on('select({$filed_list['file']}select)', function(data){
       let o = data.elem,
           d = $(o).data();
       \$(`.{$filed_list['file']}val`).val(data.value);
      {$filed_list['file']}get(data.value,function(res) {
          if(res.code != '1' || res.data.length < 1){
              return false;
          }
          let nelist = $(o).nextAll(`.\${d.name}`);
          if(nelist.length > 0){
          \$.each(nelist,function (index,item) {
                let netxt = $($(item).children('option').get(0)).text();
                $(item).html('').append(`<option value=''>\${netxt}</option>`);
                if(index == '0'){
                    \$.each(res.data,function (index1,item1) {
                        $(item).append(`<option value='\${item1.id}'>\${item1.title}</option>`);
                    });
                }
            });
          } else {
              let _oplist = '';
              $.each(res.data,function (index,item) {
                  _oplist += `<option value='\${item.id}'>\${item.title}</option>`;
              });
             $(o).after(`<select class='{$filed_list['file']}' data-name='{$filed_list['file']}' lay-filter='{$filed_list['file']}select'><option value=''>请选择...</option>\${_oplist}</select>`); 
          }
        layform.render('select');
      });
  });
});
</script>";
                break;
            case 'chinacode':
                $default = empty($data[$filed_list['file']]) ? '0' : $data[$filed_list['file']];
                $_default = empty($data['_'.$filed_list['file']]) ? '0' : $data['_'.$filed_list['file']];
                $_list = empty($filed_list['data']) ? [] : $filed_list['data'];
                if(empty($_list) && empty($default)){
                    $cMdoel = new Chinacode();
                    $_list['0']['list'] = $cMdoel->getList(['parzoneid' => '0']);
                } else if(!empty($default)){
                    $cMdoel = new Chinacode();
                    $_default = empty($_default) ? $default : $_default;
                    $_list = $cMdoel->getPiplist($_default);
                }
                $_select = '';
                foreach ($_list as $k => $v){
                    $_select .= "<select class='{$filed_list['file']}' data-name='{$filed_list['file']}' lay-filter='{$filed_list['file']}select'><option value=''>请选择...</option>";
                    foreach ($v['list'] as $k1 => $v1){
                        $_selected = !empty($v['value']) && $v1['zoneid'] == $v['value'] ? 'selected' : '';
                        $_select .= "<option {$_selected} value='{$v1['zoneid']}'>{$v1['zonename']}</option>";
                    }
                    $_select .= "</select>";
                }
                $_uri = url('api/chinacode/index');
                $res['value'] = "<div class='layout cx-fex-l cx-fex-itemsc'>{$_select}<input type='hidden' class='{$filed_list['file']}val' name='{$filed_list['file']}' value='{$_default}'></div>
<script>
layui.use('form', function(){
  let layform = layui.form,
      {$filed_list['file']}get = function(o,successCallback = '') {
        $.get('{$_uri}',{parzoneid:o},function(res) {
              successCallback && successCallback(res);
          });
      };
  layform.on('select({$filed_list['file']}select)', function(data){
       let o = data.elem,
           d = $(o).data();
       \$(`.{$filed_list['file']}val`).val(data.value);
      {$filed_list['file']}get(data.value,function(res) {
          if(res.code != '1' || res.data.length < 1){
              return false;
          }
          let nelist = $(o).nextAll(`.\${d.name}`);
          if(nelist.length > 0){
          \$.each(nelist,function (index,item) {
                let netxt = $($(item).children('option').get(0)).text();
                $(item).html('').append(`<option value=''>\${netxt}</option>`);
                if(index == '0'){
                    \$.each(res.data,function (index1,item1) {
                        $(item).append(`<option value='\${item1.zoneid}'>\${item1.zonename}</option>`);
                    });
                }
            });
          } else {
              let _oplist = '';
              $.each(res.data,function (index,item) {
                  _oplist += `<option value='\${item.zoneid}'>\${item.zonename}</option>`;
              });
             $(o).after(`<select class='{$filed_list['file']}' data-name='{$filed_list['file']}' lay-filter='{$filed_list['file']}select'><option value=''>请选择...</option>\${_oplist}</select>`); 
          }
        layform.render('select');
      });
  });
});
</script>";
                break;
            case 'bindmodel':
                $default = empty($data[$filed_list['file']]) ? $filed_list['default'] : $data[$filed_list['file']];
                $_default = empty($data["_".$filed_list['file']]) ? '' : $data["_".$filed_list['file']];
                $res['value'] = "<div class='layout cx-fex-l cx-fex-itemsc'>{$default}<input type='hidden' class='{$filed_list['file']}val' name='{$filed_list['file']}' value='{$_default}'></div>";
                break;
            case 'fielgroup':
                $_button = empty($filed_list['button']) ? "增加参数" : $filed_list['button'];
                $_class = empty($filed_list['class']) ? "cx-button cx-bg-blue" : $filed_list['class'];
                $_value = $_jsvalue = '';
                //  生成js文件
                foreach ($filed_list['data'] as $k => $v){
                    $_jsvalue .= "<div class='layout cx-fex-l cx-pad-a5 cx-mag-b10 cx-bg-white-1 {$filed_list['file']}-one'>";
                    foreach ($v as $k1 => $v1){
                        $v1['file'] = $filed_list['file'].'[_jsnum_]'.'['.$v1['file'].']';
                        $_v = $this->getFormFiled($v1,$data);
                        $_jsvalue .= "<div class='cx-xs6 cx-xl12 cx-pad-lr5'>{$_v['title']}{$_v['value']}</div>";
                    }
                    $_jsvalue .= "<div class='cx-pad-lr5'><label class='cx-label'></label><div class='layout cx-form-itemnowarp'><a class='cx-button cx-bg-red' onclick='{$filed_list['file']}del(this)'>删除</a></div></div></div>";
                }
                //  生成数据文件
                if(!empty($data[$filed_list['file']])){
                    foreach ($data[$filed_list['file']] as $key => $val){
                        foreach ($filed_list['data'] as $k => $v){
                            $_value .= "<div class='layout cx-fex-l cx-pad-a5 cx-mag-b10 cx-bg-white-1 {$filed_list['file']}-one'>";
                            foreach ($v as $k1 => $v1){
                                $_v = $this->getFormFiled($v1,$val);
                                $_v1file = $filed_list['file'].'['.$key.']'.'['.$v1['file'].']';
                                $_v1value = "<div class='cx-xs6 cx-xl12 cx-pad-lr5'>{$_v['title']}{$_v['value']}</div>";
                                $_value .= str_replace($v1['file'],$_v1file,$_v1value);
                            }
                            $_value .= "<div class='cx-pad-lr5'><label class='cx-label'></label><div class='layout cx-form-itemnowarp'><a class='cx-button cx-bg-red' onclick='{$filed_list['file']}del(this)'>删除</a></div></div></div>";
                        }
                    }
                }
                $res['value'] = "<div class='{$filed_list['file']}view'>{$_value}</div><a class='{$_class}' onclick='{$filed_list['file']}addview()'>{$_button}</a><script>
function {$filed_list['file']}addview() {
    let _n = \$(`.{$filed_list['file']}-one`).length,
        _v = `{$_jsvalue}`,
        _r = new RegExp(`_jsnum_`,'g');
  \$(`.{$filed_list['file']}view`).append(_v.replace(_r,_n));
}
function {$filed_list['file']}del(o){
    $(o).closest(`.{$filed_list['file']}-one`).remove();
}
</script>";
                break;
        }
        if(!empty($filed_list['tip'])){
            $res['value'] = "{$res['value']}<div class='layout cx-text-black-3'>{$filed_list['tip']}</div>";
        }
        if(empty($data)){
            return $res;
        }
        return $res;
    }
    /**
     * @param $filed_data 转换为储存字段
     */
    public function setFormFiles($filed_data){
        $_data[$filed_data['file']] = $filed_data['value'];
        switch ($filed_data['type']){
            case 'map':
                $_data[$filed_data['file']] = empty($_data[$filed_data['file']]) ? '' : json_encode($_data[$filed_data['file']],JSON_UNESCAPED_UNICODE);
                break;
            case 'editor':
                $img_data = [
                    'oldimg' => empty($filed_data['_value']) ? null : $filed_data['_value'],
                    'newimg' => $filed_data['value'],
                ];
                $_data[$filed_data['file']] = Upload::setEditOr($img_data, date('Y-m', time()));
                break;
            case 'text':
                if(!empty($filed_data['type_edit']) && in_array($filed_data['type_edit'],['date','datetime',])){
                    $_data[$filed_data['file']] = empty($filed_data['value']) ? '' : strtotime($filed_data['value']);
                }
                break;
            case 'checkbox':
                $_data[$filed_data['file']] = !isset($filed_data['value']) ? '' : implode(',',$filed_data['value']);
                break;
            case 'upload':
                if(!empty($filed_data['type_edit']) && $filed_data['type_edit'] == 'array'){
                    $new = Common::compare_file(empty($filed_data['value']) ? [] : $filed_data['value'],empty($filed_data['_value']) ? [] : Upload::editadd(is_array($filed_data['_value']) ? $filed_data['_value'] : json_decode($filed_data['_value'],true),false),'uri');
                    $_new = array_column($new['new'],'uri');
                    if(!empty($_new)){
                        foreach ($filed_data['value'] as $k => $v){
                            if(!in_array($v['uri'],$_new)){
                                continue;
                            }else if(empty($v['uri'])){
                                unset($filed_data['value'][$k]);
                                continue;
                            }
                            $v['uri'] = Upload::fileMove($v['uri'],empty($filed_data['_dir']) ? '' : $filed_data['_dir']);
                            $filed_data['value'][$k] = $v;
                        }
                    }
                    $_data[$filed_data['file']] = empty($filed_data['value']) ? '' : json_encode(Common::arraySort(Upload::editadd($filed_data['value']),'sort','desc'),JSON_UNESCAPED_UNICODE);
                    $_new = empty($new['old']) ? [] : array_column($new['old'],'uri');
                    if(!empty($_new)){
                        Upload::fileDel($_new);
                    }
                }else{
                    if(Upload::editadd($filed_data['_value'],false) != $filed_data['value']){
                        $_data[$filed_data['file']] = Upload::fileMove($filed_data['value'],empty($filed_data['_dir']) ? '' : $filed_data['_dir']);
                        $filed_data['_value'] = empty($filed_data['_value']) ? '' : Upload::fileDel($filed_data['_value']);
                    }
                }
                break;
        }
        return $_data;
    }
    public function getFiles($filed_data) {
        $value = !isset($filed_data['value']) ? '' : $filed_data['value'];
        $type_unit = empty($filed_data['type_unit']) ? '' : $filed_data['type_unit'];
        switch ($filed_data['type']){
            case 'map':
                $value = empty($value) ? [] : json_decode($value,true);
                break;
            case 'text':
                if(!empty($filed_data['type_edit']) && in_array($filed_data['type_edit'],['date','datetime',])){
                    if($filed_data['type_edit'] == 'date'){
                        $value =  empty($value) ? '-' : date('Y-m-d',(int) $value);
                    } else {
                        $value = empty($value) ? '-' : date('Y-m-d H:i:s',(int) $value);
                    }
                }
                break;
            case 'checkbox':
                if(isset($value)){
                    $value = !isset($value) ? [] : explode(',',$value);
                    $_value = [];
                    foreach ($filed_data['data']['list'] as $k => $v){
                        if(in_array($k,$value)){
                            $_value[] = $v.' '.$type_unit;
                        }
                    }
                    $value = implode(',',$_value);
                }
                break;
            case 'radio':
                if(isset($value)){
                    foreach ($filed_data['data']['list'] as $k => $v){
                        if($k == $value){
                            $value = $v.' '.$type_unit;
                            break;
                        }
                        continue;
                    }
                }
                break;
            case 'select':
                if(isset($value)){
                    foreach ($filed_data['data']['list'] as $k => $v){
                        if($k == $value){
                            $value = $v.' '.$type_unit;
                            break;
                        }
                        continue;
                    }
                }
                break;
            case 'upload':
                if(!empty($value)) {
                    if (!empty($filed_data['type_edit']) && $filed_data['type_edit'] == 'array') {
                        $value = is_array($value) ? $value : json_decode($value,true);
                    }
                }
                break;
            case 'addchina':
                if(!empty($value)) {
                    $cMdoel = new AddChinacode();
                    $value = $cMdoel->getPupValue($value);
                }
                break;
            case 'bindmodel':
                if(!empty($value)){
                    $_model = "app\\common\\model\\{$filed_data['form_geturi']}\\Article";
                    $_model = new $_model;
                    $_model = $_model->getList(['id' => $value]);
                    $value = '';
                    if($_model['total'] < '1'){
                        $value = '';
                    } else if($filed_data['form_geturitype'] == '1'){
                        $_model = $_model['data']['0'];
                        $_img = empty($_model['picurl']) ? '' : "<div class='cx-pad-a5' style='width: 100px;height: 100px;'><div class='cx-bg-img1x1 cx-bor cx-bor-white-1'><div class='cx-bg-img cx-fex-c cx-fex-itemsc'><img class='cx-img-responsive' src='{$_model['picurl']}' onerror='this.src=\'public/wormcms/img/imgnone.jpg\''></div></div></div>";
                        $_description = get_word($_model['description'],120);

                        $_uri = (string) Route::buildUrl("home/{$filed_data['form_geturi']}.article/",['id' => $_model['id']]);
                        $value = "<div class='layout cx-fex-l cx-bor cx-bor-white-3'>{$_img}<div class='cx-fex-l cx-fex-column cx-pad-a5' style='flex-flow: 2;'><h3 class='layout cx-text-f14'><a href='{$_uri}' target='_blank'>{$_model['title']}</a></h3><div class='layout cx-mag-t15 cx-text-black-3'>{$_description}</div></div></div>";
                    } else {
                        foreach ($_model['data'] as $k => $v){
                            $_img = empty($v['picurl']) ? '' : "<div class='cx-pad-a5' style='width: 100px;height: 100px;'><div class='cx-bg-img1x1 cx-bor cx-bor-white-1'><div class='cx-bg-img cx-fex-c cx-fex-itemsc'><img class='cx-img-responsive' src='{$v['picurl']}' onerror='this.src=\'public/wormcms/img/imgnone.jpg\''></div></div></div>";
                            $_description = get_word($v['description'],120);
                            $_uri = url("home/{$filed_data['form_geturi']}.article/",['id' => $v['id']])->build();
                            $value .= "<div class='layout cx-fex-l cx-bor cx-bor-white-3'>{$_img}<div class='cx-fex-l cx-fex-column cx-pad-a5' style='flex-flow: 2;'><h3 class='layout cx-text-f14'><a href='{$_uri}' target='_blank'>{$v['title']}</a></h3><div class='layout cx-mag-t15 cx-text-black-3'>{$_description}</div></div></div>";
                        }
                    }
                }
                break;
        }
        return $value;
    }
    protected function FileSize($size){
        $KB = 1024;
        $MB = 1024 * $KB;
        $GB = 1024 * $MB;
        $TB = 1024 * $GB;
        if ($size < $KB) {
            return $size . "B";
        } elseif ($size < $MB) {
            return round($size / $KB, 2) . "KB";
        } elseif ($size < $GB) {
            return round($size / $MB, 2) . "MB";
        } elseif ($size < $TB) {
            return round($size / $GB, 2) . "GB";
        } else {
            return round($size / $TB, 2) . "TB";
        }
    }
    protected function EditUri($uri,$data){
        preg_match_all('/__([\w]+)__/i',$uri,$url);
        $url = count($url) >= '2' ? $url['1'] : null;
        if(empty($url)){
            return $uri;
        }
        foreach ($url as $k => $v){
            $new_info = !isset($data[$v]) ? null : $data[$v];
            $uri = !isset($new_info) ? $uri : str_replace("__{$v}__",$new_info,$uri);
        }
        return $uri;
    }

}