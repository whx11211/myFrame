<?php

class ImageControl extends Control
{
    public function index()
    {
        $action = RemoteInfo::post('a');
        switch ($action) {
            case 'del':
                $this->_del();
                break;
            case 'mod':
                $this->_mod();
                break;
            case 'getTagsConf':
                $this->_getTagsConf();
                break;
            case 'view':
                $this->_view();
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
            'file_name'     =>  array("transform", array($this, 'likeFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
            'last_mod_time' =>  array("transform", array($this, 'betweenDateFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $search_tags = RemoteInfo::request('tags');
        if ($search_tags) {
            Instance::getMedia('image_tag')->increaseSearchCount($search_tags);
        }

        $select_ary = array('id', 'path','file_index', 'file_name', 'file_size', 'create_time', 'last_mod_time', 'last_view_time', 'view_count', 'tags', 'description');
    
        $class = Instance::getMedia('image');
        $data = $this->getPage($class, $select_ary, $where_arg);
    }
    /**
     * 删除
     */
    private function _del()
    {
        $form_cond_conf = array(
            'id'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
        
        $class = Instance::getMedia('image');

        $this->del($class, $where_arg);
    }

    /**
     * 修改
     */
    private function _mod()
    {
        $form_add_conf = array(
            'path'      =>  array("length", array(1, 1024), ErrorCode::PARAM_ERROR),
            'file_name' =>  array("length", array(1, 1024), ErrorCode::PARAM_ERROR),
            'description' =>  array("length", array(0, 10240), ErrorCode::PARAM_ERROR),
            'duration'  =>  array("regex", 'double', ErrorCode::PARAM_ERROR, 'null_skip'),
            'tags'      =>  array('transform', function($t){return implode(',', $t ?: []);}, ErrorCode::PARAM_ERROR)
        );
        $insert_args = RemoteInfo::getInsertFormArgs($form_add_conf);

        $form_cond_conf = array(
            'id'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $class = Instance::getMedia('image');

        $this->update($class, $insert_args, $where_arg);

    }

    /**
     * 标签详细
     */
    private function _getTagsConf()
    {
        Output::success(Instance::getMedia('image_tag')->getTagMap());
    }

    /**
     * 播放
     */
    private function _view()
    {
        $form_cond_conf = array(
            'tags'          =>  array("transform", array($this, 'findInSetFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
            'file_name'     =>  array("transform", array($this, 'likeFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
            'last_mod_time' =>  array("transform", array($this, 'betweenDateFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $select_ary = array('id', 'path','file_index', 'file_name', 'file_size', 'create_time', 'last_mod_time', 'last_view_time', 'view_count', 'tags', 'description');

        $class = Instance::getMedia('image');
        $data = $this->getPage($class, $select_ary, $where_arg ,true);
        $image = $data['items'][0];

        $file_path = $image['path'] . "\\" . $image['file_name'];

        if (strpos($file_path, IMAGE_URL_BASE_PATH) !== 0) {
            Output::fail(ErrorCode::IMAGE_PATH_ERROR);
        }

        $class->setViewData($image['id']);

        $image['url_path'] = IMAGE_HOST . str_replace(DIRECTORY_SEPARATOR, '/', substr($file_path, strlen(IMAGE_URL_BASE_PATH)));

        if (in_array(RemoteInfo::getIP(), ['127.0.0.1', '::1'])) {
            $image['file_path'] = $file_path;
        }

        $data['items'][0] = $image;

        Output::success($data);
    }

    /**
     * 标签
     */
    public function tag()
    {
        $action = RemoteInfo::post('a');
        switch ($action) {
            case 'del':
                $this->_delTag();
                break;
            case 'mod':
                $this->_modTag();
                break;
            case 'add':
                $this->_addTag();
                break;
            default:
                $this->_getTag();
                break;
        }
    }

    /**
     * 获取
     */
    private function _getTag()
    {
        $form_cond_conf = array(
            'tag_name'      =>  array("transform", array($this, 'likeFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
            'parent_id'     =>  array("regex", 'number', ErrorCode::PARAM_ERROR, 'null_skip'),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $select_ary = array('*');

        $class = Instance::getMedia('image_tag');
        $this->getPage($class, $select_ary, $where_arg);
    }

    /**
     * 删除
     */
    private function _delTag()
    {
        $form_cond_conf = array(
            'tag_id'    =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $class = Instance::getMedia('image_tag');

        $this->del($class, $where_arg);
    }

    /**
     * 修改
     */
    private function _modTag()
    {
        $form_mod_conf = array(
            'tag_name'  =>  array("length", array(1, 32), ErrorCode::PARAM_ERROR),
            'path'      =>  array("length", array(0, 1024), ErrorCode::PARAM_ERROR),
            'parent_id' =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $mod_args = RemoteInfo::getInsertFormArgs($form_mod_conf);

        $form_cond_conf = array(
            'tag_id'    =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $class = Instance::getMedia('image_tag');

        $this->update($class, $mod_args, $where_arg);
    }


    /**
     * 修改
     */
    private function _addTag()
    {
        $form_add_conf = array(
            'tag_name' =>  array("length", array(1, 32), ErrorCode::PARAM_ERROR),
            'create_time'=>  array("auto", 'getFormatDate', ErrorCode::PARAM_ERROR)
        );
        $add_args = RemoteInfo::getInsertFormArgs($form_add_conf);

        $class = Instance::getMedia('image_tag');

        $id = $class->insertByCondFromDb($add_args, 2);

        if (!$id) {
            $id = $class->select('tag_id')->where(['tag_name'=>$add_args['tag_name']])->getOne();
        }

        $data['new_tag_id'] = $id;
        if (RemoteInfo::request('getTagConf')) {
            $data['new_conf'] = $class->getTagMap();
        }

        Output::success($data);
    }
}