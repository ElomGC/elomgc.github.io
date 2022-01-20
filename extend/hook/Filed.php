<?php
namespace hook;

use worm\Table;

class Filed {
//  列表转换
    public function FiledsToLayuiTable($file_list){
        $res = array();
        $tablemodel = new Table();
        foreach ($file_list as $k => $v){
            $res[] = $tablemodel->getLayuiTable($v);
        }
        return json_encode($res,JSON_UNESCAPED_UNICODE);
    }
    //  列表转换
    public function FiledsToTable($file_list,$data = array()){
        $res = array();
        $tablemodel = new Table();
        foreach ($file_list as $k => $v){
            $res[] = $tablemodel->getTableFiled($v,$data);
        }
        return $res;
    }
    //  按钮转换
    public function BtnToTable($file_list,$data = array()){
        $res = array();
        $tablemodel = new Table();
        foreach ($file_list as $k => $v){
            $res[] = $tablemodel->getTableBtn($v,$data);
        }
        return $res;
    }
    //  表单转换
    public function FiledsToForm($file_list,$data = array()){
        $res = array();
        $tablemodel = new Table();
        foreach ($file_list as $k => $v){
            $res[] = $tablemodel->getFormFiled($v,$data);
        }
        return $res;
    }
}