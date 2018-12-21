<?php 

/**
 * VideoTagMedia
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class VideoTagMedia extends Media
{
    /**
     * 表名
     * string
     */
    protected $table = 'video_tag';

    /**
     * 视频数量减1
     * @param $tags array|string
     * @return int
     */
    public function decreaseCount($tags)
    {
        $affect_row = 0;
        if (is_array($tags)) {
            foreach ($tags as $tag_id) {
                $affect_row += $this->execSql('update '.$this->table.' set video_count=video_count-1 where tag_id=?', array($tag_id));
            }
        }
        else {
            $affect_row += $this->execSql('update '.$this->table.' set video_count=video_count-1 where tag_id=?', array($tags));
        }
        return $affect_row;
    }

    /**
     * 视频数量加1
     * @param $tags array|string
     * @return int
     */
    public function increaseCount($tags)
    {
        $affect_row = 0;
        if (is_array($tags)) {
            foreach ($tags as $tag_id) {
                $affect_row += $this->execSql('update '.$this->table.' set video_count=video_count+1 where tag_id=?', array($tag_id));
            }
        }
        else {
            $affect_row += $this->execSql('update '.$this->table.' set video_count=video_count+1 where tag_id=?', array($tags));
        }
        return $affect_row;
    }

    /**
     * 搜索量加1
     * @param $tags array|string
     * @return int
     */
    public function increaseSearchCount($tags)
    {
        $affect_row = 0;
        if (is_array($tags)) {
            foreach ($tags as $tag_id) {
                $affect_row += $this->execSql('update '.$this->table.' set search_count=search_count+1 where tag_id=?', array($tag_id));
            }
        }
        else {
            $affect_row += $this->execSql('update '.$this->table.' set search_count=search_count+1 where tag_id=?', array($tags));
        }
        return $affect_row;
    }

    public function getTagMap()
    {
        return $this->select('tag_id, tag_name')->getAll();
    }
}

?>