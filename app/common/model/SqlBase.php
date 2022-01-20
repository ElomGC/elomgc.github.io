<?php
declare(strict_types = 1);

namespace app\common\model;


use think\facade\Db;

class SqlBase
{
    protected $pre;

    public function __construct(){
        $this->pre = config('database.prefix');
    }
    public function getTableList(){
        $totalsize = 0;
        $query = Db::query("SHOW TABLE STATUS");
        $_res = [];
        foreach($query as $k => $v){
            if(!preg_match("/^{$this->pre}/",$v['Name'])){
                continue;
            }
            $totalsize = $totalsize + $v['Data_length'];
            $v['Data_length'] = CountSize($v['Data_length']);
            $v['Annotation'] = $v['Comment'];
            array_push($_res,$v);
        }
        $totalsize = CountSize($totalsize);
        $res = [
            'totalsize' => $totalsize,
            'data' => $_res,
        ];
        return $res;
    }
}