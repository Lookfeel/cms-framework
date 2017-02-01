<?php
namespace cms;
use \lookfeel\traits\Instance;

abstract class Model extends \think\Model
{
    use Instance;
    /**
     * 修改记录
     *
     * @param mixed $data
     * @param mixed $map
     * @return number
     */
    public function saveById($data = [], $map = [])
    {
        is_array($map) || $map = [
            'id' => $map
        ];
        return $this->save($data, $map);
    }
    /**
     * 修改记录字段
     *
     * @param int    $id
     * @param        $field
     * @param string $value
     *
     * @return int
     */
    public function modify($id = 0, $field, $value = '')
    {
        return $this->where('id', $id)->setField($field, $value);
    }
    /**
     *
     * 添加记录
     *
     * @param mixed $data
     * @param boolean $return_id
     * @return number
     */
    public function add($data, $return_id = true)
    {
        $ret = $this->save($data);
        return $return_id ? $this->id : $ret;
    }
    /**
     * 删除记录
     *
     * @param mixed $map
     * @param boolean $is_logic
     * @return number
     */
    public function del($map, $is_logic = false)
    {
        is_array($map) || $map = [
            'id' => $map
        ];

        if ($is_logic == false) {
            // 物理删除
            return $this::destroy($map, true);
        } else {
            return $this::destroy($map);
        }
    }
}
