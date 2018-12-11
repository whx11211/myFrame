<?php

// 数据库执行语句是否记录
define('DB_DEBUG', 1);

//错误DEGUE，=1表示直接显示错误，=0表示使用自定义错误处理
define('ERROR_DEBUG', 1);

//报错级别设置
define('ERROR_LEVEL', E_ALL);

require_once __DIR__.'/../common/init.php';

$video = Instance::getVideo('video');

$video_exts = [
    'avi','rmvb','rm','asf','divx','mpg','mpeg','mpe','wmv','mp4','mkv','vob','flv'
];

$type = $argv[1];
switch ($type) {
    case 'check_ext':
        check_ext();
        break;
    case 'add':
        add(VIDEO_BASE_PATH);
        break;
}

function check_ext() {
    global $video, $video_exts;

    $all_video = $video->select('file_name')->getAll();
    $arr = [];
    foreach ($all_video as $v) {
        $f = $v['file_name'];
        if (!in_array(substr($f, strrpos($f,'.')+1), $video_exts)) {
            $arr[] = substr($f, strrpos($f,'.'));
        }
    }
    if (!$arr) {
        echo 'OK';
    }
    else {
        echo implode("\r\n", $arr);
    }
}



function add($base_dir) {
    @$dir=opendir($base_dir);

    if ($dir !== false) {
        while($f=readdir($dir)) {
            if ($f == '.' || $f=='..') {
                continue;
            }
            $path = $base_dir . '\\' . $f;

            if (is_dir($path)) {
                add($path);
            }
            else {
                global $video, $video_exts;
                $path_info = pathinfo($path);
                if (in_array($path_info['extension'] ,$video_exts)) {
                    $data = FFMpeg::getVideoDetail($path);
                    $video->insert($data, 2);
                }
            }
        }
        closedir($dir);
    }
    else {
        LogFile::addLog('无法打开文件夹', $base_dir, 'ffmpeg');
    }
}
