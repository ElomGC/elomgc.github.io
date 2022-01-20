<?php

namespace phpspirit\dbskeleton;

interface ISkeleton
{
    /**
     * 创建表
     * @param $tableModel TableModel
     * @param $columnModels
     */
    public function createTable( $tableModel,  array $columnModels);

    /**
     * 修改表
     */
    public function alterTable( $oldtableModel,  $newtableModel);

    /**
     * 删除表
     */
    public function dropTable( $tableModel);

    /**
     * 添加字段
     */
    public function addColumn( $tableModel,  $columnModel);

    /**
     * 修改字段
     */
    public function changeColumn( $tableModel,  $oldcolumnModel,  $newcolumnModel);

    /**
     * 删除字段
     */
    public function dropColumn( $tableModel,  $columnModel);
}
