<?php

// 数据库执行语句是否记录
define('DB_DEBUG', 1);

//错误DEGUE，=1表示直接显示错误，=0表示使用自定义错误处理
define('ERROR_DEBUG', 1);

//报错级别设置
define('ERROR_LEVEL', E_ALL);

require_once __DIR__.'/../common/init.php';


define('VIDEO_BASE_PATH', 'H:\Progrom\Common Files\Videos');
define('FFMPEG_PATH', 'D:\软件\ffmpeg-20181208-6b1c4ce-win64-static\bin\\');
define('FFMPEG_IMAGE_PATH', dirname(ROOT).'/views/images/ffmpeg/');

$video = Instance::getVideo('video');
$video_exts = [
    'avi','rmvb','rm','asf','divx','mpg','mpeg','mpe','wmv','mp4','mkv','vob','flv'
];

function each_dir($base_dir) {
    try {
        $dir=opendir($base_dir);
    }
    catch (Exception  $e) {
        LogFile::addLog('无法打开文件夹', [$base_dir, json_encode($e)], 'ffmpeg');
    }
    finally {
        LogFile::addLog('无法打开文件夹', [$base_dir], 'ffmpeg');
    }
    echo $base_dir;
    if ($dir !== false) {
        while($f=readdir($dir)) {
            if ($f == '.' || $f=='..') {
                continue;
            }
            $path = $base_dir . '\\' . $f;

            if (is_dir($path)) {
                each_dir($path);
            }
            else {
                handle_file($path);
            }
        }
        closedir($dir);
    }
}

function handle_file($file) {
    global $video, $video_exts;
    $path_info = pathinfo($file);

    $data = FFMpeg::getVideoDetail($file);

    if (!$data && in_array($path_info['extension'] ,$video_exts)) {
        LogFile::addLog('无法识别视频', $file, 'ffmpeg');
    }

    if (!$data) {
        return false;
    }

    $data['file_name'] = iconv('GBK', 'UTF-8', $data['file_name']);
    $data['path'] = iconv('GBK', 'UTF-8', $data['path']);


    return $video->insert($data, 2);
}

each_dir(VIDEO_BASE_PATH);