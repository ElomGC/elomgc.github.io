<?php
declare (strict_types = 1);
namespace hook;

use app\common\model\AddChinacode;
use app\common\model\Chinacode;
use app\facade\wormview\Upload;
use think\facade\Request;
use worm\NodeFormat;
use worm\Table;

class Common {
    /**
     * @param $str   原始字符串
     * @param $add   要检测的字符串
     * @param int $length    开始位置
     * @return bool
     */
    public function strAddwith($str,$add,$length = 0){
        if(strpos($str,$add) === $length){
            return true;
        }
        return false;
    }
    /**
     * 数据集转数组
     * @param $data
     * @return array
     */
    public function JobArray($data){
        if (is_array($data)) {
            if (empty($data)){
                return array();
            }
            if (is_object(current($data))) {
                $new_data = array();
                foreach ($data as $k => $v) {
                    $new_data[$k] = $v->toArray();
                }
                return $new_data;
            }
            return $data;
        }
        if (is_object($data)) {
            return $data->toArray();
        }
        return $data;
    }
    /**
     * 格式化数组
     * @param $data 格式化数组
     * @return array
     */
    public function data_trim($data){
        $trim = array();
        foreach ($data as $key => $val){
            if(is_array($val)){
                $trim[$key] = $this->data_trim($val);
            }else{
                $val = empty($val) && !isset($val) ? '' : (string) $val;
                $val = trim($val);
                $val = str_replace("\t","   &nbsp;  &nbsp;",$val);
                $val = str_replace("   "," &nbsp; ",$val);
                $val = trim($val);
                $trim[$key] = empty($val) && !isset($val) ? null : $val;
            }
        }
        return $trim;
    }
    public function data_html($data){
        $trim = null;
        if(is_array($data)){
            foreach ($data as $key => $val){
                if(is_array($val)){
                    $trim[$key] = $this->data_trim($val);
                }else{
                    $val = trim($val);
                    $data = str_replace(" &amp;nbsp; ","&nbsp;",$data);
                    $val = str_replace("&nbsp;","",$val);
                    $data = str_replace("  ","",$data);
                    $val = str_replace("   ","",$val);
                    $trim[$key] = trim($val);
                }
            }
        }else{
            $data = trim($data);
            $data = str_replace(" &amp;nbsp; ","&nbsp;",$data);
            $data = str_replace("&nbsp;","",$data);
            $data = str_replace("   ","",$data);
            $trim = trim($data);
        }
        return $trim;
    }
    /**
     * 去除不需要的字段
     * @param $data 要处理的数组
     * @param $file 处理字段
     * @param $value 对比值
     * @param $eqfile 等于为true
     * @return mixed 返回处理完成的数组
     */
    public function del_file($data,$file,$value,$eqfile = false){
        foreach ($data as $k => $v){
            if(is_array($value)){
                if($eqfile){
                    if(in_array($v[$file],$value)){
                        unset($data[$k]);
                    }
                }else{
                    if(!in_array($v[$file],$value)){
                        unset($data[$k]);
                    }
                }
            }else{
                if($eqfile){
                    if($v[$file] == $value){
                        unset($data[$k]);
                    }
                }else{
                    if(!isset($v[$file]) || $v[$file] != $value){
                        unset($data[$k]);
                    }
                }
            }
            continue;
        }
        $data = array_merge([],$data);
        return $data;
    }
    /**
     * 删除空数据字段
     */
    public function del_null($data){
        $_data = [];
        foreach ($data as $k => $v){
            if(is_array($v)){
                $v = $this->del_null($v);
            }else{
                $v = (string) $v;
                if($v == "" || trim($v) == ""){
                    continue;
                }
            }
            $_data[$k] = $v;
        }
        return $_data;
    }
    /**
     * 清空/删除 文件夹
     * @param string $dirname 文件夹路径
     * @param bool $self 是否删除当前文件夹
     * @return bool
     */
    function del_dir($dirname, $self = true) {
        if (!file_exists($dirname)) {
            return false;
        }
        if (is_file($dirname) || is_link($dirname)) {
            return unlink($dirname);
        }
        $dir = dir($dirname);
        if ($dir) {
            while (false !== $entry = $dir->read()) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $this->del_dir($dirname . '/' . $entry);
            }
        }
        $dir->close();
        $self && rmdir($dirname);
    }
    /**
     * @desc arraySort php二维数组排序 按照指定的key 对数组进行排序
     * @param array $arr 将要排序的数组
     * @param string $keys 指定排序的key
     * @param string $type 排序类型 asc | desc
     * @return array
     */
    public function arraySort($arr, $keys, $type = 'desc') {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v){
            $keysvalue[$k] = empty($v[$keys]) ? '0' : $v[$keys];
        }
        $type == 'asc' ? asort($keysvalue) : arsort($keysvalue);
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            if(!is_array($arr[$k])){
                $arr[$k] = $arr[$k]->toArray();
            }
            $new_array[] = $arr[$k];
        }
        return $new_array;
    }
    /**
     * @param $old
     * @param $new
     * @param bool $isover
     * @return array
     */
    public function copy_dir($old,$new,$isover=true){
        if(!is_dir($new)){
            if(!mkdir($new,0777,true)){
                return ['code' => '0','msg' => $new.'目录创建失败'];
            }
        }
        if (file_exists($old)){
            if(is_file($old)){
                if($isover==true || !is_file($new)){
                    copy($old,$new);
                }
            } else{
                $handle = opendir($old);
                while (($file = readdir($handle))!=false) {
                    if ( ($file!=".") && ($file!="..") ){
                        if (is_dir("$old/$file")){
                            $this->copy_dir("$old/$file","$new/$file",$isover);
                        } else{
                            if($isover==true || !is_file("$new/$file")){
                                copy("$old/$file","$new/$file");
                            }
                        }
                    }
                }
                closedir($handle);
            }
        }
    }
    public function edit_file_class($path,$old,$new){
        $file_array = get_dir_file($path,'php');
        foreach ($file_array AS $file){
            write_file($file, str_replace("$old","$new", read_file($file)));
        }
    }
    public function copy_files($old,$new){
        $_nname = basename($new);
        $_dir = explode(DIRECTORY_SEPARATOR,$new);
        $_newdir = '';
        if(count($_dir) > '1'){
            foreach ($_dir as $k => $v){
                if(empty($v) || $v == $_nname){
                    unset($_dir[$k]);
                    continue;
                }
            }
            $_newdir = empty($_dir) ? $_newdir : implode(DIRECTORY_SEPARATOR,$_dir);
        }
        if (!file_exists($_newdir)) {
            @mkdir($_newdir,0755,true);
        }
        if(!@copy($old,$_newdir.'/'.$_nname)){
            return false;
        }
        return true;
    }
    public function wormmd5($data){
        $j=0;
        $start = 0;
        $result = array();
        if (!is_string($data)) {
            return false;
        }
        $strlen = strlen($data);
        if (!$strlen) {
            return false;
        }
        while ($start < $strlen) {
            $result[$j] = substr($data, $start, 2 << $j);
            $start += (2 << $j);
            ++$j;
        }
        if ($strlen > 32) {
            $data = '';
        }
        while ($j > 0) {
            $data .= $result[--$j];
        }
        return md5($data);
    }
    /**
     * @param $data 要处理的数据
     * @param $olddata  要对比的数据
     * @param $file 要处理的字段
     */
    public function compare_file($data,$olddata = [],$file){
        $newfiledata = $oldfiledata = $newdata = $newolddata = [];
        $newfiledata = (empty($data) && !isset($data)) ? [] : array_column($data,$file);
        $oldfiledata = empty($olddata) ? [] : array_column($olddata,$file);
        //  处理新图片
        if(!empty($oldfiledata) && !empty($data)){
            foreach ($data as $k => $v){
                if(!empty($v[$file])){
                    if(in_array($v[$file],$oldfiledata)){
                        unset($data[$k]);
                        continue;
                    }
                }
                $newdata[] = $v;
            }
        }else{
            $newdata = $data;
        }
        //  处理老图片
        if(!empty($newfiledata) && !empty($olddata)){
            foreach ($olddata as $k => $v){
                if(!empty($v[$file])){
                    if(in_array($v[$file],$newfiledata)){
                        unset($olddata[$k]);
                    }
                }
            }
        }
        $res = [
            'new' => $newdata,
            'old' => $olddata,
        ];
        return $res;
    }
    //  处理文件
    public function setFile($new,$old){
        $old = empty($old) ? null : $old;
        if($new != $old){
            if(!empty($new)){
                $new = Upload::fileMove($new);
            }
            if(!empty($old)){
                Upload::fileDel($old);
            }
        }
        return $new;
    }
    //  格式化参数到表单
    public function ReadList($data){
        $new_data = [
            'type' => $data['form_type'],
            'file' => $data['sql_file'],
            'title' => $data['form_title'],
        ];
        if($data['form_type'] == ['radio','checkbox','select']){
            $arr = $this->data_trim(explode("\r\n",str_replace(array("\r\n", "\r", "\n"), "\r\n", $data['form_data'])));
            $_arr = [];
            foreach ($arr as  $k => $v){
                $v = explode('|',$v);
                $v['1'] = empty($v['1']) ? $v['0'] : $v['1'];
                $_arr[$v['0']] = $v['1'];
            }
            $new_data['radio'] = ['list' => $_arr,'default' => $data['form_default']];
            $new_data['type'] = 'radio';
        }else if(in_array($data['form_type'],['upload_img','upload_file','upload_imgtc','upload_imgarr','upload_imgarrtc','upload_filearr'])){
            if($data['form_type'] == 'upload_img'){
                $new_data['type'] = 'imgsee';
                $new_data['width'] = '80';
            }else{
                $new_data['type'] = 'img';
                $new_data['type_edit'] = 'array_see';
                $new_data['width'] = '80';
            }
        }
        return $new_data;
    }
    //  格式化表单
    public function ReadFile($data){
        if(!empty($data['del_time']) && $data['del_time'] > '0'){
            return;
        }
        $new_data = [
            'file' => $data['sql_file'],
            'title' => $data['form_title'],
            'title_edit' => $data['form_text'],
            'type' => $data['form_type'],
            'type_group' => empty($data['form_group']) ? '' : $data['form_group'],
            'required' => $data['form_required'] == '1' ? true : false,
            'required_list' => empty($data['form_required_list']) ? '' : $data['form_required_list'],
            'type_unit' => $data['form_unit'],
            'disabled' => !empty($data['form_edit']) && $data['form_edit'] == '1' ? true : false,
            'default' => !isset($data['form_default']) ? '' : $data['form_default'],
            'tip' => $data['form_tip'],
            'form_geturitype' => empty($data['form_geturitype']) ? '' : $data['form_geturitype'],
            'form_geturi' => empty($data['form_geturi']) ? '' : $data['form_geturi'],
            'form_js' => empty($data['form_js']) ? '' : $data['form_js'],
            'admin_list_show' => empty($data['admin_list_show']) ? '0' : $data['admin_list_show'],
            'list_show' => empty($data['list_show']) ? '0' : $data['list_show'],
            'cont_show' => empty($data['cont_show']) ? '0' : $data['cont_show'],
        ];
        if(in_array($data['form_type'],['radio','checkbox','select'])){
            $arr = $this->data_trim(explode("\r\n",str_replace(array("\r\n", "\r", "\n"), "\r\n", $data['form_data'])));
            $_arr = [];
            foreach ($arr as  $k => $v){
                $v = explode('|',$v);
                $v['1'] = empty($v['1']) ? $v['0'] : $v['1'];
                $_arr[$v['0']] = $v['1'];
            }
            $new_data['data'] = ['list' => $_arr,'default' => $data['form_default']];
        }else if(in_array($data['form_type'],['upload_img','upload_video','upload_videoarr','upload_file','upload_imgtc','upload_imgarr','upload_imgarrtc','upload_filearr'])){
            if(in_array($data['form_type'],['upload_file','upload_filearr'])){
                $new_data['upload_accept'] = 'file';
            }else if(in_array($data['form_type'],['upload_video','upload_videoarr'])){
                $new_data['upload_accept'] = 'video';
            }
            if(in_array($data['form_type'],['upload_imgtc','upload_imgarr','upload_videoarr','upload_imgarrtc','upload_filearr'])){
                $new_data['upload_filenum'] = '9';
            }else{
                $new_data['upload_filenum'] = '1';
            }
            if(in_array($data['form_type'],['upload_file','upload_imgtc','upload_videoarr','upload_imgarr','upload_imgarrtc','upload_filearr'])){
                $new_data['type_edit'] = 'array';
            }
            $new_data['type'] = 'upload';
        }else if(in_array($data['form_type'],['number','money','time','date','datetime','hidden'])){
            $new_data['type'] = 'text';
            if($data['form_type'] == 'number'){
                $new_data['type_edit'] = 'number';
            }else if($data['form_type'] == 'money'){
                $new_data['type_unit'] = !empty($new_data['type_unit']) ? $new_data['type_unit'] : '元';
            }else if($data['form_type'] == 'time'){
                $new_data['type_edit'] = 'time';
            }else if($data['form_type'] == 'datetime'){
                $new_data['type_edit'] = 'datetime';
            }else if($data['form_type'] == 'date'){
                $new_data['type_edit'] = 'date';
            }else{
                $new_data['type_edit'] = 'hidden';
            }
        } else if ($data['form_type'] == 'addchina'){
            $cModel = new AddChinacode();
            $new_data['data']['0']['list'] = $cModel->getList(['pid' => '0']);
        } else if ($data['form_type'] == 'chinacode'){
            $cModel = new Chinacode();
            $new_data['data']['0']['list'] = $cModel->getList(['parzoneid' => '0']);
        } else if ($data['form_type'] == 'bindmodel'){
            $new_data['default'] = Request::param($new_data['file']);
            $new_data['data'] = [];
        }
        return $new_data;
    }
    public function ReadWebFiles($data){
        if(empty($data['_warrant'])) return $data;
        $_web_url = EmpowerUrl($data['web_url']);
        if($_web_url != $data['_warrant']['0']){
            $data['_warrant'] = '';
        }
        return $data;
    }
    //  字段写入处理
    public function SetReadFile($formList,$data=[],$old = [],$updir = ''){
        $_data = [];
        $tablemodel = new Table();
        foreach ($formList as $k => $v){
            if(isset($v['required']) && $v['required'] && (empty($data[$v['file']]) && !isset($data[$v['file']]))){
                return ['code' => '0','msg' => "{$v['title']}不得为空"];
            }
            $v['_dir'] = $updir;
            $v['_value'] = isset($old[$v['file']]) ? $old[$v['file']] : '';
            $v['value'] = isset($data[$v['file']]) ? $data[$v['file']] : '';
            if($v['_value'] == $v['value']){
                continue;
            }
            $v = $tablemodel->setFormFiles($v);
            $_data = array_merge($_data,$v);
        }
        $_data = ['code' => '1','data' => $_data];
        return $_data;
    }
    //  字段读取处理
    public function getReadFile($formList,$data = []){
        $_data = [];
        $tablemodel = new Table();
        foreach ($formList as $k => $v){
            $v['value'] = isset($data[$v['file']]) ? $data[$v['file']] : '';
            if(in_array($v['type'],['bindmodel','addchina'])){
                $_data["_{$v['file']}"] = $v['value'];
            }
            $_uns = true;
            if(in_array(Request::action(),['read']) && $v['cont_show'] != '1' || in_array(Request::action(),['addnew'])){
                $_uns = false;
            }
            if($_uns){
                $_data[$v['file']] = !isset($v['value']) ? '' : $tablemodel->getFiles($v);
            }else{
                $_data[$v['file']] = !isset($v['value']) ? '' : $v['value'];
            }
        }
        $_data = Upload::editadd($_data,false);
        return $_data;
    }
    /**
     *  判断字符串是否是身份证号
     */
    public function isIdCard($idcard){
        #  转化为大写，如出现x
        $idcard = strtoupper($idcard);
        #  加权因子
        $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        #  按顺序循环处理前17位
        $sigma = 0;
        #  提取前17位的其中一位，并将变量类型转为实数
        for ($i = 0; $i < 17; $i++) {
            $b = (int)$idcard{$i};
            #  提取相应的加权因子
            $w = $wi[$i];
            #  把从身份证号码中提取的一位数字和加权因子相乘，并累加
            $sigma += $b * $w;
        }
        #  计算序号
        $sidcard = $sigma % 11;
        #  按照序号从校验码串中提取相应的字符。
        $check_idcard = $ai[$sidcard];
        if ($idcard{17} == $check_idcard) {
            return true;
        }
        return false;
    }
    /**
     *  根据身份证号码获取性别
     *  @return int $sex 性别 1男 2女 0未知
     */
    public function get_sex($idcard) {
        $sexint = (int) substr($idcard, 16, 1);
        return $sexint % 2 === 0 ? '0' : '1';
    }
    /**
     *  根据身份证号码获取生日
     *  @return $birthday
     */
    public function get_ubdy($idcard) {
        $bir = substr($idcard, 6, 8);
        $year = (int) substr($bir, 0, 4);
        $month = (int) substr($bir, 4, 2);
        $day = (int) substr($bir, 6, 2);
        return $year . "-" . $month . "-" . $day;
    }
    /**
     *  根据身份证号码计算年龄
     */
    public function get_age($idcard){
        #  获得出生年月日的时间戳
        $date = strtotime(substr($idcard,6,8));
        #  获得今日的时间戳
        $today = strtotime('today');
        #  得到两个日期相差的大体年数
        $diff = floor(($today-$date)/86400/365);
        #  strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
        $age = strtotime(substr($idcard,6,8).' +'.$diff.'years')>$today?($diff+1):$diff;
        return $age;
    }
    /**
     *  根据身份证号，返回对应的生肖
     */
    public function get_zodiac($idcard){ //
        if(empty($idcard)) return null;
        $start = 1901;
        $end = (int)substr($idcard, 6, 4);
        $x = ($start - $end) % 12;
        if ($x == 1 || $x == -11) return '1';
        if ($x == 0) return '2';
        if ($x == 11 || $x == -1) return '3';
        if ($x == 10 || $x == -2) return '4';
        if ($x == 9 || $x == -3)  return '5';
        if ($x == 8 || $x == -4)  return '6';
        if ($x == 7 || $x == -5)  return '7';
        if ($x == 6 || $x == -6)  return '8';
        if ($x == 5 || $x == -7)  return '9';
        if ($x == 4 || $x == -8)  return '10';
        if ($x == 3 || $x == -9)  return '11';
        if ($x == 2 || $x == -10) return '12';
    }
    /**
     *  根据身份证号，返回对应的星座
     */
    public function get_starsign($idcard){
        $b = substr($idcard, 10, 4);
        $m = (int)substr($b, 0, 2);
        $d = (int)substr($b, 2);
        if(($m == 1 && $d <= 21) || ($m == 2 && $d <= 19)){
            return '1';
        }else if (($m == 2 && $d > 20) || ($m == 3 && $d <= 20)){
            return '2';
        }else if (($m == 3 && $d > 20) || ($m == 4 && $d <= 20)){
            return '3';
        }else if (($m == 4 && $d > 20) || ($m == 5 && $d <= 21)){
            return '4';
        }else if (($m == 5 && $d > 21) || ($m == 6 && $d <= 21)){
            return '5';
        }else if (($m == 6 && $d > 21) || ($m == 7 && $d <= 22)){
            return '6';
        }else if (($m == 7 && $d > 22) || ($m == 8 && $d <= 23)){
            return '7';
        }else if (($m == 8 && $d > 23) || ($m == 9 && $d <= 23)){
            return '8';
        }else if (($m == 9 && $d > 23) || ($m == 10 && $d <= 23)){
            return '9';
        }else if (($m == 10 && $d > 23) || ($m == 11 && $d <= 22)){
            return '10';
        }else if (($m == 11 && $d > 22) || ($m == 12 && $d <= 21)){
            return '11';
        }else if (($m == 12 && $d > 21) || ($m == 1 && $d <= 20)){
            return '12';
        }
    }
    /**
     *  根据身份证号码获取出身地址
     *  author:xiaochuan
     *  @param string $idcard    身份证号码
     *  @return string $address
     */
    public function get_address($idcard){
        $cxModel = new \app\common\model\Chinacode();
        $address = $cxModel->select()->toArray();
        # 截取前六位数(获取基体到县区的地址)
        $key = substr($idcard,0,6);
        $_address = $this->del_file($address,'zoneid',$key);
        if(!empty($_address)) return $_address['0'];
        # 都没有
        return '未知地址';
    }
    //  unicode解码
    public function unicode_decode($name){
        $json = '{"str":"'.$name.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return '';
        return $arr['str'];
    }
    public function Uploadfile($data){
        if(empty($data['web_warrant'])) return $data;
        $_code = setposturi(config('app.app_up'));
        $_code = Pwd($data['web_warrant'],true,EmpowerUrl($_code));
        $data['_warrant'] = $_code ? explode('@@@',$_code) : '';
        return $data;
    }
    public function setHome($data){
        $_Y = date('Y');
        $_code = setposturi(config('app.app_up'));
        $data['web_footbq'] = $data['web_footbq']." Powered by <a href='//{$_code}' title='WORMCMS' target='_blank'>WORMCMS</a> ©2012-{$_Y}";
        return $data;
    }
    //  微信全局返回码
    public function wx_res($code){
        $res = [
            '-1' =>	'系统繁忙，此时请开发者稍候再试',
            '0' =>	'请求成功',
            '40001' => 	'获取 access_token 时 AppSecret 错误，或者 access_token 无效。请开发者认真比对 AppSecret 的正确性，或查看是否正在为恰当的公众号调用接口',
            '40002' => 	'不合法的凭证类型',
            '40003' => 	'不合法的 OpenID ，请开发者确认 OpenID （该用户）是否已关注公众号，或是否是其他公众号的 OpenID',
            '40004' => 	'不合法的媒体文件类型',
            '40005' => 	'不合法的文件类型',
            '40006' => 	'不合法的文件大小',
            '40007' => 	'不合法的媒体文件 id',
            '40008' => 	'不合法的消息类型',
            '40009' => 	'不合法的图片文件大小',
            '40010' => 	'不合法的语音文件大小',
            '40011' => 	'不合法的视频文件大小',
            '40012' => 	'不合法的缩略图文件大小',
            '40013' => 	'不合法的 AppID ，请开发者检查 AppID 的正确性，避免异常字符，注意大小写',
            '40014' => 	'不合法的 access_token ，请开发者认真比对 access_token 的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口',
            '40015' => 	'不合法的菜单类型',
            '40016' => 	'不合法的按钮个数',
            '40017' => 	'不合法的按钮类型',
            '40018' => 	'不合法的按钮名字长度',
            '40019' => 	'不合法的按钮 KEY 长度',
            '40020' => 	'不合法的按钮 URL 长度',
            '40021' => 	'不合法的菜单版本号',
            '40022' => 	'不合法的子菜单级数',
            '40023' => 	'不合法的子菜单按钮个数',
            '40024' => 	'不合法的子菜单按钮类型',
            '40025' => 	'不合法的子菜单按钮名字长度',
            '40026' => 	'不合法的子菜单按钮 KEY 长度',
            '40027' => 	'不合法的子菜单按钮 URL 长度',
            '40028' => 	'不合法的自定义菜单使用用户',
            '40029' => 	'无效的 oauth_code',
            '40030' => 	'不合法的 refresh_token',
            '40031' => 	'不合法的 openid 列表',
            '40032' => 	'不合法的 openid 列表长度',
            '40033' => 	'不合法的请求字符，不能包含 \uxxxx 格式的字符',
            '40035' => 	'不合法的参数',
            '40038' => 	'不合法的请求格式',
            '40039' => 	'不合法的 URL 长度',
            '40048' => 	'无效的url',
            '40050' => 	'不合法的分组 id',
            '40051' => 	'分组名字不合法',
            '40060' => 	'删除单篇图文时，指定的 article_idx 不合法',
            '40117' => 	'分组名字不合法',
            '40118' => 	'media_id 大小不合法',
            '40119' => 	'button 类型错误',
            '40120' => 	'子 button 类型错误',
            '40121' => 	'不合法的 media_id 类型',
            '40125' => 	'无效的appsecret',
            '40132' => 	'微信号不合法',
            '40137' => 	'不支持的图片格式',
            '40155' => 	'请勿添加其他公众号的主页链接',
            '40163' => 	'oauth_code已使用',
            '41001' => 	'缺少 access_token 参数',
            '41002' => 	'缺少 appid 参数',
            '41003' => 	'缺少 refresh_token 参数',
            '41004' => 	'缺少 secret 参数',
            '41005' => 	'缺少多媒体文件数据',
            '41006' => 	'缺少 media_id 参数',
            '41007' => 	'缺少子菜单数据',
            '41008' => 	'缺少 oauth code',
            '41009' => 	'缺少 openid',
            '42001' => 	'access_token 超时，请检查 access_token 的有效期，请参考基础支持 - 获取 access_token 中，对 access_token 的详细机制说明',
            '42002' => 	'refresh_token 超时',
            '42003' => 	'oauth_code 超时',
            '42007' => 	'用户修改微信密码， accesstoken 和 refreshtoken 失效，需要重新授权',
            '43001' => 	'需要 GET 请求',
            '43002' => 	'需要 POST 请求',
            '43003' => 	'需要 HTTPS 请求',
            '43004' => 	'需要接收者关注',
            '43005' => 	'需要好友关系',
            '43019' => 	'需要将接收者从黑名单中移除',
            '44001' => 	'多媒体文件为空',
            '44002' => 	'POST 的数据包为空',
            '44003' => 	'图文消息内容为空',
            '44004' => 	'文本消息内容为空',
            '45001' => 	'多媒体文件大小超过限制',
            '45002' => 	'消息内容超过限制',
            '45003' => 	'标题字段超过限制',
            '45004' => 	'描述字段超过限制',
            '45005' => 	'链接字段超过限制',
            '45006' => 	'图片链接字段超过限制',
            '45007' => 	'语音播放时间超过限制',
            '45008' => 	'图文消息超过限制',
            '45009' => 	'接口调用超过限制',
            '45010' => 	'创建菜单个数超过限制',
            '45011' => 	'API 调用太频繁，请稍候再试',
            '45015' => 	'回复时间超过限制',
            '45016' => 	'系统分组，不允许修改',
            '45017' => 	'分组名字过长',
            '45018' => 	'分组数量超过上限',
            '45047' => 	'客服接口下行条数超过上限',
            '45064' => 	'创建菜单包含未关联的小程序',
            '45065' => 	'相同 clientmsgid 已存在群发记录，返回数据中带有已存在的群发任务的 msgid',
            '45066' => 	'相同 clientmsgid 重试速度过快，请间隔1分钟重试',
            '45067' => 	'clientmsgid 长度超过限制',
            '46001' => 	'不存在媒体数据',
            '46002' => 	'不存在的菜单版本',
            '46003' => 	'不存在的菜单数据',
            '46004' => 	'不存在的用户',
            '47001' => 	'解析 JSON/XML 内容错误',
            '48001' => 	'api 功能未授权，请确认公众号已获得该接口，可以在公众平台官网 - 开发者中心页中查看接口权限',
            '48002' => 	'粉丝拒收消息（粉丝在公众号选项中，关闭了 “ 接收消息 ” ）',
            '48004' => 	'api 接口被封禁，请登录 mp.weixin.qq.com 查看详情',
            '48005' => 	'api 禁止删除被自动回复和自定义菜单引用的素材',
            '48006' => 	'api 禁止清零调用次数，因为清零次数达到上限',
            '48008' => 	'没有该类型消息的发送权限',
            '50001' => 	'用户未授权该 api',
            '50002' => 	'用户受限，可能是违规后接口被封禁',
            '50005' => 	'用户未关注公众号',
            '61451' => 	'参数错误 (invalid parameter)',
            '61452' => 	'无效客服账号 (invalid kf_account)',
            '61453' => 	'客服帐号已存在 (kf_account exsited)',
            '61454' => 	'客服帐号名长度超过限制 ( 仅允许 10 个英文字符，不包括 @ 及 @ 后的公众号的微信号 )(invalid   kf_acount length)',
            '61455' => 	'客服帐号名包含非法字符 ( 仅允许英文 + 数字 )(illegal character in     kf_account)',
            '61456' => 	'客服帐号个数超过限制 (10 个客服账号 )(kf_account count exceeded)',
            '61457' => 	'无效头像文件类型 (invalid   file type)',
            '61450' => 	'系统错误 (system error)',
            '61500' => 	'日期格式错误',
            '63001' => 	'部分参数为空',
            '63002' => 	'无效的签名',
            '65301' => 	'不存在此 menuid 对应的个性化菜单',
            '65302' => 	'没有相应的用户',
            '65303' => 	'没有默认菜单，不能创建个性化菜单',
            '65304' => 	'MatchRule 信息为空',
            '65305' => 	'个性化菜单数量受限',
            '65306' => 	'不支持个性化菜单的帐号',
            '65307' => 	'个性化菜单信息为空',
            '65308' => 	'包含没有响应类型的 button',
            '65309' => 	'个性化菜单开关处于关闭状态',
            '65310' => 	'填写了省份或城市信息，国家信息不能为空',
            '65311' => 	'填写了城市信息，省份信息不能为空',
            '65312' => 	'不合法的国家信息',
            '65313' => 	'不合法的省份信息',
            '65314' => 	'不合法的城市信息',
            '65316' => 	'该公众号的菜单设置了过多的域名外跳（最多跳转到 3 个域名的链接）',
            '65317' => 	'不合法的 URL',
            '87009' => 	'无效的签名',
            '9001001' => 'POST 数据参数不合法',
            '9001002' => '远端服务不可用',
            '9001003' => 'Ticket 不合法',
            '9001004' => '获取摇周边用户信息失败',
            '9001005' => '获取商户信息失败',
            '9001006' => '获取 OpenID 失败',
            '9001007' => '上传文件缺失',
            '9001008' => '上传素材的文件类型不合法',
            '9001009' => '上传素材的文件尺寸不合法',
            '9001010' => '上传失败',
            '9001020' => '帐号不合法',
            '9001021' => '已有设备激活率低于 50% ，不能新增设备',
            '9001022' => '设备申请数不合法，必须为大于 0 的数字',
            '9001023' => '已存在审核中的设备 ID 申请',
            '9001024' => '一次查询设备 ID 数量不能超过 50',
            '9001025' => '设备 ID 不合法',
            '9001026' => '页面 ID 不合法',
            '9001027' => '页面参数不合法',
            '9001028' => '一次删除页面 ID 数量不能超过 10',
            '9001029' => '页面已应用在设备中，请先解除应用关系再删除',
            '9001030' => '一次查询页面 ID 数量不能超过 50',
            '9001031' => '时间区间不合法',
            '9001032' => '保存设备与页面的绑定关系参数错误',
            '9001033' => '门店 ID 不合法',
            '9001034' => '设备备注信息过长',
            '9001035' => '设备申请参数不合法',
            '9001036' => '查询起始值 begin 不合法',

        ];
        return empty($res[$code]) ? '未知错误' : $res[$code];
    }
    //  远程post请求
    public function request_post($url = '', $post_data = array()){
        $postdata = http_build_query($post_data);
        $options = [
            'https' => [
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60
            ]
        ];
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        return $result;
    }
    //  计算商品价格
    public function countMoney($data,$file,$num){
        $_key = array_keys($data);
        foreach ($_key as $k => $v){
            if(stripos($v,'groupid_') !== false || stripos($v,'chinacode_')){
                unset($_key[$k]);
            }
        }
        $data['money'] = empty($data["{$file}_{$num}"]) ? $data['money'] : $data["{$file}_{$num}"];
        $data['money_zk'] = empty($data["{$file}_{$num}money_zk"]) ? $data['money_zk'] : $data["{$file}_{$num}money_zk"];
        $_data = [];
        foreach ($_key as $k => $v){
            $_data[$v] = $data[$v];
        }
        return $_data;
    }
}