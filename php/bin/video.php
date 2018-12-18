<?php

// 数据库执行语句是否记录
define('DB_DEBUG', 1);

//错误DEGUE，=1表示直接显示错误，=0表示使用自定义错误处理
define('ERROR_DEBUG', 1);

//报错级别设置
define('ERROR_LEVEL', E_ALL);

require_once __DIR__.'/../common/init.php';

$video_exts = [
    'avi','rmvb','rm','asf','divx','mpg','mpeg','mpe','wmv','mp4','mkv','vob','flv','mov','tp','m4a','m4v','mpg','mts','vob','td'
];

$type = $argv[1];
switch ($type) {
    case 'check_ext':
        $video = Instance::getVideo('video');
        check_ext();
        break;
    case 'add':
        $tag = Instance::getVideo('tag');
        $tags = $tag->getAll() ?: [];
        if ($tags) {
            $tags = array_column($tags, 'tag_id', 'tag_name');
        }
        $video = Instance::getVideo('video');
        $videos_index = $video->select('file_index')->getAll();
        if ($videos_index) {
            $videos_index = array_column($videos_index, 'file_index');
        }
        add(VIDEO_BASE_PATH);
        break;
    case 'add_tag':
        $tag = Instance::getVideo('tag');
        $tag_names = $tag->select('tag_name')->getAll() ?: [];
        if ($tag_names) {
            $tag_names = array_column($tag_names, 'tag_name');
        }
        add_tag(VIDEO_BASE_PATH);
        break;
    default:
        echo 'param error!';
        break;
}

function check_ext() {
    global $video, $video_exts;

    $all_video = $video->select('file_name')->getAll();
    $arr = [];
    foreach ($all_video as $v) {
        $f = $v['file_name'];
        if (!in_array(strtolower(substr($f, strrpos($f,'.')+1)), $video_exts)) {
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
                add_video($path);
            }
        }
        closedir($dir);
    }
    else {
        LogFile::addLog('无法打开文件夹', $base_dir, 'video');
    }
}


function add_video($path) {
    global $video, $video_exts,$videos_index,$tags,$tag;
    $index = md5(dirname($path)).md5(substr($path, strlen(dirname($path))+1));
    if (in_array($index, $videos_index)) {
        return true;
    }
    $path_info = pathinfo($path);
    if (in_array(strtolower($path_info['extension']) ,$video_exts)) {
        $data = FFMpeg::getVideoDetail($path);
        if ($data) {
            $video_path_detail = explode('\\', $path);
            $video_tags = [];
            foreach ($video_path_detail as $tag_name) {
                if (isset($tags[$tag_name])) {
                    $video_tags[] = $tags[$tag_name];
                    $tag->execSql('update tag set video_count=video_count+1 where tag_id=?', array($tags[$tag_name]));
                }
            }
            $data['tags'] = implode(',', $video_tags);
            $video->insert($data, 2);
        }
        else {
            LogFile::addLog('无法识别视频', $path, 'video');
        }
    }
    return true;
}


function add_tag($base_dir) {
    global $tag_names, $tag;

    @$dir=opendir($base_dir);

    if ($dir !== false) {
        while($f=readdir($dir)) {
            if ($f == '.' || $f=='..') {
                continue;
            }
            $path = $base_dir . '\\' . $f;

            if (is_dir($path)) {
                if (strlen($f) < 24 && !in_array($f, $tag_names)) {
                    $data = [
                        'tag_name'  =>  $f,
                        'create_time'=>date('Y-m-d H:i:s')
                    ];
                    $tag->insert($data, 2);
                }
                add_tag($path);
            }
        }
        closedir($dir);
    }
    else {
        LogFile::addLog('无法打开文件夹', $base_dir, 'video');
    }
}