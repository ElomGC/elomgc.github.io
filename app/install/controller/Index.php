<?php
declare(strict_types = 1);
namespace app\install\controller;

use app\common\controller\Base;
use app\facade\hook\Common;
use think\db\exception\PDOException;
use think\exception\ValidateException;
use think\facade\Config;
use think\facade\Db;
use think\facade\View;

class Index extends Base
{
    protected function initialize()
    {
        parent::initialize();
        $this->tempview();
    }

    public function index(){
        session('error',false);
        return view();
    }
    public function one(){
        $error = session('error');
        if($error){
            $this->error('访问错误！',url('index')->build());
        }
        session('error',false);
        $xitong = $this->xitong();
        $hanshu = $this->hanshu();
        $check_dirs = $this->check_dirs();
        View::assign([
            'xitong' => $xitong,
            'hanshu' => $hanshu,
            'check_dirs' => $check_dirs,
        ]);
        return view();
    }
    public function two(){
        if($this->request->isPost()){
            $data = Common::data_trim(input('post.'));
            $database = $data['db'];
            try {
                validate('app\\install\\validate\\Install')->scene("database")->check($database);
            }catch (ValidateException $e){
                $this->error($e->getError());
            }
            $webdata = $data['web'];
            try {
                validate('app\\install\\validate\\Install')->scene("webdata")->check($webdata);
            }catch (ValidateException $e){
                $this->error($e->getError());
            }
            $this->checkdatabase($database);
            session('database',$database);
            session('webdata',$webdata);
            $this->success('数据库验证通过',url('three')->build());
        }
        $error = session('error');
        if($error){
            $this->error('环境验证未通过！',url('index')->build());
        }
        session('error',false);
        $imgmd5 = substr(md5(time().request()->domain()),0,8);
        $saveName = '';
        for ( $i = 0; $i < 10; $i++ ) {
            $saveName .= substr($imgmd5, mt_rand(0, strlen($imgmd5) - 1), 1);
        }
        View::assign([
            'shibiefu' => $saveName,
        ]);
        return view();
    }
    public function three(){
        $error = session('error');
        if($error){
            $this->error('访问错误！',url('index')->build());
        }
        session('error',false);
        return view();
//        echo View::fetch();
        // 读取缓存
        $database = session('database');
        $webdata = session('webdata');
        $this->showmsg('开始读取数据库文件...','cx-text-green');
        $sqlfile = read_file(app_path().'base.sql');
        $sqlfile = $this->parse_sql($sqlfile, ['cx_' => $database['prefix']] ,0);
        $this->showmsg('读取数据库文件成功,开始写入数据...','cx-text-green');
        $sqlfile = array_filter($sqlfile);
        $this->showmsg('开始安装数据库...','cx-text-green');
        foreach ($sqlfile as $v) {
            $vs = $v;
            $tabelname = preg_replace("/^CREATE TABLE `(\w+)` .*/s","\\1",$vs);
            if(!empty($tabelname)){
                $this->showmsg("{$tabelname}...","cx-text-green");
            }
            try {
                Db::execute($v);
            } catch(\Exception $e) {
                $this->showmsg("数据库安装失败，请重试","cx-text-red");
                return;
            }
        }
        $this->showmsg('数据库安装完成，正在写入网站信息...','cx-text-green');
        Db::name('config')->whereConf('web_title')->update(['conf_value' => $webdata['web_title']]);
        Db::name('config')->whereConf('web_url')->update(['conf_value' => $webdata['web_url']]);
        // 打包用户数据
        $_user = [
            'u_name' => $webdata['u_name'],
            'u_password' => Pwd($webdata['u_password']),
            'u_groupid' => '1',
            'u_uniname' => $webdata['u_name'],
            'u_regtime' => time(),
            'u_regip' => $this->request->ip(),
            'status' => '1',
        ];
        $this->showmsg('数据库安装完成，正在写入管理员帐号...','cx-text-green');
        Db::name('user')->insert($_user);
        $this->showmsg('网站安装完成...','cx-text-green');
        $_url = url('four')->build();
        echo "<script type='text/javascript'>showurl('{$_url}');</script>";
        flush();
        ob_flush();
    }
    public function four(){
        $error = session('error');
        if($error){
            $this->error('访问错误！',url('index')->build());
        }
        session('error',false);
        $_code = time();
        write_file(base_path().'app.lock', (string) $_code);
        return view();
    }
    /***/
    public function setdatabase(){
        $data = Common::data_trim(input('post.'));
        if(empty($data)){
            $this->result(['base' => 'readsql'],'1','开始读取数据库文件...');
        }
        switch ($data['base']){
            case 'readsql':
                $database = session('database');
                $sqlfile = read_file(app_path().'base.sql');
                $sqlfile = $this->parse_sql($sqlfile, ['cx_' => $database['prefix']] ,0);
                session('readsql',$sqlfile);
                $this->result(['base' => 'setsql','row' => '0'],'1','读取数据库文件成功,开始写入数据...');
                break;
            case 'setsql':
                $sqlfile = session('readsql');
                foreach ($sqlfile as $k => $v){
                    try {
                        Db::execute($v);
                    } catch(\Exception $e) {
                        $this->result(['base' => 'setsql'],'0',"数据库安装失败，请重试！");
                    }
                }
                $this->result(['base' => 'end'],'1',"数据库安装成功,开始配置网站信息...");
                break;
            case 'end':
                $webdata = session('webdata');
                Db::name('config')->whereConf('web_title')->update(['conf_value' => $webdata['web_title']]);
                Db::name('config')->whereConf('web_title_min')->update(['conf_value' => $webdata['web_title']]);
                Db::name('config')->whereConf('web_keywords')->update(['conf_value' => $webdata['web_title']]);
                Db::name('config')->whereConf('web_description')->update(['conf_value' => $webdata['web_title']]);
                Db::name('config')->whereConf('web_url')->update(['conf_value' => $webdata['web_url']]);
                $this->result(['base' => 'userend'],'1',"配置网站信息成功,开始写入管理员信息...");
                break;
            case 'userend':
                $webdata = session('webdata');
                $_user = [
                    'u_name' => $webdata['u_name'],
                    'u_password' => Pwd($webdata['u_password']),
                    'u_groupid' => '1',
                    'u_uniname' => $webdata['u_name'],
                    'u_regtime' => time(),
                    'u_regip' => $this->request->ip(),
                    'status' => '1',
                ];
                Db::name('user')->insert($_user);
                Db::name('config_up')->whereId('1')->update(['addtime' => time()]);
                $this->result(['base' => 'good'],'1000',"网站安装完成");
                break;
        }
    }
    //  检测系统信息
    protected function xitong(){
        $xitong = [
            'os' =>['操作系统','无限制','linux',PHP_OS,'success'],
            'php' => ['PHP版本','7.1','7.1及以上',PHP_VERSION,'success'],
            'gd' => ['GD库','2.0','2.0','检测中...','success'],
            'upload' => ['附件上传','无限制','2MB','检测中...','success'],
            'dirs' => ['磁盘空间','100MB','>100MB','检测中...','success'],
        ];
        //  检测php版本号
        if ($xitong['php'][3] < $xitong['php'][1]) {
            $xitong['php'][4] = 'error';
            session('error', true);
        }
        //  检测附件上传
        if(@ini_get('file_uploads')){
            $xitong['upload'][3] = ini_get('upload_max_filesize');
        }
        //  检测磁盘空间
        if(function_exists('disk_free_space')){
            $_dirs = floor(disk_free_space(root_path())/(1024*2));
            $xitong['dirs'][3] = $_dirs > '100' ? '大于 100 MB' : $_dirs.' MB';
            if($_dirs < 100){
                session('error', true);
            }
        }
        //  检测GD库
        $temparr = function_exists('gd_info') ? gd_info() : array();
        if(empty($temparr['GD Version'])){
            $xitong['gd'][3] = '未安装GD库';
            $xitong['gd'][4] = 'error';
            session('error',true);
        }else{
            $xitong['gd'][3] = $temparr['GD Version'];
        }
        unset($temparr);
        return $xitong;
    }
    //  检测函数
    protected function hanshu(){
        $hanshu = [
            'pdo' => ['pdo()','支持','支持','success','class'],
            'pdo_mysql' => ['pdo_mysql()','支持','支持','success','mod'],
            'openssl' => ['openssl()','支持','支持','success','mod'],
            'gd' => ['gd()','支持','支持','success','mod'],
            'mbstring' => ['mbstring()','支持','支持','success','mod'],
            'zip' => ['zip()','支持','支持','success','mod'],
            'fileinfo' => ['fileinfo()','支持','支持','success','mod'],
            'curl' => ['curl()','支持','支持','success','mod'],
            'xml' => ['xml()','支持','支持','success','fons'],
            'mb_strlen' => ['mb_strlen()','支持','支持','success','fons'],
        ];

        foreach ($hanshu as $k => $v) {
            if(('class'==$v[4] && !class_exists($k)) || ('mod'==$v[4] && !extension_loaded($k)) || ('fons'==$v[4] && !function_exists($k)) ) {
                $v[2] = '不支持';
                $v[3] = 'no';
                session('error', true);
            }
            $hanshu[$k] = $v;
        }
        return $hanshu;
    }
    //  检测写入权限
    protected function check_dirs(){
        $check_dirs = [
            'runtime' => ['runtime','可写','可写','success','dir'],
            'upload_file' => ['upload_file','可写','可写','success','dir'],
            'conf' => ['config/app.php','可写','可写','success','file'],
            'dabase' => ['config/database.php','可写','可写','success','file'],
        ];
        foreach ($check_dirs as $k => $v){
            if($v['4'] == 'dir'){
                if(!is_writable(root_path().$v['0'])){
                    if(is_dir(root_path().$v['0'])){
                        $v['2'] = '不可写';
                        $v['3'] = 'error';
                        session('error',true);
                    }else{
                        $v['2'] = '不存在';
                        $v['3'] = 'error';
                        session('error',true);
                    }
                }
            }else{
                if(file_exists(root_path().$v['0'])){
                    if(!is_writable(root_path().$v['0'])){
                        $v['2'] = '不存在';
                        $v['3'] = 'error';
                        session('error',true);
                    }
                }else{
                    $v['2'] = '不存在';
                    $v['3'] = 'error';
                    session('error',true);
                }
            }
            $check_dirs[$k] = $v;
        }
        return $check_dirs;
    }
    //  检测数据库
    protected function checkdatabase($data){
        $config = include config_path().'database.php';
        foreach ($data as $k => $v) {
            if (array_key_exists($k, $config['connections']['mysql']) === false) {
                $this->error('参数'.$k.'不存在！');
            }
            $config['connections']['mysql'][$k] = $v;
        }
        Config::set($config, 'database');
        $db_instance = Db::connect('mysql');
        try{
            $db_instance->execute('select version()');
        }catch(\Exception $e){
            $this->error('数据库连接失败，请检查数据库配置！');
        }
        // 创建数据库
        if (!$db_instance->execute("CREATE DATABASE IF NOT EXISTS `{$data['database']}` DEFAULT CHARACTER SET utf8mb4")) {
            $this->error($db_instance->getError());
        }
        // 生成配置文件
        self::mkDatabase($data);
        return true;
    }
    //  生成缓存文件
    //  生成配置文件
    protected function mkDatabase($data){
        $code = <<<INFO
<?php
return [
    'default'         => env('database.driver', 'mysql'),
    'time_query_rule' => [],
    'auto_timestamp'  => true,
    'datetime_format' => 'Y-m-d H:i:s',
    'connections'     => [
        'mysql' => [
            'type'              => env('database.type', 'mysql'),
            'hostname'          => env('database.hostname', '{$data['hostname']}'),
            'database'          => env('database.database', '{$data['database']}'),
            'username'          => env('database.username', '{$data['username']}'),
            'password'          => env('database.password', '{$data['password']}'),
            'hostport'          => env('database.hostport', '{$data['hostport']}'),
            'params'            => [],
            'charset'           => env('database.charset', 'utf8mb4'),
            'prefix'            => env('database.prefix', '{$data['prefix']}'),
            'deploy'            => 0,
            'rw_separate'       => false,
            'master_num'        => 1,
            'slave_no'          => '',
            'fields_strict'     => true,
            'break_reconnect'   => false,
            'trigger_sql'       => env('app_debug', true),
            'fields_cache'      => false,
            'schema_cache_path' => app()->getRuntimePath() . 'schema' . DIRECTORY_SEPARATOR,
        ],
    ],
];
INFO;
        $formsrb = write_file(config_path().'database.php', $code);
        // 判断写入是否成功
        $config = include config_path().'database.php';
        if ($config['connections']['mysql']['password'] != $data['password'] && $config['connections']['mysql']['username'] != $data['username']) {
            $this->error('数据库配置写入失败！');
            exit;
        }
    }
    //  渲染文件
    protected function showmsg($msg,$cl){
        echo "<script type='text/javascript'> showmsg(\"{$msg}\",'{$cl}'); </script>";
        flush();
        ob_flush();
    }
    protected function parse_sql($sql = '', $prefix = [], $limit = 0) {
        // 被替换的前缀
        $from = '';
        // 要替换的前缀
        $to = '';
        // 替换表前缀
        if (!empty($prefix)) {
            $to   = current($prefix);
            $from = current(array_flip($prefix));
        }
        if ($sql != '') {
            // 纯sql内容
            $pure_sql = [];
            // 多行注释标记
            $comment = false;
            // 按行分割，兼容多个平台
            $sql = str_replace(["\r\n", "\r"], "\n", $sql);
            $sql = explode("\n", trim($sql));
            // 循环处理每一行
            foreach ($sql as $key => $line) {
                // 跳过空行
                if ($line == '') {
                    continue;
                }
                // 跳过以#或者--开头的单行注释
                if (preg_match("/^(#|--)/", $line)) {
                    continue;
                }
                // 跳过以/**/包裹起来的单行注释
                if (preg_match("/^\/\*(.*?)\*\//", $line)) {
                    continue;
                }
                // 多行注释开始
                if (substr($line, 0, 2) == '/*') {
                    $comment = true;
                    continue;
                }
                // 多行注释结束
                if (substr($line, -2) == '*/') {
                    $comment = false;
                    continue;
                }
                // 多行注释没有结束，继续跳过
                if ($comment) {
                    continue;
                }
                // 替换表前缀
                if ($from != '') {
                    $line = str_replace('`'.$from, '`'.$to, $line);
                }
                if ($line == 'BEGIN;' || $line =='COMMIT;') {
                    continue;
                }
                // sql语句
                array_push($pure_sql, $line);
            }
            // 只返回一条语句
            if ($limit == 1) {
                return implode($pure_sql, "");
            }
            // 以数组形式返回sql语句
            $pure_sql = implode($pure_sql, "\n");
            $pure_sql = explode(";\n", $pure_sql);
            return $pure_sql;
        } else {
            return $limit == 1 ? '' : [];
        }
    }
}