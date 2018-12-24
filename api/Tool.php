<?php

/**
 * Created by IntelliJ IDEA.
 * User: Microanswer
 * Date: 2017/9/1
 * Time: 17:37
 */
class Tool
{
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
     * 成功的常量
     * @var int
     */
    public static $CODE_SUCCESS = 200;

    /**
     * 失败的常量
     * @var int
     */
    public static $CODE_FAIL = 500;

    /**
     * 构建返回给客户端的json字符串方法
     * @param $code String 错误码
     * @param $msg  String 信息
     * @param $obj String, Object 内容
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
     * 上传文件到指定地址
     * @param $filePath
     * @param $url
     * @return String url的响应
     */
    public function sendFile2Url($filePath, $contentType, $url)
    {

        // 发送POST请求上传文件到url
        $ch = curl_init();
        $curlPost = array(
            'file' => '@' . $filePath
        );
        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1); //POST提交
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}