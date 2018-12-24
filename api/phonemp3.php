<?php
/**
 * Created by IntelliJ IDEA.
 * User: Microanswer
 * Date: 2017/9/1
 * Time: 16:34
 */
require_once __DIR__ . '/../user/Util.php';

use user\Util;
use user\SQL;


try {
    header('Content-Type: application/json');
    $util = new Util();

    /**
     * 测试方法
     * @param $data 用户提交的json数据
     */
    $util->post("test", function ($data) {
        global $util;
        echo $util->buildReturnJson(Util::$CODE_SUCCESS, '调用成功', "Hello Microanswer. web: http://microanswer.cn");
    });

    /**
     * 保存一张封面
     * @param $data
     */
    $util->post("saveCover", function ($data) {
        global $util;

        if (!isset($data) || !isset($data['url'])) {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '参数错误', null);
            return;
        }

        // 产生一个id
        $guid = $util->guid();

        // 获取url
        $url = $data['url'];

        $createAt = $updateAt = intval(time());

        // 构建sql语句进行保存
        $sql = new SQL($util->db("utf8"));

        $res = $sql->insertInto("cover")->set([
            "id" => $guid,
            "url" => $url,
            "updateAt" => $updateAt,
            "createAt" => $createAt
        ])->d0();

        if ($res) {
            echo $util->buildReturnJson(Util::$CODE_SUCCESS, '保存成功', $res);
        } else {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '保存失败:'.mysqli_error($sql->getConn()), $res);
        }
    });

    /**
     * 获取最近封面
     * @param $data
     */
    $util->post("getCover", function ($data) {
        global $util;

        $sql = new SQL($util->db('utf8'));
        $res = $sql->select("*")->from("cover")->where("updateAt", "=", "(SELECT max(updateAt) FROM cover)")->d0();
        if ($res instanceof mysqli_result && $res->num_rows > 0) {
            echo $util->buildReturnJson(Util::$CODE_SUCCESS, '获取成功', $res->fetch_all(MYSQLI_ASSOC));
        } else {
            echo $util->buildReturnJson(Util::$CODE_FAIL, "获取失败", $sql->getSql());
        }
    });

    /**
     * 获取所有的封面
     * @param $data
     */
    $util->post("getCovers", function ($data) {
        global $util;

        if (isset($data)) {
            $count = $data['count'];
            if ($util->isStringEmpty($count)) {
                $count = 20; // 默认条数
            }

            $offset = $data['offset'];
            if ($util->isStringEmpty($offset)) {
                $offset = 0; // 默认偏移量
            }
        } else {
            $count = 20;
            $offset = 0;
        }
        $sql = new SQL($util->db('utf-8'));
        $result = $sql -> select("*") -> from("cover")->limit($offset, $count)->d0();
        if ($result instanceof mysqli_result) {
            echo $util->buildReturnJson(Util::$CODE_SUCCESS, '获取成功', $result->fetch_all(MYSQLI_ASSOC));
        } else {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '获取失败 ['.mysqli_errno($sql->getConn()) . mysqli_error($sql->getConn()), '使用的查询语句：' . $sql->getSql());
        }
    });

    /**
     * 下载最近的封面 - 已经废弃了
     * @param $data
     */
    $util->post("getCoverPic", function ($data)
    {
        http_response_code(404);
        echo "{\"msg\":\"获取失败\",\"code\":404,\"data\":\"\"}";
    });

    /**
     * 保存一个新的phonemp3的apk
     * @param $data
     */
    $util->post("saveApk",function ($data) {
        global $util;
        if (!isset($data) || !isset($data['url'])) {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '保存失败', null);
            return;
        }

        $link = $data['url'];

        if (!isset($data['size'])) {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '请指定安装包大小', null);
            return;
        }
        $size = $data['size'];

        if (!isset($data['name'])) {
            $name = '-';
        } else {
            $name = $data['name'];
        }

        if (!isset($data['version'])) {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '请指定安装包版本', null);
            return;
        }

        $version = $data['version'];

        if (!isset($data['newfunction'])) {
            $newfunction = '-';
        } else {
            $newfunction = $data['newfunction'];
        }

        $createdat = $updateat = time();

        if (!isset($data['mustDownload'])) {
            $mustDownload = 0;
        } else {
            $mustDownload = $data['mustDownload'];
        }

        $id = $util->guid();

        $sql = new SQL($util->db('utf8'));

        $result = $sql->insertInto("apk")->set([
            "id" => $id,
            "version" => $version,
            "name" => $name,
            "newfunction" => $newfunction,
            "link" => $link,
            "updateat" => $updateat,
            "createdat" => $createdat,
            "size" => $size,
            "mustDownload" => $mustDownload
        ]) -> d0();

        if ($result) {
            echo $util->buildReturnJson(Util::$CODE_SUCCESS, '保存成功', mysqli_error($sql->getConn()));
        } else {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '保存失败', mysqli_error($sql->getConn()));
        }

    });

    /**
     * 获取最新版的phonemp3安装包
     * @param $data
     */
    $util->post("getApk", function ($data) {
        global $util;

        $charset = $data['charset'];
        if ($util->isStringEmpty($charset)) {
            $charset = "utf8";
        }
        $sql = new SQL($util->db($charset));

        $result = $sql->select("*")->from("apk") -> where("updateAt", "=", "(SELECT max(updateat) FROM apk)")->d0();


        if ($result instanceof mysqli_result && $result->num_rows > 0) {
            echo $util->buildReturnJson(Util::$CODE_SUCCESS, '获取成功', $result->fetch_array(MYSQLI_ASSOC));
        } else {
            echo $util->buildReturnJson(Util::$CODE_FAIL, "获取失败 ".mysqli_error($sql->getConn()), null);
        }
    });

    /**
     * 获取所有可以下载的phoneMP3的安装包
     * @param $data
     */
    $util->post("getApks", function ($data) {
        global $util;
        $count = 20;
        $offset = 0;
        if (isset($data)) {
            if (isset($data['count'])) {
                $count = $data['count'];
            }
            if (isset($data['offset'])) {
                $offset = $data['offset'];
            }
        }
        $sql = new SQL($util->db("utf8"));

        $result = $sql->select("*")->from("apk")->limit($offset, $count)->d0();

        $err = mysqli_error($sql->getConn());

        if ($result instanceof mysqli_result) {
            echo $util->buildReturnJson(Util::$CODE_SUCCESS, '获取成功', $result->fetch_all(MYSQLI_ASSOC));
        } else {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '查询失败, ' . $err, '执行的SQL=' . $sql->getSql());
        }

    });

    /**
     * 保存一条反馈记录
     * @param $data
     */
    $util->post("saveFeedBack", function ($data) {
        global $util;
        if (isset($data)) {
            if ($util->isStringEmpty($data['content'])) {
                echo $util->buildReturnJson(Util::$CODE_FAIL, '没有内容可保存', null);
            } else {
                $sql = new SQL($util->db("utf8"));
                $content = $data['content'];
                $contact = '-';
                if (!$util->isStringEmpty($data['contact'])) {
                    $contact = $data['contact'];
                }

                $version = '-';

                if (!$util->isStringEmpty($data['version'])) {
                    $version = $data['version'];
                }

                $id = $util->guid();

                $updateAt = $createAt = time();

                $result = $sql->insertInto("feedback") ->set([
                    "id" => $id,
                    "content" => $content,
                    "contact" => $contact,
                    "updateAt" => $updateAt,
                    "createAt" => $createAt,
                    "version" => $version
                ])->d0();

                if ($result) {
                    echo $util->buildReturnJson(Util::$CODE_SUCCESS, '提交成功', $id);
                } else {
                    echo $util->buildReturnJson(Util::$CODE_SUCCESS, '提交失败 '.mysqli_error($sql->getConn()), null);
                }
            }
        } else {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '没有信息可保存', null);
        }
    });

    /**
     * 获取反馈记录
     * @param $data
     */
    $util ->post("getFeedBacks", function ($data)
    {
        global $util;
        $count = 20;
        $offset = 0;
        if (isset($data)) {
            if (isset($data['count'])) {
                $count = $data['count'];
            }
            if (isset($data['offset'])) {
                $offset = $data['offset'];
            }
        }
        $charset = $data['charset'];
        if ($util->isStringEmpty($charset)) {
            $charset = "utf8";
        }
        $sql = new SQL($util->db($charset)); // 'SELECT * FROM feedback LIMIT ' . $offset . ',' . $count;
        $result = $sql->select("id,content,contact,version,FROM_UNIXTIME(createAt) createAt,FROM_UNIXTIME(updateAt) updateAt")->from("feedback") ->desc("updateAt") -> limit($offset, $count)->d0();

        if ($result instanceof mysqli_result) {
            echo $util->buildReturnJson(Util::$CODE_SUCCESS, '获取成功', $result->fetch_all(MYSQLI_ASSOC));
        } else {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '查询失败, ' . mysqli_error($sql->getConn()), '执行的SQL=' . $sql->getSql());
        }

    });

    /**
     * 获取歌手图片
     * 参数: artist: 歌手名字
     * @param $data
     */
    $util->post("getArtImg", function ($data) {
        global $util;
        $artist = $data['artist'];

        if ($util->isStringEmpty($artist)) {
            // 如果没有传歌手名称,返回404
            header('HTTP/1.1 404 Not Found');
            header('status: 404 Not Found');
            echo $util->buildReturnJson(Util::$CODE_FAIL, '没有传递artist', null);
        } else {

            // 请求LastFm接口获取图片信息
            $str = file_get_contents('http://ws.audioscrobbler.com/2.0/?method=artist.getInfo&format=json&api_key=4e70ec9de65366b940679c6fd935bce9&artist=' . $artist);

            if ($util->isStringEmpty($str)) {
                // 没有查询到数据,返回404
                header('HTTP/1.1 404 Not Found');
                header('status: 404 Not Found');
                echo $util->buildReturnJson(Util::$CODE_FAIL, '没有结果', null);
            } else {
                $images = json_decode($str)->artist->image;
                $imgurl = $images[count($images) - 1]->{'#text'};
                $te = explode('.', $imgurl);
                $picType = $te[count($te) - 1];
                if (strtolower($picType) === 'png') {
                    $img = imagecreatefrompng($imgurl);
                } else if (strtolower($picType) === 'jpg') {
                    $img = imagecreatefromjpeg($imgurl);
                }
                header('Content-Type: image/' . $picType);
                imagejpeg($img);
                imagedestroy($img);
            }
        }
    });

    /**
     * 保存一条留言。
     * @param $data
     */
    $util->post("saveMsg", function ($data) {
        global $util;
        try {

            $id = $util->guid();
            $content = $data['content'];
            $updateAt = $createAt = time();

            $charset = $data['charset'];
            if ($util->isStringEmpty($charset)) {
                $charset = "utf8";
            }
            $sql = new SQL($util->db($charset)); // 'INSERT INTO msg (id, content, updateAt, createAt) VALUES ("' . $id . '","' . $content . '","' . $updateAt . '","' . $createAt . '")';
            $result = $sql->insertInto("msg")->set([
                "id" => $id,
                "content" => $content,
                "updateAt" => $updateAt,
                "createAt" => $createAt
            ])->d0();

            if ($result) {
                echo $util->buildReturnJson(Util::$CODE_SUCCESS, '提交成功', $id);
            } else {
                echo $util->buildReturnJson(Util::$CODE_FAIL, '提交失败 '.mysqli_error($sql->getConn()), null);
            }
        } catch (Exception $exception) {
            echo $util->buildReturnJson(500, "error".$exception->getMessage(), null);
        }
    });

    /**
     * 获取留言，支持分页
     * @param $data
     */
    $util->post("getMsg", function ($data) {
        global $util;

        $offset = $data['offset'];
        if ($util->isStringEmpty($offset)) {
            $offset = 0;
        }
        $limit = $data['limit'];
        $charset = $data['charset'];
        if ($util->isStringEmpty($charset)) {
            $charset = "utf8";
        }
        if ($util->isStringEmpty($limit)) {
            $limit = 20;
        }
        $sql = new SQL($util->db($charset));

        $result = $sql->select("id,content,FROM_UNIXTIME(createAt) createAt, FROM_UNIXTIME(updateAt) updateAt")->from("msg")->desc("updateAt")->limit($offset, $limit)->d0(); // mysql_query('SELECT * FROM msg LIMIT ' . $offset . ',' . $limit);
        if ($result instanceof mysqli_result) {
            echo $util->buildReturnJson(Util::$CODE_SUCCESS, '获取成功', $result->fetch_all(MYSQLI_ASSOC));
        } else {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '查询失败, ' . mysqli_error($sql->getConn()), null);
        }
    });

    /**
     * 添加一条软件打开记录
     * @param $data
     */
    $util->post("apkopenlog", function ($data) {
        global $util;

        $id = $util->guid();
        $createAt = $updateAt = time();
        $position = $data['position'];
        $version = $data['version'];
        $phoneinfo = $data['phoneinfo'];

        $sql = new SQL($util->db("utf8")); // 'INSERT INTO apkopenlog (id, createAt, updateAt, position, version, phoneinfo) VALUES ("' . $id . '","' . $createAt . '","' . $updateAt . '","' . $position . '","' . $version . '","' . $phoneinfo . '")';
        $result = $sql->insertInto("apkopenlog")->set([
            "id" => $id,
            "createAt" => $createAt,
            "updateAt" => $updateAt,
            "position" => $position,
            "version" => $version,
            "phoneinfo" => $phoneinfo
        ])->d0();

        if ($result) {
            echo $util->buildReturnJson(Util::$CODE_SUCCESS, '提交成功', $id);
        } else {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '提交失败'.mysqli_error($sql->getConn()), null);
        }
    });

    /**
     * 获取app打开日志
     * @param $data
     * @throws Exception
     */
    $util->post("getopenlog", function ($data)
    {
        global $util;
        $offset = $data['offset'];
        if ($util->isStringEmpty($offset)) {
            $offset = 0;
        }

        $limit = $data['limit'];
        if ($util->isStringEmpty($limit)) {
            $limit = 10;
        }
        $charset = $data['charset'];
        if ($util->isStringEmpty($charset)) {
            $charset = "utf8";
        }
        $sql = new SQL($util->db($charset));

        $ressult = $sql->select("id,version,position,FROM_UNIXTIME(createAt) createAt,FROM_UNIXTIME(updateAt) updateAt,phoneInfo")->from("apkopenlog")->desc("updateAt") ->limit($offset, $limit)->d0();
        if ($ressult instanceof mysqli_result) {
            $array = array_map(function ($value) {
                $value['phoneInfo'] = json_decode($value['phoneInfo']);
                return $value;
            }, $ressult->fetch_all(MYSQLI_ASSOC));
            echo $util->buildReturnJson(Util::$CODE_SUCCESS, '获取成功', $array);
        } else {
            echo $util->buildReturnJson(Util::$CODE_FAIL, '查询失败, ' . mysqli_error($sql->getConn()).$sql->getSql(), null);
        }
        mysqli_close($sql->getConn());
    });

    $util->start();

}catch (Exception $exception) {
    echo '{"code": 500, "msg": "服务器错误[' . addslashes($exception->getMessage()) . ']", "data": ""}';
}