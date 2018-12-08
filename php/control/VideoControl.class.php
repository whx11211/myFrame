<?php

class VideoControl extends Control
{
    public function index()
    {
        $action = RemoteInfo::post('a');
        switch ($action) {
            case 'del':
                $this->_del();
                break;
            case 'detail':
                $this->_detail();
                break;
            case 'reply':
                $this->_reply();
                break;
            default:
                $this->_get();
                break;
        }
    }
    /**
     * 获取
     */
    private function _get()
    {
        $form_cond_conf = array(
            'tags'          =>  array("transform", array($this, 'findInSetFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
            'last_mod_time' =>  array("transform", array($this, 'betweenTimeFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
    
        $select_ary = array('id', 'file_name', 'file_size', 'create_time', 'last_mod_time', 'last_view_time', 'view_count', 'tags');
    
        $Post = Instance::getVideo('video');
        $this->getPage($Post, $select_ary, $where_arg);
    }
    /**
     * 删除
     */
    private function _del()
    {
        $form_cond_conf = array(
            'postid'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
        
        $Post = Instance::getMovie('post');
        $this->del($Post, $where_arg);
    }
    
    /**
     * 详细
     */
    private function _detail()
    {
        $form_cond_conf = array(
            'postid'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
        
        $Post = Instance::getMovie('post');
        
        Output::success($Post->where($where_arg)->getAll()[0]);
    }
    
    /**
     * 回复
     */
    private function _reply()
    {
        $form_cond_conf = array(
            'postid'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
        
        $Repost = Instance::getMovie('repost');
        
        $this->getPage($Repost, array('*'), $where_arg);
    }
    
    /**
     * 用户管理
     */
    public function user()
    {
        $action = RemoteInfo::post('a');
        switch ($action) {
            case 'del':
                $this->_delUser();
                break;
            default:
                $this->_getUser();
                break;
        }
    }
    
    /**
     * 获取用户列表
     */
    private function _getUser()
    {
        $form_cond_conf = array(
            'username'      =>  array("transform", array($this, 'likeFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
            'regtime'       =>  array("transform", array($this, 'betweenTimeFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
    
        $select_ary = array('userid', 'username', 'regtime', 'email');
    
        $User = Instance::getMovie('user');
        $this->getPage($User, $select_ary, $where_arg);
    }
    /**
     * 删除
     */
    private function _delUser()
    {
        $form_cond_conf = array(
            'userid'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
        
        $User = Instance::getMovie('user');
        $this->del($User, $where_arg);
    }
    
    /**
     * 回帖管理
     */
    public function repost()
    {
        $action = RemoteInfo::post('a');
        switch ($action) {
            case 'del':
                $this->_delRepost();
                break;
            default:
                $this->_getRepost();
                break;
        }
    }
    /**
     * 获取回帖列表
     */
    private function _getRepost()
    {
        $form_cond_conf = array(
            'username'      =>  array("transform", array($this, 'likeFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
            'reposttime'    =>  array("transform", array($this, 'betweenTimeFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
            'postid'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
    
        $select_ary = array('*');
    
        $Object = Instance::getMovie('repost');
        $this->getPage($Object, $select_ary, $where_arg);
    }
    /**
     * 删除回帖
     */
    private function _delRepost()
    {
        $form_cond_conf = array(
            'repostid'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
        
        $Object = Instance::getMovie('repost');
        $this->del($Object, $where_arg);
    }
}