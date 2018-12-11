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
            'last_mod_time' =>  array("transform", array($this, 'betweenDateFormat'), ErrorCode::PARAM_ERROR, 'null_skip'),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);
    
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
        if (isset($insert_args['pwd'])) {

            $insert_args['pwd'] = md5($insert_args['pwd']);
        }


        $form_cond_conf = array(
            'id'        =>  array("regex", 'number', ErrorCode::PARAM_ERROR),
        );
        $where_arg = RemoteInfo::getSearchFormArgs($form_cond_conf);

        $class = Instance::getVideo('video');
        $class->beginTransaction();
        $video = $class->where($where_arg)->getRow();
        if ($insert_args['file_name'] != $video['file_name']) {
            $insert_args['file_index'] = md5($video['path']) . md5($insert_args['file_name']);
            $class->updateByCondFromDb($insert_args, $where_arg);
            System::renameFile($video['path'] . "\\" . $video['file_name'], $video['path'] . "\\" . $insert_args['file_name'], 0);
            System::renameFile(FFMPEG_IMAGE_PATH  . $video['file_index'] . '.png', FFMPEG_IMAGE_PATH  . $insert_args['file_index'] . '.png');
        }
        else {
            $class->updateByCondFromDb($insert_args, $where_arg);
        }
        $class->commit();

        Output::success(1);

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

        $class->execSql('update video set view_count=view_count+1 where id=?', array($where_arg['id']));

        $url_path = str_replace('\\', '/', substr($file_path, strlen(VIDEO_URL_BASE_PATH)));
        Output::success(VIDEO_HOST.$url_path);
    }

}