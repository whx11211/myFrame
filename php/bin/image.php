<?php

// 数据库执行语句是否记录
define('DB_DEBUG', 1);

//错误DEGUE，=1表示直接显示错误，=0表示使用自定义错误处理
define('ERROR_DEBUG', 1);

//报错级别设置
define('ERROR_LEVEL', E_ALL);

require_once __DIR__.'/../common/init.php';

$image_exts = [
    'bmp', 'ico', 'jpg', 'jng', 'koa', 'iff', 'mng', 'pcd', 'pcx', 'png', 'ras', 'tga', 'tif', 'wap', 'psd',
    'cut', 'xbm', 'xpm', 'dds', 'gif', 'hdr', 'g3', 'sgi', 'exr', 'j2k', 'jp2', 'pfm', 'pct', 'raw', 'wmf',
    'jpc', 'pgx', 'pnm', 'ska', 'webp', 'wdp', 'tbi'
];

$type = $argv[1];
switch ($type) {
    case 'add':
        $tag = Instance::getVideo('image_tag');
        $tags = $tag->getAll() ?: [];
        if ($tags) {
            $tags = array_column($tags, 'tag_id', 'tag_name');
        }
        $image = Instance::getVideo('image');
        $images_index = $image->select('file_index')->getAll();
        if ($images_index) {
            $images_index = array_column($images_index, 'file_index');
        }
        add(IMAGE_BASE_PATH);
        break;
    case 'add_tag':
        $tag = Instance::getVideo('image_tag');
        $tag_names = $tag->select('tag_name')->getAll() ?: [];
        if ($tag_names) {
            $tag_names = array_column($tag_names, 'tag_name');
        }
        add_tag(IMAGE_BASE_PATH);
        break;
    default:
        echo 'param error!';
        break;
}

function add($base_dir) {
    @$dir=opendir($base_dir);

    if ($dir !== false) {
        show_msg("open_dir ", $base_dir);
        while($f=readdir($dir)) {
            if ($f == '.' || $f=='..') {
                continue;
            }
            $path = $base_dir . '\\' . $f;

            if (is_dir($path)) {
                add($path);
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

        $image_path_detail = explode('\\', $path);
        $image_tags = [];
        foreach ($image_path_detail as $tag_name) {
            if (isset($tags[$tag_name])) {
                $image_tags[] = $tags[$tag_name];
                $tag->execSql('update image_tag set image_count=image_count+1 where tag_id=?', array($tags[$tag_name]));
            }
        }
        $detail['tags'] = implode(',', $image_tags);
        $image->insert($detail, 2);
        show_msg('add_file ', $detail['file_name']);

    }
    return true;
}


function add_tag($base_dir) {
    global $tag_names, $tag;
    @$dir=opendir($base_dir);

    if ($dir !== false) {
        show_msg("open_dir ", $base_dir);
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
                    show_msg('add_tag ', $f);
                }
                add_tag($path);
            }
        }
        closedir($dir);
    }
    else {
        LogFile::addLog('open_dir_failed', $base_dir, 'image');
        show_msg("open_dir_failed ",$base_dir);
    }
}

function show_msg($type, $msg) {
    echo getFormatDate() . "\t" . $type . "\t" . $msg . "\r\n";
}