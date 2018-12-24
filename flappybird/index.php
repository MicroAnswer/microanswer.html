<?php
/**
 * Created by IntelliJ IDEA.
 * User: Micro
 * Date: 2018-3-4
 * Time: 23:33
 */

require_once __DIR__ . '/../user/Util.php';

use user\Util;
use user\SQL;

header('Content-Type: application/json');
try {
    $util = new Util();

    /**
     * 上传新的成绩
     */
    $util->post('uploadScore', function ($data) {
        global $util;
        $token = $data['token'];

        if ($util->isStringEmpty($token)) {
            echo $util->buildReturnJson(501, Util::$STATUS['501'], null);
            return;
        }

        // 把成绩插入数据库
        $util->checkToken($token, function ($code, $userId) use ($data) {
            global $util;

            if ($code != 200) {
                // token验证失败
                echo $util->buildReturnJson2($code, null);
                return;
            }


            $score = $data['score'];
            if ($score <= 0) {
                // 成绩小于1，不需要上传了
                echo $util->buildReturnJson(601, Util::$STATUS['601'], null);
                return;
            }

            $createAt = time();
            $updateAt = $createAt;

            $gameversion = $data['gv'];
            if ($util->isStringEmpty($gameversion)) {
                echo $util->buildReturnJson2(603, $gameversion);
                return;
            }

            // 先查询是否该用户已经有成绩信息了，
            $sql = new SQL($util->db(null));
            $result = $sql->select("*")->from('flappybirdscore')->where('userId', '=', $userId)->d0();
            if ($result instanceof mysqli_result && $result->num_rows == 1) {
                // 有成绩信息, 更新数据
                $result = $sql->update('flappybirdscore')->set([
                    'score' => $score,
                    'updateAt' => $updateAt,
                    'gameversion' => $gameversion
                ])->where('id', '=', $result->fetch_array(MYSQLI_ASSOC)['id'])->and_('score', '<', $score)->d0();
            } else {
                // 没有成绩信息，新增数据
                $result = $sql->insertInto('flappybirdscore')->set([
                    'score' => $score,
                    'userId' => $userId,
                    'updateAt' => $updateAt,
                    'createAt' => $createAt,
                    'deviceinfo' => $data['deviceinfo']?:'-',
                    'gameversion' => $gameversion
                ])->d0();
            }
            if ($result) {
                echo $util->buildReturnJson(Util::$CODE_SUCCESS, "上传成功", null);
            } else {
                echo $util->buildReturnJson2(602, null);
            }

        });
    });

    /**
     * 获取某用户的成绩, 不需要了、
     */
    $util->post("getScore", function ($data){
        global $util;
        $token = $data['token'];
        if ($util->isStringEmpty($token)) {
            echo $util->buildReturnJson2(501,null);
            return;
        }

        $util->checkToken($token, function ($code, $userId) {
            global $util;
            if ($code === 200) {
                // 效验成功， 开始查询分数信息

                $sql = new SQL($util->db(null));
                $result = $sql->select("*")->from('flappybirdscore')->where('userId', '=', $userId)->d0();

                if ($result instanceof mysqli_result && $result->num_rows == 1) {
                    echo $util->buildReturnJson2(200, $result->fetch_array(MYSQLI_ASSOC));
                } else {
                    echo $util->buildReturnJson2(604,null);
                }

            } else {
                echo $util->buildReturnJson2($code, null);
            }
        });
    });

    /**
     * 获取排行
     */
    $util->post('getScores', function ($data) {
        global $util;
        $sql = new SQL($util->db(null));

        // 查询分数排行
        $scoreResult = $sql->select('score,userId,flappybirdscore.createAt,flappybirdscore.updateAt,gameversion,user.name as name')->from('flappybirdscore LEFT JOIN user ON flappybirdscore.userId=user.id')
            ->desc('score')->d0();

        if ($scoreResult instanceof mysqli_result && $scoreResult->num_rows > 0) {
            // 有成绩排行数据
            echo $util->buildReturnJson(Util::$CODE_SUCCESS, "获取成功", $scoreResult->fetch_all(MYSQLI_ASSOC));
            return;
        } else {
            // 没有数据
            echo $util->buildReturnJson(600, Util::$STATUS['600'], $sql->getSql());
            return;
        }

    });

    $util->start();
} catch (Exception $exception) {
    echo '{code: 500, msg: "服务器错误[' . $exception->getMessage() . '", data: null}';
}