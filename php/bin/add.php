<?php

require_once __DIR__.'/../common/init.php';


$video = Instance::getVideo('video');
$video_exts = [
    'avi','rmvb','rm','asf','divx','mpg','mpeg','mpe','wmv','mp4','mkv','vob','flv'
];

function each_dir($base_dir) {
    if ($dir=opendir($base_dir)) {
        while($f=readdir($dir)) {
            if ($f == '.' || $f=='..') {
                continue;
            }
            if (is_dir($base_dir . '/' . $f)) {
                each_dir($base_dir . '/' . $f);
            }
            else {
                handle_file($base_dir . '/' . $f);
            }
        }
    }
}

function handle_file($file) {
    global $video, $video_exts;
    $path_info = pathinfo($file);

    $data = FFMpeg::getVideoDetail($file);

    if (!$data && in_array($path_info['extension'] ,$video_exts)) {
        LogFile::addLog('无法识别视频', $file, LOG_PATH . 'ffmpeg.log');
    }

    if (!$data) {
        return false;
    }

    return $video->insert($data, 2);
}

each_dir(VIDEO_BASE_PATH);