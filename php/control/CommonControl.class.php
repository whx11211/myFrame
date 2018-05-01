<?php

class CommonControl extends Control
{

    /**
     * 用户信息获取（id作为key）
     */
    public function getUsersInfo()
    {
        $User = Instance::get('user');
        $admin_users = $User->select(array('id','userName'))->getAll();
        $tmp = array();
        foreach ($admin_users as $v) {
            $tmp[$v['id']] = $v;
        }
        Output::success($tmp);
    }
    
    /**
     * 角色信息获取（id作为key）
     */
    public function getRolesInfo()
    {
        $Role = Instance::get('role');
        $admin_roles = $Role->select(array('id','roleName'))->getAll();
        $tmp = array();
        foreach ($admin_roles as $v) {
            $tmp[$v['id']] = $v;
        }
        Output::success($tmp);
    }
}