<?php


    namespace App\HttpController\Api\Admin;

    use EasySwoole\Http\Message\Status;
    use \App\Model\Admin\User;
    use EasySwoole\HttpAnnotation\AnnotationTag\Method;
    use EasySwoole\HttpAnnotation\AnnotationTag\Param;
    use EasySwoole\Session\Session;

    class Auth extends AbstractBase
    {
        protected $noneAuthAction = [
            'login'
        ];

        protected function moduleName(): string
        {
            return 'auth';
        }

        /**
         * @Method(allow={POST,GET})
         * @Param(name="account",from={GET,POST},notEmpty="账号没有填写")
         * @Param(name="password",from={GET,POST},notEmpty="密码没有填写")
         * @return bool|null
         * @throws \EasySwoole\Mysqli\Exception\Exception
         * @throws \EasySwoole\ORM\Exception\Exception
         * @throws \Throwable
         */
        public function login(): ?bool
        {
            $request = $this->request();
            $data = $request->getRequestParam();
            //实例化User
            $adminModel = User::create();
            $res = $adminModel->where("account", $data['account'])->get();
            $passwordSql = $res->password;
            $adminId = $res->adminId;
            //如果密码不对则管理员ID为NULL
            if (md5($data['password']) != $passwordSql) {
                $adminId = null;
            }
            // 如果密码账号没错则设置登录状态
            if ($adminId) {
                $hashSession = md5(microtime(true) . $adminId);
                $adminModel->update(['session' => $hashSession], ['adminId' => $adminId]);
                Session::getInstance()->set(static::ADMIN_COOKIE_NAME, $hashSession);
                $this->response()->setCookie(static::ADMIN_COOKIE_NAME, $hashSession, time() + 3600, '/');
                $adminInfo = ["admin_id" => $adminId, "admin_account" => $data['account']];
                $this->writeJson(Status::CODE_OK, $adminInfo, "login successed!");
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