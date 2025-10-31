<?php

namespace Nece\NestedSet;

/**
 * 节点对象接口
 *
 * @author nece001@163.com
 * @create 2025-10-31 21:34:38
 */
interface INode
{
    /**
     * 设置节点ID
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:33:43
     *
     * @param mixed $id
     * @return void
     */
    public function setId($id);

    /**
     * 设置父节点ID
     *
     * @author nece001@163.com
     * @created 2025-10-24 10:53:40
     *
     * @param mixed $pid
     *
     * @return void
     */
    public function setPid($pid);

    /**
     * 设置左边界
     *
     * @author nece001@163.com
     * @created 2025-10-24 10:53:40
     *
     * @param mixed $lft
     *
     * @return void
     */
    public function setLft($lft);

    /**
     * 设置右边界
     *
     * @author nece001@163.com
     * @created 2025-10-24 10:53:40
     *
     * @param mixed $rit
     *
     * @return void
     */
    public function setRit($rit);

    /**
     * 获取节点ID
     *
     * @author nece001@163.com
     * @created 2025-10-24 10:53:40
     *
     * @return mixed
     */
    public function getId();

    /**
     * 获取父节点ID
     *
     * @author nece001@163.com
     * @created 2025-10-24 10:53:40
     *
     * @return mixed
     */
    public function getPid();

    /**
     * 获取左边界
     *
     * @author nece001@163.com
     * @created 2025-10-24 10:53:40
     *
     * @return mixed
     */
    public function getLft();

    /**
     * 获取右边界
     *
     * @author nece001@163.com
     * @created 2025-10-24 10:53:40
     *
     * @return mixed
     */
    public function getRit();

    /**
     * 是否有子节点
     *
     * @author nece001@163.com
     * @created 2025-10-24 10:53:40
     *
     * @return boolean
     */
    public function hasChild();

    /**
     * 获取节点宽度
     *
     * @author nece001@163.com
     * @created 2025-10-24 10:48:33
     *
     * @return int
     */
    public function getWidth();

    /**
     * 获取节点与指定左值的距离
     *
     * @author nece001@163.com
     * @created 2025-10-24 10:48:33
     *
     * @param int $lft 移动目标节点的左值
     * @return int
     */
    public function getDistance(int $lft);

    /**
     * 从数组填充属性值
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:34:38
     *
     * @param array $data
     * @return void
     */
    public function fromArray(array $data);

    /**
     * 转换为数组
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:34:38
     *
     * @return array
     */
    public function toArray();
}
