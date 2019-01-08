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
        $data = $this->select('tag_id, tag_name, parent_id')->order('tag_id desc')->getAll() ?: [];
        if (!function_exists('tag_map_format')) {
            function tag_map_format($data, $parent_id=0, $parent_ids=[], $pre_attr=0) {
                $format_data = [];
                foreach ($data as $k => $t) {
                    if ($t['parent_id'] == $parent_id) {
                        unset($data[$k]);
                        if ($parent_id) {
                            $parent_ids[] = $parent_id;
                        }
                        $t['pre_attr'] = $pre_attr;
                        $t['parent_ids'] = $parent_ids;
                        $format_data[] = $t;
                        $format_data = array_merge($format_data, tag_map_format($data, $t['tag_id'], $parent_ids, $pre_attr+1));
                    }
                }
                return $format_data;
            }
        }

        $format_data = tag_map_format($data);
        $format_data_ids = array_column($format_data, 'tag_id');
        foreach ($data as $v) {
            if (!in_array($v['tag_id'], $format_data_ids)) {
                $format_data[] = $v;
            }
        }

        return $format_data;
    }
}

?>