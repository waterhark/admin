<?php


    namespace App\HttpController\Api\Admin;


    use App\HttpController\Api\ApiBase;
    use App\Model\Admin\AccessModule;
    use App\Model\Admin\Module;
    use App\Model\Admin\User;
    use EasySwoole\Http\Message\Status;

    abstract class AbstractBase extends ApiBase
    {
        protected $noneAuthAction = [];
        protected $who;
        protected $acl;

        const ADMIN_COOKIE_NAME = 'admin_session';
        const NO_MODULE = -2;
        const NO_ACCESS_MODULE = -1;
        const HAVE_ACCESS_MODULE = 1;

        abstract protected function moduleName(): string;

        protected function onRequest(?string $action): ?bool
        {
            //控制器为pool模式，强制重置，
            $this->who = null;
            $this->acl = null;
            if (in_array($action, $this->noneAuthAction)) {
                return true;
            }
            if (!$this->who()) {
                $this->writeJson(Status::CODE_UNAUTHORIZED, [
                    'errorCode' => -2
                ], '请重新登录');
                return false;
            }
            $acl = $this->adminAcl();
            switch ($acl) {
                case self::NO_MODULE:
                    $this->writeJson(Status::CODE_UNAUTHORIZED, [
                        'errorCode' => self::NO_MODULE
                    ], '此模块没有注册');
                    return false;
                case self::NO_ACCESS_MODULE:
                    $this->writeJson(Status::CODE_UNAUTHORIZED, [
                        'errorCode' => self::NO_ACCESS_MODULE
                    ], '您没有此模块权限');
                    return false;
                case self::HAVE_ACCESS_MODULE:
                    return true;
                default:
                    $this->writeJson(Status::CODE_UNAUTHORIZED, [
                        'errorCode' => -1
                    ], '权限不在判断范围内');
                    return false;
            }
        }


        protected function who(): ?User
        {
            if (!$this->who) {
                $cookie = $this->request()->getCookieParams(static::ADMIN_COOKIE_NAME);
                if (empty($cookie)) {
                    $cookie = $this->request()->getRequestParam(static::ADMIN_COOKIE_NAME);
                }
                if ($cookie) {
                    $this->who = User::create()->where(['session' => $cookie])->get();
                }
            }
            return $this->who;
        }


        protected function adminAcl(): ?int
        {
            $moduleCode = $this->moduleName();
//            查询模块是否存在，不存在则报错
            $result = Module::create()->where('moduleCode', $moduleCode)->count();
            if ($result <= 0) {
                return self::NO_MODULE;
            }
            //查询用户是否有这个模块的权限
            $ret = AccessModule::create()->where('adminId', $this->who()->adminId)->where('moduleCode',
                $moduleCode)->count();
            if ($ret > 0) {
                return self::HAVE_ACCESS_MODULE;
            }

            return self::NO_ACCESS_MODULE;
        }
    }