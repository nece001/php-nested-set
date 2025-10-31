<?php

use Nece\NestedSet\INode;
use Nece\NestedSet\Node as NestedSetNode;

/**
 * 1.创建节点类
 *
 * @author nece001@163.com
 * @create 2025-10-31 22:00:20
 */
class Node implements INode
{
    use NestedSetNode;

    protected $title = '';

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }
}
