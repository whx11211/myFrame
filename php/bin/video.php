<?php

// 数据库执行语句是否记录
define('DB_DEBUG', 1);

//错误DEGUE，=1表示直接显示错误，=0表示使用自定义错误处理
define('ERROR_DEBUG', 1);

//报错级别设置
define('ERROR_LEVEL', E_ALL);

//是否记录运行日志
define('RUNNING_LOG', true);

require_once __DIR__.'/../common/init.php';

$video_exts = [
    'avi','rmvb','rm','asf','divx','mpg','mpeg','mpe','wmv','mp4','mkv','vob','flv','mov','tp','m4a','m4v','mpg','mts','vob','td'
];

$type = $argv[1];
switch ($type) {
    case 'check_ext':
        $video = Instance::getMedia('video');
        check_ext();
        break;
    case 'add':
        $tag = Instance::getMedia('video_tag');
        $tags = $tag->getAll() ?: [];
        if ($tags) {
            $tags = array_column($tags, 'tag_id', 'path');
        }
        $video = Instance::getMedia('video');
        $videos_index = $video->select('file_index')->getAll();
        if ($videos_index) {
            $videos_index = array_column($videos_index, 'file_index');
        }
        $image = new Image(FFMPEG_IMAGE_PATH);
        add([VIDEO_BASE_PATH]);
        break;
    case 'update_tag':
        update_tag([VIDEO_BASE_PATH]);
        break;
    case 'compress_image':
        compress_image();
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
        echo implode(PHP_EOL, $arr);
    }
}



function add($paths) {
    while($base_dir = array_pop($paths)) {

        @$dir=opendir($base_dir);

        if ($dir !== false) {
            show_msg("open_dir", $base_dir);
            while($f=readdir($dir)) {
                if ($f == '.' || $f=='..') {
                    continue;
                }
                $path = $base_dir . DIRECTORY_SEPARATOR . $f;

                if (is_dir($path)) {
                    array_push($paths, $path);
                }
                else {
                    add_video($path);
                }
            }
            closedir($dir);
        }
        else {
            LogFile::addLog('open_dir_failed', $base_dir, 'video');
            show_msg("open_dir_failed ", $base_dir);
        }
    }
}


function add_video($path) {
    global $video, $video_exts,$videos_index,$tags,$tag,$image;
    $index = md5(dirname($path)).md5(substr($path, strlen(dirname($path))+1));
    if (in_array($index, $videos_index)) {
        return true;
    }
    $path_info = pathinfo($path);
    if (in_array(strtolower($path_info['extension']) ,$video_exts)) {
        $data = FFMpeg::getVideoDetail($path);
        if ($data) {
            if (!isset($tags[$data['path']]) && $data['path'] != VIDEO_BASE_PATH) {
                $path_detail = explode(DIRECTORY_SEPARATOR, substr($data['path'], strlen(VIDEO_BASE_PATH)+1));
                $new_tag_path = VIDEO_BASE_PATH;
                foreach ($path_detail as $new_tag_name) {
                    $new_tag_path .= DIRECTORY_SEPARATOR . $new_tag_name;
                    if (mb_strlen($new_tag_name) <= 32 && !isset($tags[$new_tag_path])) {
                        $tag_data = [
                            'tag_name'  =>  $new_tag_name,
                            'path'      =>  $new_tag_path,
                            'create_time'=>date('Y-m-d H:i:s')
                        ];
                        if (isset($tags[dirname($new_tag_path)])) {
                            $tag_data['parent_id'] = $tags[dirname($new_tag_path)];
                        }
                        else if (isset($tags[dirname(dirname($new_tag_path))])) {
                            $tag_data['parent_id'] = $tags[dirname(dirname($new_tag_path))];
                        }
                        $new_tag_id = $tag->insertByCondFromDb($tag_data, 2);
                        if ($new_tag_id) {
                            show_msg('add_tag ', $new_tag_name);
                            $tags[$new_tag_path] = $new_tag_id;
                        }
                    }
                }
            }

            $video_tags = [];
            foreach ($tags as $tag_path => $tag_id) {
                if (strpos($data['path'], $tag_path) === 0) {
                    $video_tags[] = $tag_id;
                }
            }
            $data['tags'] = implode(',', $video_tags);
            $video_id = $video->insertByCondFromDb($data, 2);
            $image->thumb($data['file_index']  . '.png', $video_id . '.png');
            System::delFile(FFMPEG_IMAGE_PATH . $data['file_index'] . '.png');
            show_msg("add_file ", $data['file_name']);
        }
        else {
            LogFile::addLog('unknown_video', $path, 'video');
            show_msg("unknown_video ", $data['file_name']);
        }
    }
    return true;
}

function update_tag($paths) {
    $tag = Instance::getMedia('video_tag');
    $update_tags = $tag->getAll() ?: [];

    $update_tags = array_column($update_tags, null, 'tag_name');

    $tags = [];
    while($base_dir = array_pop($paths)) {

        @$dir=opendir($base_dir);

        if ($dir !== false) {
            show_msg("open_dir ", $base_dir);
            while($f=readdir($dir)) {
                if ($f == '.' || $f=='..') {
                    continue;
                }
                $path = $base_dir . DIRECTORY_SEPARATOR . $f;

                if (is_dir($path)) {
                    if (isset($update_tags[$f])) {
                        $update_ary = [
                            'path'  =>  $path,
                        ];

                        if (isset($tags[dirname($path)])) {
                            $update_ary['parent_id'] = $tags[dirname($path)];
                        }
                        else if (isset($tags[dirname(dirname($path))])) {
                            $update_ary['parent_id'] = $tags[dirname(dirname($path))];
                        }

                        if ($update_tags[$f]['create_time'] == '0000-00-00 00:00:00') {
                            $update_ary['create_time'] = '2018-12-19 22:00:00';
                        }

                        $tag->updateByCondFromDb($update_ary, ['tag_id'=>$update_tags[$f]['tag_id']]);
                        show_msg("updated_tag ", $update_tags[$f]['tag_name'] . "\t" .json_encode($update_ary));
                        $tags[$path] = $update_tags[$f]['tag_id'];
                    }

                    array_push($paths, $path);
                }
            }
            closedir($dir);
        }
        else {
            LogFile::addLog('open_dir_failed', $base_dir, 'video');
            show_msg("open_dir_failed ", $base_dir);
        }
    }
}


function compress_image() {
    $videos = Instance::getMedia('video')->select('file_index, id')->getAll();

    $image = new Image(FFMPEG_IMAGE_PATH);
    foreach($videos as $video) {
        $image->thumb($video['id'] . '.png');
        echo $video['id'] . '.png' . "\r\n";
    }
}


function show_msg($type, $msg) {
    if ($type == 'open_dir') {
        return;
    }

    $str = getFormatDate() . "\t" . $type . "\t" . $msg . PHP_EOL;

    if (RUNNING_LOG) {
        file_put_contents(LOG_PATH . 'video_running.log', $str, FILE_APPEND);
    }

    echo $str;
}