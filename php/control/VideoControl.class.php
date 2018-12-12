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

        $select_ary = array('id', 'file_index', 'file_name', 'file_size', 'duration', 'create_time', 'last_mod_time', 'last_view_time', 'view_count', 'tags');
    
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
            'file_name' =>  array("length", array(1, 1024), ErrorCode::PARAM_ERROR),
            'duration'  =>  array("regex", 'double', ErrorCode::PARAM_ERROR, 'null_skip'),
            'tags'      =>  array("transform", function($a) {return is_array($a) ? implode(',', $a) : '';}, ErrorCode::PARAM_ERROR),
        );
        $insert_args = RemoteInfo::getInsertFormArgs($form_add_conf);

        $form_cond_conf = array(
            'id'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $class = Instance::getVideo('video');
        $class->beginTransaction();
        $video = $class->where($where_arg)->getRow();
        if (isset($insert_args['tags'])) {
            $new_tags = explode(',', $insert_args['tags']);
            $old_tags = explode(',', $video['tags']);
            $tag_model = Instance::getVideo('tag');
            foreach ($new_tags as $t) {
                if (!in_array($t, $old_tags)) {
                    $tag_model->execSql('update tag set video_count=video_count+1 where tag_id=?', array($t));
                }
            }
            foreach ($old_tags as $t) {
                if (!in_array($t, $new_tags)) {
                    $tag_model->execSql('update tag set video_count=video_count-1 where tag_id=?', array($t));
                }
            }
        }
        if ($insert_args['file_name'] != $video['file_name']) {
            $insert_args['file_index'] = md5($video['path']) . md5($insert_args['file_name']);
            $class->updateByCondFromDb($insert_args, $where_arg);
            System::moveFile($video['path'] . "\\" . $video['file_name'], $video['path'] . "\\" . $insert_args['file_name'], 0);
            System::moveFile(FFMPEG_IMAGE_PATH  . $video['file_index'] . '.png', FFMPEG_IMAGE_PATH  . $insert_args['file_index'] . '.png');
        }
        else {
            $class->updateByCondFromDb($insert_args, $where_arg);
        }
        $class->commit();

        Output::success();

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

        $url_path = str_replace('\\', '/', substr($file_path, strlen(VIDEO_URL_BASE_PATH)));
        Output::success(VIDEO_HOST.$url_path);
    }

}