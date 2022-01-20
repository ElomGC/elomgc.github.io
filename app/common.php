<?php
declare (strict_types=1);
// 应用公共文件
function WormCms($tofunction = null){
    static $fun_array = [];
    list($class_name,$action) = explode('@',$tofunction);
    $class = "hook\\".ucfirst($class_name);
    $obj = empty($fun_array[$class_name]) ? null : $fun_array[$class_name];
    if(empty($obj)){
        if(!class_exists($class)){
            return;
        }
        $obj = $fun_array[$class_name] = new $class;
    }
    if(!method_exists($obj, $action)){
        return;
    }
    $params = func_get_args();
    unset($params[0]);
    $params = array_values($params);
    static $default_params_array = [];
    $_params = empty($default_params_array[$tofunction]) ? null : $default_params_array[$tofunction];
    if (!isset($_params)) {
        $_params = [];
        $_obj = new \ReflectionMethod($obj, $action);
        $_array = $_obj->getParameters();
        foreach($_array as $key=>$value){
            if($value->isOptional()){
                $_params[$key] = $value->getDefaultValue();
            }else{
                $_params[$key] = null;
            }
        }
        $default_params_array[$tofunction] = $_params;
    }
    foreach($_params as $key=>$value){
        if(isset($params[$key])){
            $_params[$key] = $params[$key];
        }
    }
    return call_user_func_array([$obj, $action], $_params);
}
//  驼峰写法转换
function toUnderScore($str,$sep = '_'){
    if(is_array($str)){
        foreach ($str as $k => $v){
            $str[$k] = toUnderScore($v);
        }
        return $str;
    }
     return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $sep . "$2", $str));
}
//  加密
function Pwd($string,$code = false,$key=''){
    $key_c = $string;
    $key = md5($key);
    $key_length = strlen($key);
    $string = $code === true ? base64_decode($string) : substr(md5(time().$string.$key),0,8).time().$string;
    $string_length=strlen($string);
    $rndkey=$box=array();
    $result='';
    for($i=0;$i<=255;$i++){
        $rndkey[$i]=ord($key[$i%$key_length]);
        $box[$i]=$i;
    }
    for($j=$i=0;$i<256;$i++){
        $j=($j+$box[$i]+$rndkey[$i])%256;
        $tmp=$box[$i];
        $box[$i]=$box[$j];
        $box[$j]=$tmp;
    }
    for($a=$j=$i=0;$i<$string_length;$i++){
        $a=($a+1)%256;
        $j=($j+$box[$a])%256;
        $tmp=$box[$a];
        $box[$a]=$box[$j];
        $box[$j]=$tmp;
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
    }
    if($code){
        if(substr($result,0,10) < time() && substr($result,0,8) == substr(md5(substr($result,8).$key),0,8)){
            return substr($result,18);
        }else{
            return false;
        }
    }else{
        return str_replace('=','',base64_encode($result));
    }
}
//  加密手机号
function PhonePwd($data){
    if(is_array($data)){
        foreach ($data as $k => $v){
            $data[$k] = PhonePwd($v);
        }
    }else{
        $data = substr($data, 0, 4).'****'.substr($data, 8);
    }
    return $data;
}
//  生成随机数
function get_range($endnum = '8'){
    $numbers = range (1,50);
    shuffle ($numbers);
    $num=8;
    $result = array_slice($numbers,0,$num);
    $result = implode('',$result);
    $newname = '';
    for ( $i = 0; $i < $endnum; $i++ ) {
        $newname .= substr($result, mt_rand(0, strlen($result) - 1), 1);
    }
    return $newname;
}
//  执行模型方法
function get_word(string $string,int $length,$more=1,$dot = '..'){
        $more || $dot='';
        if(strlen($string) <= $length) {
            return $string;
        }
        $pre = chr(1);
        $end = chr(1);
        $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);
        $strcut = '';
        if( 1 ) {
            $n = $tn = $noc = 0;
            while($n < strlen($string)) {

                $t = ord($string[$n]);
                if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $tn = 1; $n++; $noc++;
                } elseif(194 <= $t && $t <= 223) {
                    $tn = 2; $n += 2; $noc += 2;
                } elseif(224 <= $t && $t <= 239) {
                    $tn = 3; $n += 3; $noc += 2;
                } elseif(240 <= $t && $t <= 247) {
                    $tn = 4; $n += 4; $noc += 2;
                } elseif(248 <= $t && $t <= 251) {
                    $tn = 5; $n += 5; $noc += 2;
                } elseif($t == 252 || $t == 253) {
                    $tn = 6; $n += 6; $noc += 2;
                } else {
                    $n++;
                }
                if($noc >= $length) {
                    break;
                }
            }
            if($noc > $length) {
                $n -= $tn;
            }
            $strcut = substr($string, 0, $n);
        } else {
            $_length = $length - 1;
            for($i = 0; $i < $length; $i++) {
                if(ord($string[$i]) <= 127) {
                    $strcut .= $string[$i];
                } else if($i < $_length) {
                    $strcut .= $string[$i].$string[++$i];
                }
            }
        }
        $strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
        $pos = strrpos($strcut, chr(1));
        if($pos !== false) {
            $strcut = substr($strcut,0,$pos);
        }
        return $strcut.$dot;
}
/**
 * @param $_dir 要读取的文件目录
 * @param array $getfile
 * @return array|mixed
 */
function getDirlist($_dir,$getfile = []){
    if(file_exists($_dir)){
        $dir = opendir($_dir);
        while ($_file = readdir($dir)) {
            if($_file =='.' || $_file =='..'){
                continue;
            }
            $path = $_file;
            $getfile[] = $path;
        }
        closedir($dir);
    }
    return $getfile;
}
function get_dir_file($path='',$_suffix='',$brat = true){
    $suffix = explode(',', $_suffix);
    $dir = opendir($path);
    $array = [];
    while (false!=($file=readdir($dir))){
        if(is_file($path.$file)){
            $file = iconv('GB2312', 'UTF-8',$file);
            $detail = explode('.', $file);
            if(in_array(end($detail), $suffix)){
                $array[] = $path.$file;
            }
        }elseif($file!='.' && $file!='..' && $brat){
            $_array = get_dir_file($path.$file,$_suffix);
            if(is_array($_array)){
                $array = $array ? array_merge($array,$_array) : $_array ;
            }
        }
    }
    return $array;
}
function get_fiellist($_dir,$getfile = []){
    if(file_exists($_dir)){
        $dir = scandir($_dir);
        foreach ($dir as $k => $v){
            if($v =='.' || $v =='..'){
                continue;
            }
            if(is_dir($_dir.'/'.$v)){
                $_v = get_fiellist($_dir.'/'.$v);
                $getfile = array_merge($getfile,$_v);
            }else{
                $getfile[] = $_dir.'/'.$v;
            }
        }
    }
    return $getfile;
}
/***
 * 把内容写入文件
 * @param unknown $filename 文件名
 * @param unknown $data 内容
 * @param string $method 默认不追加写入,要追加写入,可以改为 'a'
 * @param number $iflock 锁定文件不能同时多个人同时写入
 * @return number
 */
function write_file($filename,$data,$method="rb+",$iflock=1){
    @touch($filename);
    $handle=@fopen($filename,$method);
    if(!$handle){
        return "此文件不可写:$filename";
    }
    if($iflock){
        @flock($handle,LOCK_EX);
    }
    @fputs($handle,$data);
    if($method=="rb+") @ftruncate($handle,strlen($data));
    @fclose($handle);
    @chmod($filename,0777);
    if(is_writable($filename) ){
        return true;
    }else{
        return false;
    }
}
/**
 * 读文件,相当于file_get_contents函数
 * @param unknown $filename
 * @param string $method
 * @return unknown
 */
function read_file($filename,$method="rb"){
    if($handle=@fopen($filename,$method)){
        @flock($handle,LOCK_SH);
        $filedata=@fread($handle,@filesize($filename));
        @fclose($handle);
    }
    return $filedata;
}
/**
 * @param $val 值
 * @param $filed    键
 * @return array    返回用户信息
 */
function getUser($val,$filed = 'uid'){
    $cxmodel = new \app\common\model\User();
    $user = $cxmodel->where($filed,$val)->find();
    if(empty($user)){
        return false;
    }
    $user = is_array($user) ? $user : $user->toArray();
    $user = $cxmodel->getUserFiled([$user]);
    $user = $user['0'];
    return $user;
}
function getUserList($data,$filed = 'uid',$user_filed = 'uid',$userdb = 'userdb'){
    if(is_array($filed)){
        $uids = [];
        foreach ($filed as $k => $v){
            $uids = array_merge($uids,array_unique(array_column($data,$v)));
        }
    }else{
        $uids = array_unique(array_column($data,$filed));
    }
    $userModel = new \app\common\model\User();
    $_userList = empty($uids) ? [] : $userModel->getList([$user_filed => $uids,'page' => '1','limit' => count($uids)]);
    $_userList = empty($_userList) ? [] : $_userList['data'];
    foreach ($data as $k => $v){
        if(is_array($userdb)){
            foreach ($userdb as $k1 => $v1){
                $_v = empty($_userList) ? [] : \app\facade\hook\Common::del_file($_userList,$user_filed,$v[$k1]);
                $_v = empty($_v) ? [] : $_v['0'];
                $v[$v1] = $_v;
            }
        }else{
            $_v = empty($_userList) ? [] : \app\facade\hook\Common::del_file($_userList,$user_filed,$v[$filed]);
            $_v = empty($_v) ? [] : $_v['0'];
            $v[$userdb] = $_v;
        }
        $data[$k] = $v;
    }
    $data = \app\facade\wormview\Upload::editadd($data,false);
    return $data;
}
function getWebdb(){
    if(cache('webdb')){
        $_confList = cache('webdb');
    } else {
        $confModel = new \app\common\model\Config();
        $confList = $confModel->whereStatus('1')->select()->toArray();
        foreach ($confList as $k => $v) {
            $_confList[$v['conf']] = $v['conf_value'];
        }
        $confModel = new \app\common\model\ConfigUp();
        $_confList = \app\facade\hook\Common::Uploadfile($_confList);
        $_confList = \app\facade\hook\Common::ReadWebFiles($_confList);
        $_ups = $confModel->getList(['status' => '1']);
        $_confList['web_edition_no'] = $_ups['data']['0']['edition_no'];
        cache('webdb',$_confList);
    }
    if(app('http')->getName() != 'admin' && empty($_confList['_warrant'])){
        $_confList = \app\facade\hook\Common::setHome($_confList);
    }
    return $_confList;
}
function setposturi($uri){
    return implode('.',explode('_',$uri));
}
function EmpowerUrl($url):string
{
    $url = str_replace(['http://','https://'],'',$url);
    $_empower = explode('.',$url);
    if(count($_empower) > '2' && $_empower['0'] == 'www'){
        unset($_empower['0']);
    }
    return implode('.',$_empower);
}
function imgqq($uri){
    $uri = explode('?',$uri);
    return $uri['0'];
}
/** 大小单位转换  **/
function CountSize($size){
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
function getUrl($fun,$type = 'home',$data = [],$domain = false){
    $type = in_array($type,['admin','member']) ? 'home' : $type;
    switch ($type){
        case 'home':
            $resUrl = url('/home/'.$fun,$data)->domain($domain)->build();
            break;
        default:
            $resUrl = url('/'.$type.'/'.$fun,$data)->domain($domain)->build();
            break;
    }
    return $resUrl;
}