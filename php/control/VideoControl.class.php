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
            'last_mod_time' =>  array("transform", array($this, 'betweenDateFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $search_tags = RemoteInfo::request('tags');
        if ($search_tags) {
            $tag = Instance::getVideo('tag');
            foreach ($search_tags as $id) {
                $tag->execSql('update tag set search_count=search_count+1 where tag_id=?', array($id));
            }
        }

        $select_ary = array('id', 'path','file_index', 'file_name', 'file_size', 'duration', 'create_time', 'last_mod_time', 'last_view_time', 'view_count', 'tags');
    
        $class = Instance::getVideo('video');
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
        
        $class = Instance::getVideo('video');
        $video = $class->where($where_arg)->getRow();
        if ($video === false) {
            Output::fail(ErrorCode::FILE_NOT_EXISTS);
        }

        $old_tags = explode(',', $video['tags']);
        if ($old_tags) {
            $tag_model = Instance::getVideo('tag');
            foreach ($old_tags as $t) {
                $tag_model->execSql('update tag set video_count=video_count+1 where tag_id=?', array($t));
            }
        }
        System::delFile($video['path'] . "\\" . $video['file_name']);
        System::delFile(FFMPEG_IMAGE_PATH  . $video['file_index'] . '.png');

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
            'duration'  =>  array("regex", 'double', ErrorCode::PARAM_ERROR, 'null_skip')
        );
        $insert_args = RemoteInfo::getInsertFormArgs($form_add_conf);

        $form_cond_conf = array(
            'id'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $class = Instance::getVideo('video');
        $class->beginTransaction();
        $video = $class->where($where_arg)->getRow();
        $has_new_tag = false;
        $new_tags = RemoteInfo::request('tags');
        if ($new_tags) {
            $old_tags = explode(',', $video['tags']);
            $tag_model = Instance::getVideo('tag');
            foreach ($new_tags as $k => $t) {
                if (!in_array($t, $old_tags)) {
                    if (!$tag_model->select('tag_id')->where(['tag_id'=>$t])->getOne()) {
                        //新标签
                        $tid = $tag_model->insert(['tag_name'=>$t, 'create_time'=>time()]);
                        $new_tags[$k] = $t = $tid;
                        $has_new_tag = true;
                    }
                    $tag_model->execSql('update tag set video_count=video_count+1 where tag_id=?', array($t));
                }
            }
            foreach ($old_tags as $t) {
                if (!in_array($t, $new_tags)) {
                    $tag_model->execSql('update tag set video_count=video_count-1 where tag_id=?', array($t));
                }
            }
            $insert_args['tags'] = implode(',', $new_tags);
        }
        if (($file_index = md5($insert_args['path']) . md5($insert_args['file_name'])) != $video['file_index']) {
            $insert_args['file_index'] = $file_index;
            $class->updateByCondFromDb($insert_args, $where_arg);
            System::moveFile($video['path'] . "\\" . $video['file_name'], $insert_args['path'] . "\\" . $insert_args['file_name'], 0);
            System::moveFile(FFMPEG_IMAGE_PATH  . $video['file_index'] . '.png', FFMPEG_IMAGE_PATH  . $insert_args['file_index'] . '.png');
        }
        else {
            $class->updateByCondFromDb($insert_args, $where_arg);
        }
        $class->commit();

        Output::success(['has_new_tag'=>$has_new_tag]);

    }

    /**
     * 标签详细
     */
    private function _getTagsConf()
    {
        $class = Instance::getVideo('tag');
        
        Output::success($class->getAll());
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

        $class = Instance::getVideo('video');
        $video = $class->where($where_arg)->getRow();
        $file_path = $video['path'] . "\\" . $video['file_name'];

        if (strpos($file_path, VIDEO_URL_BASE_PATH) !== 0) {
            Output::fail(ErrorCode::VIDEO_PATH_ERROR);
        }

        $class->execSql('update video set view_count=view_count+1,last_view_time=? where id=?', array(date('Y-m-d H:i:s'),$where_arg['id']));

        $data['url_path'] = VIDEO_HOST . str_replace('\\', '/', substr($file_path, strlen(VIDEO_URL_BASE_PATH)));

        if (in_array(RemoteInfo::getIP(), ['127.0.0.1', '::1'])) {
            $data['vlc_play'] = 'webbin://vlcplay/?f=' . urlencode($file_path);
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
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $select_ary = array('*');

        $class = Instance::getVideo('tag');
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

        $class = Instance::getVideo('tag');

        $this->del($class, $where_arg);
    }

    /**
     * 修改
     */
    private function _modTag()
    {
        $form_mod_conf = array(
            'tag_name' =>  array("length", array(1, 32), ErrorCode::PARAM_ERROR),
        );
        $mod_args = RemoteInfo::getInsertFormArgs($form_mod_conf);

        $form_cond_conf = array(
            'tag_id'    =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $class = Instance::getVideo('tag');

        $this->update($class, $mod_args, $where_arg);
    }


    /**
     * 修改
     */
    private function _addTag()
    {
        $form_add_conf = array(
            'tag_name' =>  array("length", array(1, 32), ErrorCode::PARAM_ERROR),
            'create_time'=>  array("auto", 'time', ErrorCode::PARAM_ERROR)
        );
        $add_args = RemoteInfo::getInsertFormArgs($form_add_conf);

        $class = Instance::getVideo('tag');

        $this->add($class, $add_args);

    }
}