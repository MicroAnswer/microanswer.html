<?php
/**
 * Created by IntelliJ IDEA.
 * User: Microanswer
 * Date: 2018/3/16
 * Time: 10:24
 */
require_once __DIR__ . '/../user/Util.php';

use user\Util;
use user\SQL;


try {
    header('Content-Type: application/json');

    $util = new Util();

    /**
     * 获取留言消息
     */
    $util->post("getMessage", function ($data) {
        global $util;

        $offset = $data['offset'];
        if ($util->isStringEmpty($offset)) {
            $offset = 0;
        }
        $limit = $data['limit'];
        if ($util->isStringEmpty($limit)) {
            $limit = 20;
        }
        $charset = $data['charset'];
        if ($util->isStringEmpty($charset)) {
            $charset = "utf8";
        }

        $sql = new SQL($util->db($charset));

        // 先查询出总的条数
        $result = $sql->select("count(*)")->from("msg")->d0();

        if ($result instanceof mysqli_result && $result->num_rows > 0) {
            // 获取到总的条数；
            $total = $result->fetch_array(MYSQLI_NUM)[0];
        } else {
            // 查询结果不是结果集类型的，那么查询必然出现了错误
            echo $util->buildReturnJson2(500, null);
            return;
        }

        $result = $sql->select("id,content,FROM_UNIXTIME(createAt,'%Y-%m-%d %H:%i:%S') createAt,FROM_UNIXTIME(updateAt,'%Y-%m-%d %H:%i:%S') updateAt")->from("msg")->desc("updateAt")->limit($offset, $limit)->d0();
        if ($result instanceof mysqli_result && $result->num_rows > 0) {
            echo $util->buildReturnJson(200, "获取成功", ['total' => $total, 'rows' => mysqli_fetch_all($result, MYSQLI_ASSOC)]);
        } else {
            echo $util->buildReturnJson(200, "获取失败, " . mysqli_error($sql->getConn()) . ' sql:' . $sql->getSql(), ['total' => $total]);
        }
    });

    /**
     * 对数独计算器进行一次赞
     */
    $util->post("sudoSolverHelpedMe", function ($data) {
        global $util;

        $createAt = time();

        $sql = new SQL($util->db(null));
        $result = $sql-> insertInto("likesudosolver")->set([
            'userAgent' => $_SERVER['HTTP_USER_AGENT'],
            'createAt' => $createAt,
            'host' => $_SERVER['HTTP_HOST'],
            'ip' => $util->getClientIp()
        ])->d0();

        if ($result) {

            // 插入成功，查询总数目
            $result = $sql -> select("count(id)")->from("likesudosolver")->d0();

            if ($result instanceof mysqli_result) {
                echo $util->buildReturnJson2(200, $result->fetch_array(MYSQLI_NUM)[0]);
            } else {
                echo $util->buildReturnJson2(701, mysqli_error($sql->getConn()));
            }
        } else {
            echo $util->buildReturnJson2(500, mysqli_error($sql->getConn()));
        }
    });

    /**
     * 获取数独计算器的总赞数
     */
    $util->post("sudoSolverZanCount", function ($data) {
        global $util;

        $sql = new SQL($util->db(null));

        // 查询总数目
        $result = $sql -> select("count(id) as zanCount")->from("likesudosolver")->d0();

        if ($result instanceof mysqli_result) {
            echo $util->buildReturnJson2(200, $result->fetch_array(MYSQLI_ASSOC));
        } else {
            echo $util->buildReturnJson2(701, mysqli_error($sql->getConn()));
        }
    });

    /**
     * 赞 首页， 或则获取总赞数
     */
    $util->post("zhanMicroanswer", function ($data) {
        global $util;

        $zhan = '';
        if (key_exists('zhan', $data)) {
            $zhan = $data['zhan'];
        }

        $sql = new SQL($util->db(null));

        if ($zhan === 'yes') {

            $createAt = time();

            // 进行赞
            $sql->insertInto('likeme')->set([
                'createAt' => $createAt,
                'userAgent' => $_SERVER['HTTP_USER_AGENT'],
                'host' => $_SERVER['HTTP_HOST'],
                'ip' => $util->getClientIp()
            ])->d0();
        }

        // 查询总赞数
        $result = $sql -> select('count(id) as zanCount') ->from('likeme')->d0();

        if ($result instanceof mysqli_result) {
            echo $util->buildReturnJson(200, '获取成功', $result->fetch_array(MYSQLI_ASSOC));
        } else {
            echo $util->buildReturnJson2(701, mysqli_error($sql->getConn()));
        }

    });

    $util->start();
} catch (Throwable $exception) {
    echo '{"code": 500, "msg": "服务器错误[' . addslashes($exception->getMessage()) . ']", "data": ""}';
}
