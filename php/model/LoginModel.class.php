<?php 

/**
 * LoginModel 登录管理类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class LoginModel extends Model
{
    /**
     * 表名
     * string
     */
    protected $table = 'loginList';

    /**
     * 登录记录类
     */
    public function loginRecode($userid)
    {
        $insert_ary = array(
            'userId'    =>  $userid,
            'LoginIp'   =>  RemoteInfo::getIP(),
            'loginTime' =>  time()
        );
        return $this->insert($insert_ary);
    }

}

?>