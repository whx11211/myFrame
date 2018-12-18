<?php
define('VLC_PLAYER_PATH', "C:\Program Files\VideoLAN\VLC\\vlc.exe");

$url = $argv[1];

$url_info = parse_url($url);

$host = $url_info['host'] ?? null;
parse_str($url_info['query'], $param);

switch ($host) {
    case 'vlcplay':
        if (!isset($param['f']) || !$param['f']) {
            exit('f cannot be null');
        }
        vlc_play($param['f']);
        break;
    case 'opendir':
        if (!isset($param['path']) || !$param['path']) {
            exit('path cannot be null');
        }
        shell_exec('Explorer /select, "' . $param['path'] . '"');
        break;
    case 'open':
        if (!isset($param['path']) || !$param['path']) {
            exit('path cannot be null');
        }
        shell_exec('start "" "' . $param['path'] . '"');
        break;
    default:
        echo 'host error!';
        break;
}


function vlc_play($f) {
    if (!defined('VLC_PLAYER_PATH')) {
        exit('VLC_PLAYER_PATH not defined!');
    }
    shell_exec('start "' . VLC_PLAYER_PATH . '" "' . $f . '"');
}

