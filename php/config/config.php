<?php 
/**
 * 配置设置
 *
 * @copyright   Copyright (c) 2017-2018
 * @author      whx
 * @version     ver 1.0
 */

// 设置时区
ini_set('date.timezone', 'Asia/Shanghai');

// 数据库类型，请填写PDO识别的标准名称 如mysql
define('DB_TYPE', 'mysql');
// 数据库IP
define('DB_IP', '127.0.0.1');
// 数据库端口，默认3306，一般不需修改
define('DB_PORT', 33306);
// 数据库IP
define('DB_USER', 'root');
// 数据库密码
define('DB_PWD', 'root');
// 默认数据库名
define('DB_NAME', 'my_frame');

// 电影数据库类型，请填写PDO识别的标准名称 如mysql
define('MOVIE_DB_TYPE', 'mysql');
// 电影论坛数据库IP
define('MOVIE_DB_IP', '127.0.0.1');
// 电影论坛数据库端口，默认3306，一般不需修改
define('MOVIE_DB_PORT', 33306);
// 电影论坛数据库IP
define('MOVIE_DB_USER', 'root');
// 电影论坛数据库密码
define('MOVIE_DB_PWD', 'root');
// 电影论坛默认数据库名
define('MOVIE_DB_NAME', 'my_movie');

// Video数据库类型，请填写PDO识别的标准名称 如mysql
define('MEDIA_DB_TYPE', 'mysql');
// Video论坛数据库IP
define('MEDIA_DB_IP', '127.0.0.1');
// Video论坛数据库端口，默认3306，一般不需修改
define('MEDIA_DB_PORT', 33306);
// Video论坛数据库IP
define('MEDIA_DB_USER', 'root');
// Video论坛数据库密码
define('MEDIA_DB_PWD', 'root');
// Video论坛默认数据库名
define('MEDIA_DB_NAME', 'my_media');

define('FFMPEG_IMAGE_PATH', str_replace('/', '\\', dirname(ROOT)) . '\views\images\ffmpeg\\');
define('VIDEO_BASE_PATH', 'H:\Progrom\Common Files\Videos');
define('VIDEO_URL_BASE_PATH', 'H:\Progrom\Common Files\Videos\\');
if (isset($_SERVER['HTTP_HOST'])) {
	define('VIDEO_HOST', 'http://' . $_SERVER['HTTP_HOST'] . ':8000/');
}
define('FFMPEG_PATH', 'D:\软件\ffmpeg-20181208-6b1c4ce-win64-static\bin\\');

define('IMAGE_BASE_PATH', 'H:\Progrom\Common Files\Videos\图片收藏');
define('IMAGE_URL_BASE_PATH', 'H:\Progrom\Common Files\Videos\\');
if (isset($_SERVER['HTTP_HOST'])) {
	define('IMAGE_HOST', 'http://' . $_SERVER['HTTP_HOST'] . ':8000/');
}

//缓存配置
define('CACHE_HOST', '127.0.0.1');
define('CACHE_PORT', '6379');



?>