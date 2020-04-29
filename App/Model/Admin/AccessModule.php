<?php


namespace App\Model\Admin;


use App\Model\BaseModel;
use EasySwoole\ORM\Utility\Schema\Table;

/**
 * Class AccessModule
 * @package App\Model\Admin
 * @property $accessId
 * @property $adminId
 * @property $moduleId
 * @property $accessHash
 */
class AccessModule extends BaseModel
{
    protected $tableName = 'admin_access_module';

    function schemaInfo(bool $isCache = true): Table
    {
        $table = new Table($this->tableName);
        $table->colInt('accessId')->setIsUnique()->setIsAutoIncrement()->setIsPrimaryKey();
        $table->colInt('adminId')->setIsNotNull()->setColumnComment("管理员id");
        $table->colVarChar("moduleCode",40)->setIsNotNull()->setColumnComment("模块code");
        $table->colVarChar('actionCode',40)->setIsNotNull()->setColumnComment("功能权限code");
        $table->colVarChar('accessHash')->setIsNotNull()->setIsUnique()->setColumnComment("hash(adminId+moduleCode+actionCode)");
        $table->setIfNotExists();
        return $table;
    }
}
