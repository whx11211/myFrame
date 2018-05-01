<?php 

/**
 * TestModel类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class RoleModel extends Model
{
    /**
     * 角色表名
     * string
     */
    protected $table = 'role';
    
    /**
     * 菜单表名
     */
    protected $menu_table = 'menu';

    /**
     * 获取全部用户功能菜单
     * @param $role_id  用户的角色id
     */
    public function getAllMenuTrees()
    {
        $Menu = Instance::get($this->menu_table);
        $menu_trees = $Menu->order('sort')->getAll();
        if (!$menu_trees) {
            return null;
        }
    
        return $this->_formatMenu_trees($menu_trees);
    }
    
    
    /**
     * 获取用户功能权限数组
     * @param $role_id  用户的角色id
     */
    public function getAllMenuPermit($role_id)
    {
        $role = $this->where(array('id'=>$role_id))->getAll();
        if (!isset($role[0]['privileges'])) {
            return null;
        }
    
        $sql2 = "SELECT `func` FROM `" . $this->menu_table . "` WHERE `id` in (" . $role[0]['privileges'] .") ORDER BY `sort`";
       
        $menu_trees = $this->getSqlResult($sql2);
        
        foreach ($menu_trees as &$v) {
            $v = $v['func'];
        }
        
        return $menu_trees;
    }
    

    /**
     * 获取用户功能菜单
     * @param $role_id  用户的角色id
     */
    public function getMenuTrees($role_id)
    {
        $role = $this->where(array('id'=>$role_id))->getAll();
        if (!isset($role[0]['privileges'])) {
            return null;
        }
    
        $sql2 = "SELECT * FROM `" . $this->menu_table . "` WHERE `id` in (" . $role[0]['privileges'] .") ORDER BY `sort`";
        $menu_trees = $this->getSqlResult($sql2);
        if (!$menu_trees) {
            return null;
        }
    
        return $this->_formatMenu_trees($menu_trees);
    }
    
    /**
     * 格式化功能列表
     *
     */
    private function _formatMenu_trees($menu_trees, $parent=0)
    {
        $format_trees = array();
        foreach ($menu_trees as $val) {
            if ($val['parent'] == $parent) {
                $sub = $this->_formatMenu_trees($menu_trees, $val['id']);
                if ($sub) {
                    $val['sub'] = $sub;
                }
                $format_trees[] = $val;
            }
        }
        return $format_trees;
    }
}

?>