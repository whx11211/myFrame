<?php 

/**
 * Movie基类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class Video extends Model
{
    /**
     * 构造函数
     */
    public function __construct($table=null, $type=VIDEO_DB_TYPE, $name=VIDEO_DB_NAME, $ip=VIDEO_DB_IP, $port=VIDEO_DB_PORT, $user=VIDEO_DB_USER, $pwd=VIDEO_DB_PWD)
    {
        parent::__construct($table, $type, $name, $ip, $port, $user, $pwd);
    }

}

?>