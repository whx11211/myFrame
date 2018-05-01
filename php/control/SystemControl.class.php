<?php

class SystemControl extends Control
{
    
    /**
     * 登录处理
     */
    public function login()
    {
        $loginname = RemoteInfo::post('loginname');
        $pwd = RemoteInfo::post('pwd');
        $User = Instance::get('user');
        $admin_user = $User->where(array('userName'=>$loginname))->getAll();
    
        if (!$admin_user) {
            Output::fail(ErrorCode::LOGINNAME_ERROR);
        }
        else if (md5($pwd) !== $admin_user[0]['pwd']) {
            Output::fail(ErrorCode::LOGINPWD_ERROR);
        }
        else {
            unset($admin_user[0]['pwd']);
            $admin_user[0]['lastLoginTime'] = time();
            $Role = Instance::get('role');
            $admin_user[0]['pri'] = $Role->getAllMenuPermit($admin_user[0]['roleId']);
            SESSION::set('admin_user', $admin_user[0]);
            $Login = Instance::get('login');
            $Login->loginRecode($admin_user[0]['id']);
            $User->update(array('lastLoginTime'=>$admin_user[0]['lastLoginTime']), array('id'=>$admin_user[0]['id']));
            Output::success($admin_user);
        }
    }
    
    /**
     * 菜单配置获取
     */
    public function menu()
    {
        $admin_user = SESSION::get('admin_user');
        $Role = Instance::get('role');
        Output::success($Role->getMenuTrees($admin_user['roleId']));
    }
    
    /**
     * 用户信息获取
     */
    public function userInfo()
    {
        $admin_user = SESSION::get('admin_user');
        Output::success($admin_user);
    }
    
    
    /**
     * 注销
     */
    public function loginOut()
    {
        Output::success(SESSION::destory());
    }
    
    /**
     * 用户管理
     */
    public function user()
    {
        $action = RemoteInfo::post('a');
        switch ($action) {
            case 'get_roles':
                break;
            case 'add':
                $this->_addUser();
                break;
            case 'mod':
                $this->_modUser();
                break;
            case 'del':
                $this->_delUser();
                break;
            default:
                $this->_getUsers();
                break;
        }
    }
    /**
     * 获取用户列表
     */
    private function _getUsers()
    {
        $form_cond_conf = array(
            'id'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
            'roleId'    =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
    
        $select_ary = array('id', 'userName', 'lastLoginTime', 'roleId');
    
        $User = Instance::get('user');
        $this->getPage($User, $select_ary, $where_arg);
    }
    /**
     * 添加用户
     */
    private function _addUser()
    {
        $form_add_conf = array(
            'userName'  =>  array("length", array(2, 64), ErrorCode::PARAM_ERROR),
            'pwd'       =>  array("length", array(6, 30), ErrorCode::PARAM_ERROR),
            'roleId'    =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $insert_args = RemoteInfo::getInsertFormArgs($form_add_conf);
        $insert_args['pwd'] = md5($insert_args['pwd']);
        $User = Instance::get('user');
        $this->add($User, $insert_args);
    }
    /**
     * 修改用户
     */
    private function _modUser()
    {
        $form_add_conf = array(
            'userName'  =>  array("length", array(2, 64), ErrorCode::PARAM_ERROR),
            'pwd'       =>  array("length", array(6, 30), ErrorCode::PARAM_ERROR, 'null_skip'),
            'roleId'    =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $insert_args = RemoteInfo::getInsertFormArgs($form_add_conf);
        if (isset($insert_args['pwd'])) {

            $insert_args['pwd'] = md5($insert_args['pwd']);
        }
        
        
        $form_cond_conf = array(
            'id'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
        
        $User = Instance::get('user');
        $this->update($User, $insert_args, $where_arg);
    }
    /**
     * 删除用户
     */
    private function _delUser()
    {
        $form_cond_conf = array(
            'id'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
        
        $User = Instance::get('user');
        $this->del($User, $where_arg);
    }

    /**
     * 登录记录
     */
    public function loginList()
    {
        $Login = Instance::get('login');
        
        $form_cond_conf = array(
            'loginIp'   =>  array("transform", array($this, 'ipFormat'), ErrorCode::PARAM_ERROR),
            'loginTime' =>  array("transform", array($this, 'betweenTimeFormat'), ErrorCode::PARAM_ERROR),
            'userId'    =>  array("int"),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $this->getPage($Login, '*', $where_arg);
        
    }
    
    
    /**
     * 角色管理
     */
    public function role()
    {
        $action = RemoteInfo::post('a');
        switch ($action) {
            case 'get_all_menu':
                $Role = Instance::get('role');
                Output::success($Role->getAllMenuTrees());
                break;
            case 'add':
                $this->_addRole();
                break;
            case 'mod':
                $this->_modRole();
                break;
            case 'del':
                $this->_delRole();
                break;
            default:
                $this->_getRoles();
                break;
        }
    }
    /**
     * 获取用户列表
     */
    private function _getRoles()
    {
        $form_cond_conf = array(
            'id'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
    
        $select_ary = array('*');
    
        $Role = Instance::get('role');
        $this->getPage($Role, $select_ary, $where_arg);
    }
    /**
     * 添加用户
     */
    private function _addRole()
    {
        $form_add_conf = array(
            'roleName'  =>  array("length", array(2, 64), ErrorCode::PARAM_ERROR),
            'roleDesc'  =>  array("length", array(0, 200), ErrorCode::PARAM_ERROR),
            'privileges'=>  array("length", array(1, 2048), ErrorCode::PARAM_ERROR, 'null_skip'),
        );
        $insert_args = RemoteInfo::getInsertFormArgs($form_add_conf);
        $Role = Instance::get('role');
        $this->add($Role, $insert_args);
    }
    /**
     * 修改用户
     */
    private function _modRole()
    {
        $form_add_conf = array(
            'roleName'  =>  array("length", array(2, 64), ErrorCode::PARAM_ERROR),
            'roleDesc'  =>  array("length", array(0, 200), ErrorCode::PARAM_ERROR),
            'privileges'=>  array("length", array(1, 2048), ErrorCode::PARAM_ERROR),
        );
        $insert_args = RemoteInfo::getInsertFormArgs($form_add_conf);

        $form_cond_conf = array(
            'id'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
    
        $Role = Instance::get('role');
        $this->update($Role, $insert_args, $where_arg);
    }
    /**
     * 删除用户
     */
    private function _delRole()
    {
        $form_cond_conf = array(
            'id'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
    
        $Role = Instance::get('role');
        $this->del($Role, $where_arg);
    }
    
}