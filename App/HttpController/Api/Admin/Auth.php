<?php


    namespace App\HttpController\Api\Admin;


    use EasySwoole\Http\Message\Status;
    use \App\Model\Admin\User;
    use EasySwoole\HttpAnnotation\AnnotationTag\Method;
    use EasySwoole\HttpAnnotation\AnnotationTag\Param;
    use EasySwoole\Session\Session;
    use App\Validate\Admin\UserValidate;

    class Auth extends AbstractBase
    {
        protected $noneAuthAction = [
            'login',
            'logout'
        ];

        protected function moduleName(): string
        {
            return 'auth';
        }

        public function register()
        {
            $params = $this->paramsValidate(new UserValidate);
            if ($params === false) {
                return false;
            }
            $userModel = new User();

            $userInfo = $userModel->get(['account' => $params['account']]);
            if ($userInfo) {
                return $this->error('帐号已存在');
            }

            $userModel->account = $params['account'];
            $userModel->password = md5($params['password']);
            if (!$userModel->save()) {
                return $this->error('帐号注册失败');
            }

            return $this->success([], '帐号注册成功');
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
            Session::getInstance()->del(self::ADMIN_COOKIE_NAME);
            $this->response()->setCookie(static::ADMIN_COOKIE_NAME, '', time() - 3600, '/');
            $this->writeJson(Status::CODE_OK, '', "login out successed!");
            return true;
        }
    }