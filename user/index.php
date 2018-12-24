<?php
require_once __DIR__ . '/Util.php';

use user\Util;
use user\SQL;

try {
    error_reporting(E_ALL & ~E_NOTICE);
    header('Content-Type: application/json');

    $util = new Util();


    /**
     * 获取用户信息
     * 参数: token 登录token
     * 返回：用户信息
     */
    $util->post('getUserInfo', function ($data) {
        global $util;

        if ($util->isStringEmpty($data['token'])) {
            echo $util->buildReturnJson2(501, null);
            return;
        }

        // 查询token是否过期
        $sql = new SQL($util->db(null));
        $logins = $sql->select('token,userId,createAt, updateAt,loginType,loginTypeName,expireTime')
            ->from('login')->where('token', '=', $data['token'])->d0();

        if ($logins instanceof mysqli_result && $logins->num_rows > 0) {
            $login = $logins->fetch_array(MYSQLI_ASSOC);

            // 计算是否登陆信息有效
            $update = intval($login['updateAt']);
            $expireTime = intval($login['expireTime']);

            $expire = $update + $expireTime;

            if (time() >= $expire) {
                // 当前时间已经超过了过期时间
                echo $util->buildReturnJson2(502, null);
                return;
            } else {
                // 查询用户信息
                $userInfos = $sql->select('name,realName,sex,phone,signature,qq,wechat,city,area,province,country,createAt,updateAt')->from('user')->where('id', '=', $login['userId'])->d0();
                if ($userInfos instanceof mysqli_result && $userInfos->num_rows > 0) {
                    echo $util->buildReturnJson(Util::$CODE_SUCCESS, '获取成功', $userInfos->fetch_array(MYSQLI_ASSOC));
                } else {
                    echo $util->buildReturnJson(Util::$CODE_FAIL, '用户信息未找到:'.$sql->getSql(), null);
                }
                return;
            }

        } else {
            // 没有登陆信息
            echo $util->buildReturnJson2(501, null);
        }


    });

    /**
     * 登录
     * 参数：name 用户名
     *      password 用户密码
     * 返回：token 登录token
     */
    $util->post('login', function ($data) {
        global $util;

        try {
            $name = $util->nullThrow($data, 'name', '用户名不能为空');
            $password = $util->nullThrow($data, 'password', '密码不能为空');
            $loginType = $util->nullThrow($data, 'loginType', '必须指定登陆类型');
            $loginTypeName = $util->nullThrow($data, 'loginTypeName', '必须指定登陆类型文案');
        } catch (Exception $exception) {
            echo $util->buildReturnJson(Util::$CODE_FAIL, $exception->getMessage(), null);
            return;
        }

        $password = md5(md5($password) . $password);

        // 在用户表中寻找该用户信息
        $sql = new SQL($util->db(null));
        $result = $sql->select('id')->from('user')->where('name', '=', $name)->and_('password', '=', $password)->d0();
        if ($result instanceof mysqli_result && $result->num_rows > 0) {
            $r = $result->fetch_array(MYSQLI_ASSOC);
            $userId = $r['id'];

            // 先尝试寻早原有的登陆信息
            $loginInfo = $sql->select('token,userId,createAt, updateAt,loginType,loginTypeName,expireTime')
                ->from('login')->where('userId', '=', $userId)->d0();

            $token = $util->guid();
            $createAt = time();
            $updateAt = $createAt;
            $expireTime = 60 * 60 * 24 * 31; // 登陆有效期为31天

            if ($loginInfo instanceof mysqli_result && $loginInfo->num_rows > 0) {
                // 寻找到了原有的登陆信息
                // 设置新的token

                // 获取到结果及中的一条数据
                $login = $loginInfo->fetch_array(MYSQLI_ASSOC);

                // 计算是否登陆信息有效
                $update = intval($login['updateAt']);
                $expireTime = intval($login['expireTime']);

                $expire = $update + $expireTime;

                if (time() >= $expire) {
                    // 当前时间已经超过了过期时间， 已有的登录token失效
                    // 更新Token
                    $login['token'] = $token;
                    // $login['createAt'] = $createAt;
                    $login['updateAt'] = $updateAt;
                    $login['loginType'] = $loginType;
                    $login['loginTypeName'] = $loginTypeName;
                    // 保存
                    $s = $sql->update('login')->set($login)->where('userId', "=", $userId)->d0();
                    if ($s) {
                        echo $util->buildReturnJson(Util::$CODE_SUCCESS, '登录成功', $token);
                    } else {
                        echo $util->buildReturnJson(Util::$CODE_FAIL, '登录失败', null);
                    }
                    return;
                } else {
                    // d登录信息什么的都是好的， 直接返回登录token
                    echo $util->buildReturnJson(Util::$CODE_SUCCESS, '登录成功', $login['token']);
                }

            } else {

                // 生成一条登陆数据

                $result = $sql->insertInto('login')->set([
                    'token' => $token,
                    'userId' => $userId,
                    'createAt' => $createAt,
                    'updateAt' => $updateAt,
                    'loginType' => $loginType,
                    'loginTypeName' => $loginTypeName,
                    'expireTime' => $expireTime
                ])->d0();
                if ($result) {
                    echo $util->buildReturnJson(Util::$CODE_SUCCESS, '登录成功', $token);
                } else {
                    echo $util->buildReturnJson(Util::$CODE_FAIL, '登陆失败,请重试', null);
                }
            }
            return;
        }
        echo $util->buildReturnJson2(503, null);
    });

    /**
     * 注册账号
     * 参数：name 用户名 必
     *      password 用户密码 必
     *      phone 手机号 可选
     *
     */
    $util->post('regist', function ($data) {
        global $util;

        try {
            $name = $util->nullThrow($data, 'name', '用户名不能为空');
            $password = $util->nullThrow($data, 'password', '密码不能为空');
            $createAt = time();
            $updateAt = $createAt;

        } catch (Exception $exception) {
            echo $util->buildReturnJson(500, $exception->getMessage(), null);
            return;
        }


        $realName = $data['realName'];
        $sex = $data['sex'];
        $phone = $data['phone'];
        $signature = $data['signature'];
        $qq = $data['qq'];
        $city = $data['city'];
        $area = $data['area'];
        $province = $data['province'];
        $country = $data['country'];


        $password = md5(md5($password) . $password); // 对密码进行二次md5加密
        $sql = new SQL($util->db(null));

        // 先查询用户名是否存在，如果存在，不注册，给出提示。
        $result = $sql->select('count(*)')->from('user')->where('name', '=', $name)->d0();
        if ($result instanceof mysqli_result) {
            $r = $result->fetch_array(MYSQLI_NUM);
            if ($r[0] > 0) {
                echo $util->buildReturnJson2(504, $r[0]);
                return;
            }
        }

        $result = $sql->insertInto('user')->keys([
            'name', 'password', 'realName', 'sex', 'phone', 'signature', 'qq',
            'city', 'area', 'province', 'country', 'createAt', 'updateAt'
        ])->values([
            $name, $password, $realName, 'num' => $sex, $phone, $signature, $qq,
            $city, $area, $province, $country, $createAt, $updateAt
        ])->d0();

        if ($result) {
            echo $util->buildReturnJson(Util::$CODE_SUCCESS, '注册成功', $result);
        } else {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '注册失败' . $sql->getSql(), mysqli_error($sql->getConn()));
        }
    });

    $util->start();
} catch (Throwable $exception) {
    echo '{"code": 500, "msg": "服务器错误[' . addslashes($exception->getMessage()) . ']", "data": ""}';
}
