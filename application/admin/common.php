<?php

/**
 * 生成随机字符串
 * @param  integer $num 生成字符串的长度
 * @return string       返回生成的随机字符串
 */
function get_random_str($num = 8)
{
    $pattern = 'AaZzBb0YyCc9XxDd8Ww7EeVvF6fUuG5gTtHhS4sIiRr3JjQqKkP2pLlO1oMmNn';
    $str     = '';
    for ($i = 0; $i < $num; $i++) {
        $str .= $pattern{mt_rand(0, 35)}; //生成 php 随机数
    }
    return $str;
}

/**
 * 把用户状态转换成文字
 * @param  integer $status 用户状态值
 * @return string          返回用户状态
 */
function get_user_status($status = 0)
{
    $status_txt = '';
    switch ($status) {
        case 1:
            $status_txt = '正常';
            break;
        case 44:
            $status_txt = '禁用';
            break;
    }
    return $status_txt;
}

/**
 * 获取毫秒数
 */
function get_millisecond()
{
    list($microsecond, $time) = explode(' ', microtime()); //' '中间是一个空格
    return (float) sprintf('%.0f', (floatval($microsecond) + floatval($time)) * 1000);
}

/**
 * 生成密码
 * @param  string  $str    明文密码
 * @param  string  $salt   密码盐
 * @param  integer $start  截取开始位置
 * @param  integer $length 截取长斋
 * @return string          返回md5加密并截取后的密码
 */
function get_password($str, $salt, $start = 3, $length = 25)
{
    return substr(md5($str . $salt), $start, $length);
}

/**
 * 获取客户端IP
 * @author 贺强
 * @time   2018-11-13 10:05:32
 * @return string 返回获取的IP
 */
function get_client_ip()
{
    // 判断服务器是否允许$_SERVER
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        //不允许就使用getenv获取
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    return $realip;
}

/**
 * 数组转 xml
 * @author 贺强
 * @time   2018-11-13 15:03:03
 * @param  array  $arr 被转换的数组
 * @return string      返回转换后的 xml 字符串
 */
function array2xml($arr)
{
    $xml = "<xml>";
    foreach ($arr as $key => $val) {
        $xml .= "<$key>$val</$key>";
    }
    $xml .= "</xml>";
    return $xml;
}

/**
 * xml 转换为数组
 * @author 贺强
 * @time   2018-11-13 15:12:04
 * @param  string $xml 被转换的 xml
 * @return array       返回转换后的数组
 */
function xml2array($xml)
{
    libxml_disable_entity_loader(true);
    $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $values;
}

/**
 * 格式化数字
 * @author 贺强
 * @time   2018-11-30 16:12:39
 * @param  integer $num 要格式化的数字
 * @return string       返回格式化后的字符串
 */
function format_number($num = 0)
{
    if ($num > 100000000) {
        $num /= 100000000;
        return round($num, 2) . '亿';
    } elseif ($num > 10000000) {
        $num /= 10000000;
        return round($num, 2) . '千万';
    } elseif ($num > 1000000) {
        $num /= 1000000;
        return round($num, 2) . '百万';
    } elseif ($num > 100000) {
        $num /= 100000;
        return round($num, 2) . '十万';
    } elseif ($num > 10000) {
        $num /= 10000;
        return round($num, 2) . '万';
    } else {
        return $num;
    }
}

/**
 * URL 请求
 * @author 贺强
 * @time   2018-10-30 12:13:06
 * @param  string  $url     请求地址
 * @param  string  $post    POST 数据
 * @param  string  $charset 编码方式，默认utf8
 * @return object           返回请求返回的数据
 */
function curl($url, $post = '', $charset = 'utf-8')
{
    $keypath  = '/www/wwwroot/wwwdragontangcom/cert/apiclient_key.pem';
    $certpath = '/www/wwwroot/wwwdragontangcom/cert/apiclient_cert.pem';
    $ch       = curl_init();
    //超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //设置header
    curl_setopt($ch, CURLOPT_HEADER, false);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //设置证书
    //使用证书：cert 与 key 分别属于两个.pem文件
    //默认格式为PEM，可以注释
    curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLCERT, $certpath);
    //默认格式为PEM，可以注释
    curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLKEY, $keypath);
    //post提交方式
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $data = curl_exec($ch);
    //返回结果
    if ($data) {
        curl_close($ch);
        return $data;
    } else {
        $error = curl_errno($ch);
        echo "curl出错，错误码:$error" . "<br>";
        curl_close($ch);
        return false;
    }
}

/**
 * 获取视频文件的缩略图
 * @author 贺强
 * @time   2019-01-16 09:47:14
 * @param  string  $file   视频文件路径
 * @param  integer $s      指定从什么时间开始截取，单位秒
 * @param  boolean $is_win 是否是 windows 系统
 * @return string          返回截取的图片保存路径
 */
function getVideoCover($file, $s = 11, $is_win = false)
{
    $ffmpeg = 'ffmpeg';
    $root   = ROOT_PATH . 'public';
    if ($is_win) {
        $ffmpeg = 'D:/ffmpeg/bin/ffmpeg.exe';
    }
    $path = '/uploads/cli/video/' . date('Y') . '/' . date('m') . '/' . date('d');
    if (!is_dir($root . $path)) {
        @mkdir($root . $path, 0755, true);
    }
    $path .= '/' . get_millisecond() . '.jpg';
    $strlen = strlen($file);
    $str    = "{$ffmpeg} -i {$file} -ss {$s} {$root}{$path}";
    system($str);
    return $path;
}
