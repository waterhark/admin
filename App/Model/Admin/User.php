<?php


namespace App\Model\Admin;


use App\Model\BaseModel;
use EasySwoole\ORM\Utility\Schema\Table;

/**
 * Class AdminUser
 * @package App\Model\Admin
 * @property $adminId
 * @property $account
 * @property $password
 * @property $session
 */
class User extends BaseModel
{
    protected $tableName = 'admin_list';

    function schemaInfo(bool $isCache = true): Table
    {
        $table = new Table($this->tableName);
        $table->colInt('adminId')->setIsAutoIncrement()->setIsPrimaryKey();
        $table->colVarChar('account', 18)->setIsUnique()->setIsNotNull();
        $table->colVarChar('password', 32);
        $table->colVarChar('session', 32)->setIsUnique();
        $table->setIfNotExists();
        return $table;
    }

}
