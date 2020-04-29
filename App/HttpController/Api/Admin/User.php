<?php


    namespace App\HttpController\Api\Admin;


    class User extends AbstractBase
    {

        protected function moduleName(): string
        {
            // TODO: Implement moduleName() method.
            return 'User';
        }

        public function userAdd(): bool
        {
            $this->actionPrivilege("USER_ADD");
            $this->response()->write("this is user add");
            return true;
        }


        public function userDel(): bool
        {
            $this->actionPrivilege("USER_DELETE");
            $this->response()->write("this is user delete");
            return true;
        }
    }