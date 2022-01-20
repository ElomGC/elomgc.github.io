<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use PhpOffice\PhpWord\Shared\ZipArchive;
use phpspirit\databackup\BackupFactory;
use phpspirit\dbskeleton\Factory;
use phpspirit\dbskeleton\mysql\ColumnModel;
use phpspirit\dbskeleton\mysql\TableModel;
use think\facade\Db;

class Configup extends AdminBase
{
    use AddEditList;
    protected $list_temp = false;
    protected function initialize(){
        parent::initialize();
        $models = "app\\common\\model\\ConfigUp";
        $this->model = new $models;
    }
    public function index()
    {
        $this->list_temp = 'index';
        $listdb = $this->model->getList(['status' => '0','order' => 'id asc','limit' => '100']);
        return $this->viewAdminList($listdb);
    }

    public function editadd(){
        $_old = $this->model->getOne();
        if(date('Y-m-d',empty($_old['edittime']) ? 0 : (int) $_old['edittime']) == date('Y-m-d',time())){
            $this->success("暂无更新");
        }
        if($_old['status'] == '0'){
            $this->result('','2','有更新');
        }
        /**  去检查更新   **/
        $res = $this->model->getUplist();
        $this->result('',$res['code'] == '1' ? '2' : $res['code'],$res['msg']);
    }

    public function updatelist(){
        $data = Common::data_trim(input('post.'));
        //  检测升级文件是否存在
        $data['update'] = empty($data['update']) ? 'donw' : $data['update'];
        $_updata = 'get'.$data['update'];
        $this->$_updata($data);
    }
    protected function getdonw($data){
        if (!file_exists(runtime_path("update/{$data['nos']}"))) {
            @mkdir(runtime_path("update/{$data['nos']}"),0777,true);
        }
        $this->result(['update' => 'gzip'],'1','准备获取更新文件...');
    }
    protected function getgzip($data){
        $res = false;
        if (file_exists(runtime_path("update/{$data['nos']}")."{$data['nos']}.zip")) {
            $res = true;
        }else{
            $res = $this->model->getUpfile($data);
        }
        $this->result(['update' => $res ? 'uzip' : 'end'],$res ? '1' : '0',$res ? '更新文件读取成功...' : '更新文件读取失败！');
    }
    protected function getuzip($data){
        $_zip = new ZipArchive();
        if($_zip->open(runtime_path("update/{$data['nos']}")."{$data['nos']}.zip")){
            if (!file_exists(runtime_path("update/{$data['nos']}/file"))) {
                @mkdir(runtime_path("update/{$data['nos']}/file"),0777,true);
            }
            $_zip->extractTo(runtime_path("update/{$data['nos']}/file"));
            $_zip->close();
            $this->result(['update' => 'readold'],'1','开始读取原有文件...');
        }
    }
    protected function getreadold($data){
        $_list = get_fiellist(runtime_path("update/{$data['nos']}/file"));
        cache('update_fiellist',$_list);
        $this->result(['update' => 'copyold'],'1','开始备份原有文件...');
    }
    protected function getcopyold($data){
        $_list = cache('update_fiellist');
        $data['row'] = empty($data['row']) ? '0' : $data['row'];
        if(count($_list) > $data['row']){
            $_file = str_replace(runtime_path("update/{$data['nos']}/file"),'',$_list[$data['row']]);
            if(!is_file(root_path().$_file)){
                $data['row'] = $data['row'] + 1;
                return $this->getcopyold($data);
            }
            $res = Common::copy_files(root_path().$_file,'backup'.$_file);
            $this->result(['update' => 'copyold','row' => $data['row'] + 1],$res ? '1' : '0',$res ? "备份文件:{$_file}" : "备份文件:{$_file}失败");
        }
        $this->result(['update' => 'copynew'],'1',"备份文件完成...");
    }
    protected function getcopynew($data){
        $_list = cache('update_fiellist');
        $data['row'] = empty($data['row']) ? '0' : $data['row'];
        if(count($_list) > $data['row']){
            $_file = str_replace(runtime_path("update/{$data['nos']}/file"),'',$_list[$data['row']]);
            $_filename = basename($_file);
            $_filename = explode('.',$_filename);
            if($_filename['0'] == 'upsql'){
                cache('update_sql',$_list[$data['row']]);
                $data['row'] = $data['row'] + 1;
                return $this->getcopynew($data);
            }
            $res = Common::copy_files($_list[$data['row']],$_file);
            $this->result(['update' => 'copynew','row' => $data['row'] + 1],$res ? '1' : '0',$res ? "升级文件:{$_file}" : "升级文件:{$_file}失败");
        }
        if(!empty(cache('update_sql'))){
            $this->result(['update' => 'sqlold'],'1',"开始备份数据库");
        }
        $this->model->whereEditionNo($data['nos'])->update(['status' => '1','addtime' => time(),'edittime' => time()]);
        $this->result(['update' => 'end'],'1000',"系统升级完成");
    }
    protected function getsqlold($data){
        $backupdir = root_path('backup/sql');
        if (isset($data['backdir']) && $data['backdir'] != '') {
            $backupdir = $data['backdir'];
        }
        if (!file_exists($backupdir)) {
            mkdir($backupdir,0777,true);
        }
        $_default = config('database.default');
        $_database = config('database.connections');
        $_database = $_database[$_default];
        $backup = BackupFactory::instance($_default, "{$_database['hostname']}:{$_database['hostport']}", $_database['database'], $_database['username'], $_database['password'], $_database['charset']);
        $result = $backup->setbackdir($backupdir)->setvolsize(2)->ajaxbackup(empty($data['sqlres']) ? [] : $data['sqlres']);
        if($result['totalpercentage'] < '100'){
            $data['sqlres'] = $result;
        }
        $this->result($result['totalpercentage'] < '100' ? $data : ['update' => 'newsql'],'1',$result['totalpercentage'] < '100' ? "备份 {$result['nowtable']} 成功" : '备份完成,开始更新数据库');
    }
    protected function getnewsql($data){
        $_list = cache('update_sql');
        //  读取文件内容
        $_list = require $_list;
        $_default = config('database.default');
        $_database = config('database.connections');
        $_database = $_database[$_default];
        $data['fun'] = empty($data['fun']) ? [] : $data['fun'];
        foreach ($_list as $k => $v){
            if(!empty($data['fun']) && in_array($k,$data['fun'])){
                continue;
            }
            switch ($k){
                case 'add_table':
                    $table_list = Db::query("SHOW TABLE STATUS");
                    $table_list = array_column($table_list,'Name');
                   foreach ($v as $k1 => $v1){
                       if (in_array($_database['prefix'].$v1['table_name'],$table_list)){
                           continue;
                       }
                       $tablemodel = new TableModel();
                       $_table = $tablemodel->setCharset($v1['charset'])->setEngine($v1['engine'])->setTablename($_database['prefix'].$v1['table_name'])->setComment($v1['comment']);
                       $fmodel = [];
                       foreach ($v1['data'] as $k2 => $v2) {
                           array_push($fmodel,$this->addfiled($v2));
                       }
                       $res = Factory::instance($_default,"{$_database['hostname']}:{$_database['hostport']}", $_database['database'], $_database['username'], $_database['password'], $_database['charset']);
                       $res->createTable($_table, $fmodel);
                   }
                   array_push($data['fun'],'add_table');
                   $this->result($data,'1','新增数据表完成');
                   break;
                case 'add_filed':
                    foreach ($v as $k1 => $v1){
                        $table_list = Db::name($v1['table_name'])->getTableFields();
                        $fmodel = [];
                        $tablemodel = new TableModel();
                        $tablemodel->setTablename($_database['prefix'].$v1['table_name']);
                        $res = Factory::instance($_default,"{$_database['hostname']}:{$_database['hostport']}", $_database['database'], $_database['username'], $_database['password'], $_database['charset']);
                        foreach ($v1['data'] as $k2 => $v2) {
                            if(in_array($v2['name'],$table_list)){
                                continue;
                            }
                            $res->addColumn($tablemodel, $this->addfiled($v2));
                        }
                    }
                    array_push($data['fun'],'add_filed');
                    $this->result($data,'1','新增数据表字段完成');
                    break;
                case 'add_value':
                    foreach ($v as $k1 => $v1){
                        foreach ($v1['data'] as $k2 => $v2){
                            if(!empty($v2['data']['pid']) && !is_numeric($v2['data']['pid'])){
                                $v2['data']['pid'] = Db::name($v1['table_name'])->where($v2['file'],$v2['data']['pid'])->value('id');
                            }
                            $_old = Db::name($v1['table_name'])->where($v2['file'],$v2['data'][$v2['file']])->find();
                            if(!empty($_old)){
                                continue;
                            }
                            Db::name($v1['table_name'])->insert($v2['data']);
                        }
                    }
                    array_push($data['fun'],'add_value');
                    $this->result($data,'1','新增数据完成');
                    break;
            }
        }
        $this->model->whereEditionNo($data['nos'])->update(['status' => '1','addtime' => time(),'edittime' => time()]);
        $this->result(['update' => 'end'],'1000',"系统升级完成");
    }

    protected function addfiled($data){
        $fmodel = new ColumnModel();
        return $fmodel->setType($data['type'])->setLen((int) $data['len'])->setName($data['name'])->setIsPk($data['pk'])->setIncrement($data['inc'])->setComment($data['comment'])->setIsnull($data['isnull'])->setDefaultval($data['default']);
    }

}