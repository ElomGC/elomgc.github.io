<?php



ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');

include dirname(__FILE__) . '/../src/Factory.php';
include dirname(__FILE__) . '/../src/ISkeleton.php';
include dirname(__FILE__) . '/../src/mysql/ColumnModel.php';
include dirname(__FILE__) . '/../src/mysql/TableModel.php';
include dirname(__FILE__) . '/../src/mysql/Skeleton.php';


use phpspirit\dbskeleton\Factory;
use phpspirit\dbskeleton\mysql\ColumnModel;
use phpspirit\dbskeleton\mysql\TableModel;

$dbskeleton = Factory::instance('mysql', '127.0.0.1:3306', 'test', 'root', 'root');

$tablemodel = new TableModel();
$tablemodel->setCharset('utf8mb4')
    ->setEngine('InnoDB')
    ->setTablename('test')
    ->setComment('测试表');

$fieldmodel = new ColumnModel();
$fieldmodel->setType('int')
    ->setLen(11)
    ->setName('id')
    ->setIsPk(true)
    ->setIncrement(true)
    ->setComment('自增长');


//创建一个表 第一个参数 表模型 第二个参数 字段模型数组 
$dbskeleton->createTable($tablemodel, [$fieldmodel]);
/**
 *  alterTable(旧表模型，新表模型) 修改表
 *  dropTable(表模型) 删除表
 */



$fieldtitlemodel = new ColumnModel();
$fieldtitlemodel->setType('varchar')
    ->setLen(255)
    ->setName('title')
    ->setComment('标题');
//添加字段 第一个参数 表模型 第二个参数 字段模型
$dbskeleton->addColumn($tablemodel, $fieldtitlemodel);
/**
 *  changeColumn(TableModel $tableModel, ColumnModel $oldcolumnModel, ColumnModel $newcolumnModel) 修改字段
 *  dropColumn(TableModel $tableModel, ColumnModel $columnModel)  删除字段
 */
