<?php

// ���ݿ�ִ������Ƿ��¼
define('DB_DEBUG', 1);

//����DEGUE��=1��ʾֱ����ʾ����=0��ʾʹ���Զ��������
define('ERROR_DEBUG', 1);

//����������
define('ERROR_LEVEL', E_ALL);

require_once __DIR__.'/../common/init.php';


define('VIDEO_BASE_PATH', 'H:\Progrom\Common Files\Videos');
define('FFMPEG_PATH', 'D:\���\ffmpeg-20181208-6b1c4ce-win64-static\bin\\');
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
        LogFile::addLog('�޷����ļ���', [$base_dir, json_encode($e)], 'ffmpeg');
    }
    finally {
        LogFile::addLog('�޷����ļ���', [$base_dir], 'ffmpeg');
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
        LogFile::addLog('�޷�ʶ����Ƶ', $file, 'ffmpeg');
    }

    if (!$data) {
        return false;
    }

    $data['file_name'] = iconv('GBK', 'UTF-8', $data['file_name']);
    $data['path'] = iconv('GBK', 'UTF-8', $data['path']);


    return $video->insert($data, 2);
}

each_dir(VIDEO_BASE_PATH);