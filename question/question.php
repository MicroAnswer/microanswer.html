<?php

namespace question;

require_once '../user/Util.php';

use mysqli_result;
use user\Util;
use user\SQL;

class Question
{

    private $util;
    private $sql;

    public function __construct()
    {
        $this->util = new Util();
        $this->sql = new SQL($this->util->db("UTF-8"));
    }


    /**
     * 创建一个新问题。
     * @param $title 标题
     * @param $content 内容
     * @param $userId 用户id
     * @param $memo 备注 可为空
     * @param $keys 标签 可为空
     * @param $permission 权限 0-公共， 1-私人
     */
    public function createQuestion ($title, $content, $userId, $memo, $keys, $permission) {

        $id = $this->util->guid();
        $value = [
            'id' => $id,
            'title' => $title,
            'content' => $content,
            'userId' => $userId,
            'status' => 1,
            'answerStatus' => 0,
            'createAt' => time(),
            'memo' => $memo,
            'keys' => $keys,
            'permission' => $permission
        ];

        $result = $this->sql->insertInto("question")
            ->set($value)->d0();

        if ($result) {
            return $value;
        } else {
            return null;
        }
    }

}