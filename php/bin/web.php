<?php
define('VLC_PLAYER_PATH', "C:\Program Files\VideoLAN\VLC\\vlc.exe");

$url = $argv[1];

$url_info = parse_url($url);

$host = $url_info['host'] ?? null;

switch ($host) {
    case 'vlcplay':
        parse_str($url_info['query'], $param);
        if (!isset($param['f']) || !$param['f']) {
            exit('f cannot be null');
        }
        vlc_play($param['f']);
        break;
    default:
        echo 'host error!';
        break;
}


function vlc_play($f) {
    if (!defined('VLC_PLAYER_PATH')) {
        exit('VLC_PLAYER_PATH not defined!');
    }
    shell_exec('"' . VLC_PLAYER_PATH . '" "' . $f . '"');
}
