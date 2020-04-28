<?php


    namespace App\HttpController\Api\Admin;

    use EasySwoole\Http\Message\Status;
    use \App\Model\Admin\User;

    class Auth extends AbstractBase
    {
        protected $noneAuthAction = [
            'login'
        ];

        protected function moduleName(): string
        {
            return 'auth';
        }

        public function login(): ?bool
        {
            $request = $this->request();
            $data = $request->getRequestParam();
            // 没有输入账号则报错
            if (!$data['account']) {
                $this->writeJson(Status::CODE_BAD_REQUEST, [
                    'errorCode' => -2
                ], '没输入账号');
                return false;
            }
            // 没有输入密码则报错
            if (!$data['password']) {
                $this->writeJson(Status::CODE_BAD_REQUEST, [
                    'errorCode' => -1
                ], '没输入密码');
                return false;
            }
            //实例化User
            $admin_model = User::create();
            $res = $admin_model->where("account", $data['account'])->get();
            $password_sql = $res->password;
            $admin_id = $res->adminId;
            //如果密码不对则管理员ID为NULL
            if (md5($data['password']) != $password_sql) {
                $admin_id = null;
            }
            // 如果密码账号没错则设置登录状态
//        $admin_id = $this->checkPassword($admin_model,$data['account'],$data['password']);

            if ($admin_id) {
                $hashSession = md5(microtime(true) . $admin_id);
                $admin_model->update(['session' => $hashSession], ['adminId' => $admin_id]);
                $this->response()->setCookie('adminSession', $hashSession, time() + 3600, '/');
                $admin_info = ["admin_id" => $admin_id, "admin_account" => $data['account']];
                $this->writeJson(Status::CODE_OK, $admin_info, "login successed!");
            } else {
                $this->writeJson(Status::CODE_BAD_REQUEST, [
                    'errorCode' => -1
                ], '账号错误或者密码错误');
                return false;
            }
            return true;
        }

        public function logout()
        {

        }
    }