<?php
/**
 * Created by PhpStorm.
 * User: erhuo
 * Date: 2017/8/14
 * Time: 21:03
 */
echo "\r\n=========================扫描时间".date('Y-m-d H:i:s')."==============================\r\n";
$savepath='/root/';
$path = '/var/www/zxwk/public_html/';
$pre_data = json_decode(file_get_contents($savepath.'scan.dat'),1);
$files = scanpath($path);
foreach ($files as $file){
    $data[$file] = md5_file($file);
}
foreach($data as $key=>$value){
    if(empty($pre_data[$key])){
        echo date("Y-m-d H:i:s",time())."创建文件:".$key."\r\n";
        continue;
    }elseif($value!=$pre_data[$key]){
        echo date("Y-m-d H:i:s",time())."修改文件:".$key."\r\n";
    }
}
foreach($pre_data as $key=>$value){
    if(empty($data[$key])){
        echo date("Y-m-d H:i:s",time())."删除文件:".$key."\r\n";
    }
}
file_put_contents($savepath.'scan.dat',json_encode($data));
function scanpath($path, $except = null, &$data = null)
{
    $except = is_array($except) ? $except : array($except);
    $temp = scandir($path);
    foreach ($temp as $v) {
        if ($v != '.' && $v != '..') {
            if (is_dir($path . $v)) {
                if (!in_array($path . $v . DIRECTORY_SEPARATOR, $except))
                    scanpath($path . $v . DIRECTORY_SEPARATOR, $except, $data);
            } else {
                if (!in_array($path . $v, $except))
                    $data[] = $path . $v;
            }
        }
    }
    return $data;
}
