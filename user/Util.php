<?php

namespace user;

use mysqli_result;

class Util
{
    public static $CODE_FAIL = 500;
    public static $CODE_SUCCESS = 200;
    public static $STATUS = [
        '200' => "成功",
        '500' => "服务器错误，请稍后再试。",
        '501' => "您还未登录，请先登录。",
        '502' => "登录信息已过期，请重新登录。",
        '503' => "用户名或密码错误。",
        '504' => "用户名已存在",
        '600' => "像素鸟成绩排行榜为空。",
        '601' => "成绩不可以小于 0 。",
        '602' => "成绩保存失败，请稍候再试。",
        '603' => "上传成绩必须包含游戏版本号。",
        '604' => "还没有上传过成绩。",
        '700' => "浏览器信息为空",
        '701' => "获取赞赏总数失败"
    ];

    public function __construct()
    {
        $this->pathFun = array();
    }

    private $pathFun;

    /**
     * 响应所有请求
     * @param $method [String] 访问路劲
     * @param $fun [Function]执行逻辑
     */
    public function post($method, $fun)
    {
        $this->pathFun[$method] = $fun;
    }

    /**
     * 生成一个随机uuid
     * @return string
     */
    public function guid()
    {
        if (function_exists('com_create_guid')) {
            $t = com_create_guid();
            $t = strtolower(str_replace('-', '', $t));
            $t = str_replace('{', '', $t);
            $t = str_replace('}', '', $t);
            return $t;
        }

        mt_srand((double)microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $uuid = substr($charid, 0, 8)
            . substr($charid, 8, 4)
            . substr($charid, 12, 4)
            . substr($charid, 16, 4)
            . substr($charid, 20, 12);
        return $uuid;
    }

    /**
     * h获取数据，如果没有值就抛出错误
     * @param $data
     * @param $key
     * @param $msg
     * @return mixed
     * @throws \Exception
     */
    public function nullThrow($data, $key, $msg)
    {
        $e = array_key_exists($key, $data);

        if (!$e) {
            throw new \Exception($msg);
        }

        $value = $data[$key];
        if ($this->isStringEmpty($value)) {
            throw new \Exception($msg);
        }
        return $data[$key];
    }

    /**
     * 判断某字符串是否为空
     * @param $str
     * @return bool
     */
    public function isStringEmpty($str)
    {
        if (isset($str)) {
            if (empty($str)) {
                return true;
            } else {
                if (strlen($str) == 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return true;
        }
    }


    /**
     * 判断某字符串是否为空static版
     * @param $str
     * @return bool
     */
    public static function staticisStringEmpty($str)
    {
        if (isset($str)) {
            if (empty($str)) {
                return true;
            } else {
                if (strlen($str) == 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return true;
        }
    }

    /**
     * 执行一条sql语句
     * @param $sql
     * @return bool|\mysqli_result
     */
    public function query($sql)
    {
        return mysqli_query($this->db(), $sql);
    }

    /**
     * 获取数据库连接
     * @return \mysqli
     */
    public function db($charset)
    {
        $conn = mysqli_connect('127.0.0.1', 'zjwdb_6203682', '', 'zjwdb_6203682', '3306');

        if (mysqli_connect_errno()) {
            // 数据库连接出问题
            echo $this->buildReturnJson(500, "[" . mysqli_connect_errno() . ']' . mysqli_connect_error(), null);
            exit(0);
        }

        if ($this->isStringEmpty($charset)) {
            $conn->set_charset('utf8');
        } else {
            $conn->set_charset($charset);
        }
        return $conn;
    }

    public function buildReturnJson2($code, $obj)
    {
        return $this->buildReturnJson($code, Util::$STATUS['' . $code . ''], $obj);
    }

    /**
     * 获取客户的ip
     */
    public function getClientIp()
    {
        $user_IP = '';
        if (key_exists('HTTP_VIA', $_SERVER)) {
            if (key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
                $user_IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (key_exists('REMOTE_ADDR', $_SERVER)){
                $user_IP = $_SERVER['REMOTE_ADDR'];
            }
        } else  if (key_exists('REMOTE_ADDR', $_SERVER)){
            $user_IP = $_SERVER['REMOTE_ADDR'];
        }
        return $user_IP;
    }

    /**
     * 判断字符串是否以flag开始
     * @param $str
     * @param $flag
     */
    public static function strStartWith($str, $flag)
    {
        if (Util::staticisStringEmpty($str)) {
            return false;
        } else {
            if ($flag === "") {
                return true;
            }
            return strpos($str, $flag) === 0;
        }
    }

    /**
     * 判断字符串是否以flag结尾
     * @param $strm
     * @param $flag
     */
    public static function strEndWith($str, $flag)
    {
        if (Util::staticisStringEmpty($str)) {
            return false;
        } else {
            if ($flag === "") {
                return true;
            }
            return substr($str, 0 - strlen($flag)) === $flag;
        }
    }

    public function checkToken($token, $callback)
    {
        // 查询token是否过期
        $sql = new SQL($this->db(null));
        $logins = $sql->select('token,userId,createAt, updateAt,loginType,loginTypeName,expireTime')
            ->from('login')->where('token', '=', $token)->d0();

        if ($logins instanceof mysqli_result && $logins->num_rows > 0) {
            $login = $logins->fetch_array(MYSQLI_ASSOC);

            // 计算是否登陆信息有效
            $update = intval($login['updateAt']);
            $expireTime = intval($login['expireTime']);

            $expire = $update + $expireTime;

            if (time() >= $expire) {
                // 当前时间已经超过了过期时间
                // echo $util->buildReturnJson(502, Util::$STATUS['502'], null);
                $callback(502, -1);
                return;
            } else {
                // d登录信息什么的都是好的
                $callback(200, $login['userId']);
            }
        } else {
            // 没有登陆信息
            $callback(501, -1);
        }
    }

    /**
     * 构建返回给客户端的数据
     * @param $code
     * @param $msg
     * @param $obj
     * @return string
     */
    public function buildReturnJson($code, $msg, $obj)
    {
        if (isset($obj)) {
            $obj = json_encode($obj);
            if ($this->isStringEmpty($obj)) {
                $obj = '""';
            }
        } else {
            $obj = '""';
        }
        return '{"code": ' . $code . ', "msg": "' . $msg . '", "data":' . $obj . '}';
    }

    /**
     * 开始执行所有设定的path和方法
     */
    public function start()
    {
        $ERRMSG = 'something was wrong.';
        try {
            $REQUEST_METHOD = strtolower($_SERVER['REQUEST_METHOD']);
            // 接口供post支持。
            if ($REQUEST_METHOD === 'post') {

                // 获取请求类型
                if (array_key_exists('HTTP_CONTENT_TYPE', $_SERVER)) {
                    $contentType = $_SERVER['HTTP_CONTENT_TYPE'];
                } else if (array_key_exists('CONTENT_TYPE', $_SERVER)) {
                    $contentType = $_SERVER['CONTENT_TYPE'];
                } else {
                    $contentType = '';
                }

                if ($this->isStringEmpty($contentType)) {
                    echo $this->buildReturnJson(500, '未指定ContentType', null);
                    return;
                }

                $contentType = strtolower($contentType);

                if (stripos($contentType, "application/json") > -1) {
                    // 获取post过来的数据
                    $jsonData = file_get_contents('php://input');

                    if ($this->isStringEmpty($jsonData)) {
                        echo $this->buildReturnJson(500, "empty data", null);
                        return;
                    }

                    // 解析json数据为数组
                    $jsonDataObj = json_decode($jsonData, true);

                    if (!is_array($jsonDataObj)) {
                        // 数据解析完了，发现不是数组，报错。
                        echo $this->buildReturnJson(500, "data format is not json", null);
                        return;
                    }

                    if (isset($jsonDataObj['method'])) {
                        try {
                            $method = $jsonDataObj['method'];

                            if ($this->isStringEmpty($method)) {
                                echo $this->buildReturnJson(500, "method is empty", null);
                                return;
                            }

                            if (array_key_exists('data', $jsonDataObj)) {
                                $data = $jsonDataObj['data'];
                            } else {
                                $data = [];
                            }

                            if (!array_key_exists($method, $this->pathFun)) {
                                echo $this->buildReturnJson(500, "method not found", null);
                                return;
                            }
                            try {
                                $this->pathFun[$method]($data);
                            } catch (\Throwable $throwable) {
                                echo $this->buildReturnJson(500, $throwable->getMessage(), null);
                                return;
                            }
                        } catch (\Exception $e) {
                            echo $this->buildReturnJson(Util::$CODE_FAIL, $e->getMessage(), null);
                        }
                    } else {
                        echo $this->buildReturnJson(Util::$CODE_FAIL, 'no method found', null);
                    }
                } else if (stripos($contentType, "multipart/form-data") > -1) {
                    // 文件上传过来的请求

                    // 获取方法名称
                    if (isset($_POST['method'])) {
                        try {
                            $method = $_POST['method'];
                            $this->pathFun[$method]();
                        } catch (Throwable $e) {
                            echo $this->buildReturnJson(Util::$CODE_FAIL, $e->getMessage(), null);
                        }
                    } else {
                        echo $this->buildReturnJson(Util::$CODE_FAIL, "没有指定要执行的方法", null);
                    }

                } else {
                    echo $this->buildReturnJson(Util::$CODE_FAIL, "不支持的ContentType类型", $_SERVER);
                }

            } else {
                echo $this->buildReturnJson(Util::$CODE_FAIL, $ERRMSG, null);
            }
        } catch (\Throwable $e) {
            echo $this->buildReturnJson(Util::$CODE_FAIL, $e->getMessage(), null);
        }
    }

}

class SQL
{
    private $sql;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function select($col)
    {
        $this->sql = 'select ' . $col . ' ';
        return $this;
    }

    public function from($table)
    {
        $this->sql = $this->sql . 'from ' . $table . ' ';
        return $this;
    }

    public function where($key, $op, $value)
    {
        if (Util::strStartWith($value, "(") && Util::strEndWith($value, ")")) {
            $this->sql = $this->sql . 'where ' . $key . $op . mysqli_escape_string($this->conn, $value);
        } else {
            $this->sql = $this->sql . 'where ' . $key . $op . '\'' . mysqli_escape_string($this->conn, $value) . '\' ';
        }
        return $this;
    }

    public function and_($key, $op, $value)
    {
        $this->sql = $this->sql . 'and ' . $key . $op . '\'' . mysqli_escape_string($this->conn, $value) . '\' ';
        return $this;
    }

    public function limit($offset, $limit)
    {
        $this->sql = $this->sql . 'limit ' . $offset . ', ' . $limit . ' ';
        return $this;
    }

    public function asc($key)
    {
        $this->sql = $this->sql . 'order by ' . $key . ' asc ';
        return $this;
    }

    public function desc($key)
    {
        $this->sql = $this->sql . 'order by ' . $key . ' desc ';
        return $this;
    }

    public function insertInto($table)
    {
        $this->sql = 'insert into ' . $table . ' ';
        return $this;
    }

    public function keys($keys)
    {
        $sql_ = '(';
        foreach ($keys as $k) {
            $sql_ = $sql_ . $k . ',';
        }
        $sql_ = substr($sql_, 0, strlen($sql_) - 1);
        $this->sql = $this->sql . $sql_ . ') ';
        return $this;
    }

    public function values($values)
    {
        $sql_ = 'values (';
        foreach ($values as $k => $v) {
            if (is_null($v)) {
                if ($k === 'num') {
                    $sql_ = $sql_ . '0,';
                } else {
                    $sql_ = $sql_ . 'null,';
                }
            } else if ($k !== 'num') {
                $sql_ = $sql_ . '\'' . mysqli_escape_string($this->conn, $v) . '\',';
            } else {
                $sql_ = $sql_ . mysqli_escape_string($this->conn, $v) . ',';
            }
        }
        $sql_ = substr($sql_, 0, strlen($sql_) - 1);
        $this->sql = $this->sql . $sql_ . ')';
        return $this;
    }

    public function update($table)
    {
        $this->sql = 'update ' . $table . ' ';
        return $this;
    }

    public function set($keyValueArray)
    {
        $sql_ = '';
        foreach ($keyValueArray as $k => $v) {
            $sql_ = $sql_ . $k . '=\'' . mysqli_escape_string($this->conn, $v) . '\',';
        }
        $sql_ = substr($sql_, 0, strlen($sql_) - 1);
        $this->sql = $this->sql . 'set ' . $sql_ . ' ';
        return $this;
    }

    public function d0()
    {
        return mysqli_query($this->conn, $this->sql);
    }

    /**
     * @return mixed
     */
    public function getConn()
    {
        return $this->conn;
    }

    public function getSql()
    {
        return $this->sql;
    }
}