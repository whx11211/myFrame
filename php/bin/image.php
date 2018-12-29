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

$image_exts = [
    'bmp', 'ico', 'jpg', 'jng', 'koa', 'iff', 'mng', 'pcd', 'pcx', 'png', 'ras', 'tga', 'tif', 'wap', 'psd',
    'cut', 'xbm', 'xpm', 'dds', 'gif', 'hdr', 'g3', 'sgi', 'exr', 'j2k', 'jp2', 'pfm', 'pct', 'raw', 'wmf',
    'jpc', 'pgx', 'pnm', 'ska', 'webp', 'wdp', 'tbi'
];

$type = $argv[1];
switch ($type) {
    case 'add':
        $tag = Instance::getMedia('image_tag');
        $tags = $tag->where("path != ''")->getAll() ?: [];
        if ($tags) {
            $tags = array_column($tags, 'tag_id', 'path');
        }
        $image = Instance::getMedia('image');
        $images_index = $image->select('file_index')->getAll();
        if ($images_index) {
            $images_index = array_column($images_index, 'file_index');
        }
        if (isset($argv[2])) {
            if (is_dir($argv[2])) {
                add([$argv[2]]);
            }
            else if (file_exists($argv[2])) {
                add_image($argv[2]);
            }
            else {
                echo "dir or file not exists!";
                exit;
            }
        }
        else {
            add([VIDEO_BASE_PATH]);
        }
        break;
    default:
        echo 'param error!';
        break;
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
                    add_image($path);
                }
            }
            closedir($dir);
        }
        else {
            LogFile::addLog('open_dir_failed', $base_dir, 'image');
            show_msg("open_dir_failed ", $base_dir);
        }
    }
}


function add_image($path) {
    global $image, $image_exts,$images_index,$tags,$tag;
    $index = md5(dirname($path)).md5(substr($path, strlen(dirname($path))+1));
    if (in_array($index, $images_index)) {
        return true;
    }
    $path_info = pathinfo($path);
    if (in_array(strtolower($path_info['extension']) ,$image_exts)) {
        $detail = [
            'path'      =>  dirname($path),
            'file_name' =>  substr($path, strlen(dirname($path))+1),
            'file_size' =>  round(filesize($path)/1024, 2),
            'create_time'=>  date('Y-m-d H:i:s', filectime($path)),
            'last_mod_time'=>  date('Y-m-d H:i:s', filemtime($path)),
            'add_time'     =>  date('Y-m-d H:i:s'),
        ];

        $detail['file_index'] = md5($detail['path']).md5($detail['file_name']);

        if (!isset($tags[$detail['path']]) && $detail['path'] != IMAGE_BASE_PATH) {
            $path_detail = explode(DIRECTORY_SEPARATOR, substr($detail['path'], strlen(IMAGE_BASE_PATH)+1));
            $new_tag_path = IMAGE_BASE_PATH;
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

        $image_tags = [];
        foreach ($tags as $tag_path => $tag_id) {
            if (strpos($detail['path'], $tag_path) === 0) {
                $image_tags[] = $tag_id;
            }
        }
        $detail['tags'] = implode(',', $image_tags);
        $image->insertByCondFromDb($detail, 2);
        show_msg('add_file ', $detail['file_name']);

    }
    return true;
}

function update_tag($paths) {
    $tag = Instance::getMedia('image_tag');
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

function show_msg($type, $msg) {
    if ($type == 'open_dir') {
        return;
    }

    $str = getFormatDate() . "\t" . $type . "\t" . $msg . PHP_EOL;

    if (RUNNING_LOG) {
        file_put_contents(LOG_PATH . 'image_running.log', $str, FILE_APPEND);
    }

    echo $str;
}