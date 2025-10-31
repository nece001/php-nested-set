<?php

namespace Nece\NestedSet;

trait Node
{
    protected $id;
    protected $pid;
    protected $lft;
    protected $rit;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    public function setLft($lft)
    {
        $this->lft = $lft;
    }

    public function setRit($rit)
    {
        $this->rit = $rit;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function getLft()
    {
        return $this->lft;
    }

    public function getRit()
    {
        return $this->rit;
    }

    /**
     * 是否有子节点
     *
     * @author gjw
     * @created 2025-10-24 10:53:40
     *
     * @return boolean
     */
    public function hasChild()
    {
        return $this->rit - $this->lft > 1;
    }

    /**
     * 获取节点宽度
     *
     * @author gjw
     * @created 2025-10-24 10:48:33
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->rit - $this->lft + 1;
    }

    /**
     * 获取节点与指定左值的距离
     *
     * @author gjw
     * @created 2025-10-24 10:48:33
     *
     * @param int $lft
     * @return int
     */
    public function getDistance(int $lft)
    {
        return $lft - $this->lft;
    }

    /**
     * 从数组填充属性值
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:34:38
     *
     * @param array $data
     * @return void
     */
    public function fromArray($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * 转换为数组
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:34:38
     *
     * @return array
     */
    public function toArray()
    {
        $data = array();
        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }
}
