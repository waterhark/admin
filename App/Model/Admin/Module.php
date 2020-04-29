<?php


namespace App\Model\Admin;


use App\Model\BaseModel;
use EasySwoole\ORM\Utility\Schema\Table;

/**
 * Class AdminModule
 * @package App\Model\Admin
 * 管理后台模块
 */
class Module extends BaseModel
{
    protected $tableName = 'admin_module';

    function schemaInfo(bool $isCache = true): Table
    {
        $table = new Table($this->tableName);
        $table->colInt('actionId')->setIsAutoIncrement()->setColumnComment('功能id')->setIsUnique()->setIsPrimaryKey();
        $table->colVarChar('actionCode', 45)->setIsUnique()->setColumnComment('功能代号')->setIsNotNull();
        $table->colVarChar('actionNote', 255)->setColumnComment('功能备注');
        $table->colVarChar('moduleCode', 45)->setColumnComment('模块代号')->setIsNotNull();
        $table->colVarChar('moduleNote', 255)->setColumnComment('模块备注');
        $table->setIfNotExists();
        return $table;
    }
}