<?php
/**
 * Created by PhpStorm.
 * User: erhuo
 * Date: 2017/8/14
 * Time: 21:03
 */
echo "\r\n=========================start time".date('Y-m-d H:i:s')."==============================\r\n";
error_reporting(7);
$option['savepath'] = dirname(__FILE__).DIRECTORY_SEPARATOR;
$path = ['/www/web/xishui/public_html/'];
$except = ['js','css','jpg','jpeg','zip','png','gif','html','htm','svg','_addons_'];
checkFiles($path,$except,$option);



function checkFiles($paths,$except,$option){
    $print_tips = true;
    $files = [];
    $scandat = $option['savepath'].'scan.dat';
    if(file_exists($scandat)) {
        $pre_data = unserialize(file_get_contents($option['savepath'] . 'scan.dat'));
    }else{
        $pre_data = [];
    }
    foreach ($paths as $path){
        $files = array_merge($files,scanpath($path,$except));
        if($print_tips){
            output("end scan.".count($files)." files.\r\n");
        }
    }
    foreach ($files as $file){
        $temp = md5_file($file);
        if($temp)
            $data[$file] = $temp;
        if($print_tips && count($data)%1000==0){
            output(count($data)." files has computed hash. end file is ".$file."\r\n");
        }
    }
    if($print_tips){
        output("\r\n end md5files.".count($data)." files.\r\n");
    }
    foreach($data as $key=>$value){
        if(empty($pre_data[$key])){
            output(date("Y-m-d H:i:s",time())."create:".$key."\r\n");
            continue;
        }elseif($value!=$pre_data[$key]){
            output(date("Y-m-d H:i:s",time())."modify:".$key."\r\n");
        }
    }
    foreach($pre_data as $key=>$value){
        if(empty($data[$key])){
            output(date("Y-m-d H:i:s",time())."deleteed:".$key."\r\n");
        }
    }
    fwrite ( STDOUT , 'save the hash data：y/n' . PHP_EOL );
    $input = trim(fgets(STDIN));
    if($input=='y') {
        file_put_contents($scandat, serialize($data));
        output("file hash saved.\r\n");
    }else{
        output("file hash file not save\r\n");
    }
    output('',$option['savepath'].date('YYYY-MM-dd.log',time()));
}

function output($str,$filename=null){
    static $ret;
    echo $str;
    $ret.=$ret;
    if ($filename){
        file_put_contents($filename,$ret);
    }
}

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
                if (!in_array($path . $v, $except)&&!in_array(strtolower(end(explode('.',$v))), $except))
                    $data[] = $path . $v;
            }
        }
    }
    return $data;
}

