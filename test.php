<?php
/**
 * Created by IntelliJ IDEA.
 * User: Microanswer
 * Date: 2018/3/28
 * Time: 11:01
 */

error_reporting(E_ALL & ~E_NOTICE);

$data = [
    'name' => 'Microanswer',
    'age' => 24
];

echo $data['height'] ?: "1654456";

