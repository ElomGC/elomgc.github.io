<?php
declare(strict_types = 1);

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\wormview\AddEditList;
use app\facade\hook\Common;
use phpspirit\databackup\BackupFactory;
use phpspirit\databackup\RecoveryFactory;
use think\facade\View;

class Sqldata extends AdminBase
{
    use AddEditList;
    protected $list_temp = false;
    protected function initialize(){
        parent::initialize();
        $this->contauth = $this->CheckAuth('create,edit,del');
        $models = "app\\common\\model\\SqlBase";
        $this->model = new $models;
        $this->getConf();
    }
    protected function getConf(){
        if ($this->request->action() == 'index'){
            $this->list_file = [
                ['file' => 'Name','title' => '数据表名','type' => 'text','width' => '50%'],
                ['file' => 'Engine','title' => '数据引擎','type' => 'text','width' => '10%','textalign' => 'center',],
                ['file' => 'Data_length','title' => '数据长度','type' => 'text','width' => '10%','textalign' => 'center',],
                ['file' => 'Collation','title' => '排序规则','type' => 'text','width' => '15%','textalign' => 'center',],
                ['file' => 'Comment','title' => '数据表','type' => 'text','width' => '15%'],
            ];
        }
    }
    public function index(){
        $backupdir = getDirlist(public_path('backup/'));
        arsort($backupdir);
        $backupdir = array_merge([],$backupdir);
        View::assign(['list_file' => $this->list_file,'listdb' => $backupdir]);
        return view();
    }
    public function getlist()
    {
        $sql = $this->model->getTableList();
        $this->result($sql,'1','查询成功');
    }
    public function create(){
        $data = Common::data_trim(input('post.'));
        $backupdir = '';
        if (isset($data['backdir']) && $data['backdir'] != '') {
            $backupdir = $data['backdir'];
        } else {
            $backupdir = public_path('backup/'.date('Y-m-d-his'));
        }
        if (!file_exists($backupdir)) {
            mkdir($backupdir,0777,true);
        }
        $_default = config('database.default');
        $_database = config('database.connections');
        $_database = $_database[$_default];
        $backup = BackupFactory::instance($_default, "{$_database['hostname']}:{$_database['hostport']}", $_database['database'], $_database['username'], $_database['password'], $_database['charset']);
        $result = $backup->setbackdir($backupdir)->setvolsize(2)->ajaxbackup($data);
        $this->result($result,'1','成功');
    }
    public function save()
    {
        $data = Common::data_trim(input('post.'));
        if (empty($data['backdir'])) {
            $this->error("请选择要恢复的备份文件");
        }
        $_default = config('database.default');
        $_database = config('database.connections');
        $_database = $_database[$_default];
        $backupdir = public_path("backup").$data['backdir'];
        $backup = RecoveryFactory::instance($_default, "{$_database['hostname']}:{$_database['hostport']}", $_database['database'], $_database['username'], $_database['password'], $_database['charset']);
        $result = $backup->setSqlfiledir($backupdir)->ajaxrecovery(empty($data['res']) ? [] : $data['res']);
        $this->result($result,'1','成功');
    }

}