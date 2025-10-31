<?php

use Nece\NestedSet\NodeService;

/**
 * 4.创建树服务对象类
 *
 * @author nece001@163.com
 * @create 2025-10-31 22:09:36
 */
class TreeService extends NodeService
{
    /**
     * 获取仓储对象
     *
     * @author nece001@163.com
     * @create 2025-10-31 22:04:11
     *
     * @return NodeRepository
     */
    public function getRepository()
    {
        return new NodeRepository();
    }

    /**
     * 创建节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 22:09:36
     *
     * @return Node
     */
    public function createNode()
    {
        return new Node();
    }

    /**
     * 创建根节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 22:13:54
     *
     * @return Node
     */
    public function createRootNode()
    {
        $node = $this->createNode();
        $node->setTitle('Root');
        return $node;
    }
}
