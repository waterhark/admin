<?php

    namespace EasySwoole\EasySwoole;


    use EasySwoole\EasySwoole\Swoole\EventRegister;
    use EasySwoole\EasySwoole\AbstractInterface\Event;
    use EasySwoole\Http\Request;
    use EasySwoole\Http\Response;
    use EasySwoole\ORM\Db\Connection;
    use EasySwoole\ORM\DbManager;
    use EasySwoole\ORM\Db\Config as OrmConfig;
    use EasySwoole\Session\Session;
    use EasySwoole\Session\SessionFileHandler;

    class EasySwooleEvent implements Event
    {

        public static function initialize()
        {
            // TODO: Implement initialize() method.
            date_default_timezone_set('Asia/Shanghai');
            $config = new OrmConfig(Config::getInstance()->getConf('MYSQL'));
            DbManager::getInstance()->addConnection(new Connection($config));
        }

        public static function mainServerCreate(EventRegister $register)
        {
            // TODO: Implement mainServerCreate() method.
            //可以自己实现一个标准的session handler
            $handler = new SessionFileHandler(EASYSWOOLE_TEMP_DIR);
            //表示cookie name   还有save path
            Session::getInstance($handler, 'easy_session', 'session_dir');
        }

        public static function onRequest(Request $request, Response $response): bool
        {
            // TODO: Implement onRequest() method.
            $cookie = $request->getCookieParams('easy_session');
            if (empty($cookie)) {
                $sid = Session::getInstance()->sessionId();
                $response->setCookie('easy_session', $sid);
            } else {
                Session::getInstance()->sessionId($cookie);
            }
            return true;
        }

        public static function afterRequest(Request $request, Response $response): void
        {
            // TODO: Implement afterAction() method.
        }
    }