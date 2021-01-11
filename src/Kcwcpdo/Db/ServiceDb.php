<?php

namespace Kcwcpdo\Db;


/**

 * 
 * Desc:提供数据库单独服务基本swoole
 */

class ServiceDb
{
    public $conf = [
        "Ip" => "0.0.0.0",
        "Port" => 9501,
        "Daemonize" => false,

    ];
    public function start()
    {

        $ROOT = (__DIR__ . "/../../../../../../");
        if (file_exists($ROOT . "conf/servicepdo.ini")) {
            $this->conf = parse_ini_file($ROOT . "conf/servicepdo.ini");
        }
        $http = new \Swoole\Http\Server($this->conf["Ip"], intval($this->conf["Port"]));
        $http->set([
            //'ssl_cert_file' =>   $config["sslCertFile"],
            //'ssl_key_file' =>   $config["sslKeyFile"],
            //'open_http2_protocol' => true,
            //	'reactor_num' => intval($config["reactorNum"]),
            //'worker_num' => intval($config["workerNum"]),
            'daemonize' => intval($this->conf["Daemonize"]), //是否后台运行
            'tcp_fastopen' => true,
            //'user' => $config["serverUser"],
            //'group' => $config["serverGroup"],
            //'chroot' => $config["serverChroot"]
        ]);
        $http->db = null; //定义数据库
        $http->on('WorkerStart', function ($serv, $worker_id) use ($http) {
            $pdoparam = [
                \PDO::ATTR_PERSISTENT => true,
                \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
            ];
            $http->db =  \Kcwcpdo\Db\DAOPDO::getInstance($pdoparam); //加载db
        });
        $http->on('request', function ($request, $response) use ($http) {
            if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
                $response->end();
                return;
            }
            list($controller, $action) = explode('/', trim($request->server['request_uri'], '/'));
            $controller = '\\Kcwcpdo\\Db\\' . $controller;
            try {
                (new $controller)->$action($request, $response, $http->db);
            } catch (\Exception $e) {
                $response->header("Content-Type", "text/html; charset=utf-8");
                $response->end(json_encode(["code" => 500, "data" => $e->getMessage(), "msg" => "Error"]));
            }
        });

        $http->start();
    }
}

//定义服务接口类
class ServiceDbController
{
    //查询方法
    public function query($req, $rep, $db)
    {
        if (is_null($req->post) || is_null($req->post["sql"])) {
            $rep->header("Content-Type", "text/json; charset=utf-8");
            $rep->end(json_encode(["code" => 500, "data" => [], "msg" => "请输入sql"]));
        } else {
            $rep->header("Content-Type", "text/json; charset=utf-8");
            if (!stristr($req->post["sql"], 'SELECT')) {
                $rep->end(json_encode(["code" => 500, "data" => [], "msg" => "SQL调用错误"]));
                return;
            }
            // $rs = $db->query($req->post["sql"]);
            $result = $db->prepareSql($req->post["sql"]);
            $result->execute($req->post["param"]);
            $data = $result->fetchAll();
            $rep->end(json_encode(["code" => 200, "data" => $data, "msg" => "Ok"]));
        }
    }
    //更新与插入方法
    public function execute($req, $rep, $db)
    {
        if (is_null($req->post) || is_null($req->post["sql"])) {
            $rep->header("Content-Type", "text/json; charset=utf-8");
            $rep->end(json_encode(["code" => 500, "data" => [], "msg" => "请输入sql"]));
        } else {
            $rep->header("Content-Type", "text/json; charset=utf-8");
            if (stristr($req->post["sql"], 'SELECT')) {
                $rep->end(json_encode(["code" => 500, "data" => [], "msg" => "SQL调用错误"]));
                return;
            } elseif (stristr($req->post["sql"], 'INSERT')) {
                $result = $db->prepareSql($req->post["sql"]);
                $result->execute($req->post["param"]);
                $lastId = $db->lastInsertId();
                $rep->end(json_encode(["code" => 200, "data" => $lastId, "msg" => "Ok"]));
            } elseif (stristr($req->post["sql"], 'UPDATE')) {
                $result = $db->prepareSql($req->post["sql"]);
                $result->execute($req->post["param"]);
                $affected2 = $result->rowCount();
                $rep->end(json_encode(["code" => 200, "data" => $affected2, "msg" => "Ok"]));
            } elseif (stristr($req->post["sql"], 'DELETE')) {
                $result = $db->prepareSql($req->post["sql"]);
                $result->execute($req->post["param"]);
                $affected2 = $result->rowCount();
                $rep->end(json_encode(["code" => 200, "data" => $affected2, "msg" => "Ok"]));
            } else {
                $rep->end(json_encode(["code" => 500, "data" => [], "msg" => "SQL 语言错误"]));
            }
        }
    }
    //执行多条sql事务方法
    public function transactionexecute($req, $rep, $db)
    {
        if (is_null($req->post) || is_null($req->post["sql"])) {
            $rep->header("Content-Type", "text/json; charset=utf-8");
            $rep->end(json_encode(["code" => 500, "data" => [], "msg" => "请输入sql"]));
        } else {
            $rep->header("Content-Type", "text/json; charset=utf-8");
            $rs = $db->execTransaction($req->post["sql"]);
            $rep->end(json_encode(["code" => 200, "data" => $rs, "msg" => "Ok"]));
        }
    }
}
