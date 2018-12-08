<?php

/**
 * Copyright (c) 2018, whx
 * All rights reserved.
 *
 * @description: 视频处理软件FFMPEG封装
 * @author     : whx<whx341222@163.com>
 * @date       : 2018-12-08
 */
class FFMpeg {
    private static $is_install = false;

    /**
     * 执行指令
     * @param $command
     * @return string
     */
    public static function exec($command) {
        if (!self::$is_install) {
            if (!exec(FFMPEG_PATH.'ffmpeg -version')) {
                throw new Error("ffmpeg not find");
                exit;
            }
            else {
                self::$is_install = true;
            }
        }
        echo FFMPEG_PATH.$command;
        return shell_exec(FFMPEG_PATH.$command);
    }

    public static function createPreviewImageFromVideo($video, $image_path, $duration=null) {
        $tm = '0:0:1';
        if (!is_null($duration)) {
            $duration = $duration / 2;
            //$tm = floor($duration/3600) . ':' . floor(($duration%3600)/60) . ':' . floor($duration%60);
        }
        if (file_exists($image_path)) {
            if (stripos(php_uname('s'), 'window') !== false) {
                shell_exec("del ".str_replace('/','\\', $image_path));
            }
            else {
                shell_exec("rm -f ".$image_path);
            }
        }
        return self::exec("ffmpeg -i ".$video." -ss ".$tm." -vframes 1 " . $image_path);
    }

    /**
     * 获取视频信息
     * @param $file
     * @return array|null
     */
    public static function getVideoDetail($file) {
        $res = self::exec("ffprobe ".$file." -print_format json -show_format -v quiet");
        $res = json_decode($res, true);
        print_r($res);
        if (!isset($res['format']['duration'])) {
            return null;
        }

        $file_size = $res['format']['size'];
        if (($tmp=$file_size/1024/1024/1024)>=1) {
            $file_size = round($tmp, 2) . 'GB';
        }
        else if (($tmp=$file_size/1024/1024)>=1) {
            $file_size = round($tmp, 2) . 'MB';
        }
        else {
            $file_size = round($file_size/1024, 2) . 'KB';
        }

        $detail = [
            'path'      =>  dirname($res['format']['filename']),
            'file_name' =>  basename($res['format']['filename']),
            'file_size' =>  $file_size,
            'duration'	=>  round($res['format']['duration']/60, 2),
            'create_time'=>  date('Y-m-d H:i:s', filectime($file)),
            'last_mod_time'=>  date('Y-m-d H:i:s', filemtime($file)),
            'add_time'     =>  date('Y-m-d H:i:s'),
        ];

        $detail['file_index'] = md5($detail['path']).md5($detail['file_name']);
        $image_path = FFMPEG_IMAGE_PATH  . $detail['file_index'] . '.png';

        if ($a=self::createPreviewImageFromVideo($file, $image_path, $res['format']['duration'])) {
            $detail['preview_image'] = basename($image_path);
        }

        return $detail;
    }
}