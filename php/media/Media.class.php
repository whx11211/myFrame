<?php 

/**
 * Movie基类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class Media extends Model
{
    /**
     * 构造函数
     */
    public function __construct($table=null, $type=MEDIA_DB_TYPE, $name=MEDIA_DB_NAME, $ip=MEDIA_DB_IP, $port=MEDIA_DB_PORT, $user=MEDIA_DB_USER, $pwd=MEDIA_DB_PWD)
    {
        parent::__construct($table, $type, $name, $ip, $port, $user, $pwd);
    }

}

?>