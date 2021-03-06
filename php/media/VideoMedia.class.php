<?php 

/**
 * VideoMedia
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class VideoMedia extends Media
{
    /**
     * 表名
     * string
     */
    protected $table = 'video';

    /**
     * 插入
     * @param array $insert_ary
     * @param int $type
     * @return int
     */
    public function insertByCondFromDb($insert_ary, $type=1)
    {
        $this->beginTransaction();
        $id = parent::insertByCondFromDb($insert_ary, $type);
        if (isset($insert_ary['tags']) && $insert_ary['tags']) {
            $tags = explode(',', $insert_ary['tags']);
            Instance::getMedia('video_tag')->increaseCount($tags);
        }
        $this->commit();
        return $id;
    }

    /**
     * 删除
     * @param array $cond_ary
     * @return bool
     */
    public function delByCondFromDb($cond_ary)
    {
        $videos = $this->where($cond_ary)->getAll();
        if ($videos === false) {
            Output::fail(ErrorCode::FILE_NOT_EXISTS);
        }

        foreach ($videos as $video) {
            if ($video['tags']) {
                $old_tags = explode(',', $video['tags']);
                
                @Instance::getMedia('video_tag')->decreaseCount($old_tags);
                
            }
            parent::delByCondFromDb(['id'=>$video['id']]);
            System::delFile($video['path'] . "\\" . $video['file_name']);
            System::delFile(FFMPEG_IMAGE_PATH  . $video['id'] . '.png');
        }
        
        return true;
    }

    /**
     * 修改
     * @param array $insert_args 插入数组
     * @param array $where_arg 条件数组
     * @return int 插入的ID
     */
    public function updateByCondFromDb($insert_args, $where_arg)
    {
        $this->beginTransaction();
        $video = $this->where($where_arg)->getRow();
        $new_tags = $insert_args['tags'] ? explode(',', $insert_args['tags']) : [];
        $old_tags = $video['tags'] ? explode(',', $video['tags']) : [];

        $tag_model = Instance::getMedia('video_tag');
        foreach ($new_tags as $k => $t) {
            if (!in_array($t, $old_tags)) {
                $tag_model->increaseCount($t);
            }
        }
        foreach ($old_tags as $t) {
            if (!in_array($t, $new_tags)) {
                $tag_model->decreaseCount($t);
            }
        }

        if (($file_index = md5($insert_args['path']) . md5($insert_args['file_name'])) != $video['file_index']) {
            $insert_args['file_index'] = $file_index;
            System::moveFile($video['path'] . "\\" . $video['file_name'], $insert_args['path'] . "\\" . $insert_args['file_name'], 0);
        }

        $res = parent::updateByCondFromDb($insert_args, $where_arg);

        $this->commit();

        return $res;
    }

    public function setViewData($id)
    {
        return $this->execSql('update '.$this->table.' set view_count=view_count+1,last_view_time=? where id=?', [get_format_date(),$id]);
    }
}

?>