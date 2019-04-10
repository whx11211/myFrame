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
            case 'mod':
                $this->_mod();
                break;
            case 'getTagsConf':
                $this->_getTagsConf();
                break;
            case 'play':
                $this->_play();
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
            'path'          =>  array("transform", array($this, 'rightLikeFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
            'last_mod_time' =>  array("transform", array($this, 'betweenDateFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $search_tags = RemoteInfo::request('tags');
        if ($search_tags) {
            Instance::getMedia('video_tag')->increaseSearchCount($search_tags);
        }

        $select_ary = array('id', 'path','file_index', 'file_name', 'file_size', 'duration', 'create_time', 'last_mod_time', 'last_view_time', 'view_count', 'tags', 'description');
    
        $class = Instance::getMedia('video');
        $this->getPage($class, $select_ary, $where_arg);
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
        
        $class = Instance::getMedia('video');

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

        $class = Instance::getMedia('video');

        $this->update($class, $insert_args, $where_arg);

    }

    /**
     * 标签详细
     */
    private function _getTagsConf()
    {
        Output::success(Instance::getMedia('video_tag')->getTagMap());
    }
    
    /**
     * 播放
     */
    private function _play()
    {
        $form_cond_conf = array(
            'id'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $class = Instance::getMedia('video');
        $video = $class->where($where_arg)->getRow();
        $file_path = $video['path'] . "\\" . $video['file_name'];

        if (strpos($file_path, VIDEO_URL_BASE_PATH) !== 0) {
            Output::fail(ErrorCode::VIDEO_PATH_ERROR);
        }

        $class->setViewData($where_arg['id']);

        $data['url_path'] = VIDEO_HOST . url_path_format(str_replace(DIRECTORY_SEPARATOR, '/', substr($file_path, strlen(VIDEO_URL_BASE_PATH))));

        if (in_array(RemoteInfo::getIP(), ['127.0.0.1', '::1'])) {
            $data['vlc_play'] = 'webbin://vlcplay/?f=' . urlencode($file_path);
        }
        else {
            switch (RemoteInfo::getOs()) {
                case 'windows':
                    $data['vlc_play'] = 'webbin://vlcplay/?f=' . urlencode($data['url_path']);
                    break;
                case 'iphone':
                case 'ipad':
                case 'android':
                    $data['vlc_play'] = 'vlc://' . $data['url_path'];
                    break;
            }
        }

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

        $class = Instance::getMedia('video_tag');
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

        $class = Instance::getMedia('video_tag');

        $this->del($class, $where_arg);
    }

    /**
     * 修改
     */
    private function _modTag()
    {
        $form_mod_conf = array(
            'tag_name' =>  array("length", array(1, 32), ErrorCode::PARAM_ERROR),
            'path'      =>  array("length", array(0, 1024), ErrorCode::PARAM_ERROR),
            'parent_id' =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $mod_args = RemoteInfo::getInsertFormArgs($form_mod_conf);

        $form_cond_conf = array(
            'tag_id'    =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $class = Instance::getMedia('video_tag');

        $this->update($class, $mod_args, $where_arg);
    }


    /**
     * 修改
     */
    private function _addTag()
    {
        $form_add_conf = array(
            'tag_name' =>  array("length", array(1, 32), ErrorCode::PARAM_ERROR),
            'create_time'=>  array("auto", 'get_format_date', ErrorCode::PARAM_ERROR),
            'path'      =>  array("length", array(0, 1024), ErrorCode::PARAM_ERROR),
            'parent_id' =>  array("int"),
        );
        $add_args = RemoteInfo::getInsertFormArgs($form_add_conf);

        $class = Instance::getMedia('video_tag');

        $id = $class->select('tag_id')->where(['tag_name'=>$add_args['tag_name']])->getOne();
        if (!$id) {
            $id = $class->insertByCondFromDb($add_args, 2);
        }

        $data['new_tag_id'] = $id;
        if (RemoteInfo::request('getTagConf')) {
            $data['new_conf'] = $class->getTagMap();
        }

        Output::success($data);
    }
}