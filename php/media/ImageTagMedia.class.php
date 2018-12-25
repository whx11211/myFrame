<?php 

/**
 * ImageTagMedia
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class ImageTagMedia extends Media
{
    /**
     * 表名
     * string
     */
    protected $table = 'image_tag';

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
                $affect_row += $this->execSql('update '.$this->table.' set image_count=image_count-1 where tag_id=?', array($tag_id));
            }
        }
        else {
            $affect_row += $this->execSql('update '.$this->table.' set image_count=image_count-1 where tag_id=?', array($tags));
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
                $affect_row += $this->execSql('update '.$this->table.' set image_count=image_count+1 where tag_id=?', array($tag_id));
            }
        }
        else {
            $affect_row += $this->execSql('update '.$this->table.' set image_count=image_count+1 where tag_id=?', array($tags));
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
        $data = $this->select('tag_id, tag_name, parent_id')->getAll() ?: [];
        if (!function_exists('get_parents')) {
            function get_parents($parent_id, $data, $parent_ids=[]) {
                foreach ($data as $t) {
                    if ($t['tag_id'] == $parent_id && !in_array($t['tag_id'], $parent_ids)) {
                        $parent_ids[] = $parent_id;
                        $parent_ids = get_parents($t['parent_id'], $data, $parent_ids);
                    }
                }
                return $parent_ids;
            }
        }

        foreach ($data as &$v) {
            $v['parent_ids'] = get_parents($v['parent_id'], $data);
        }

        return $data;
    }
}

?>