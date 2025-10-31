<?php

/**
 * 5.使用示例
 * 仅限于示例，并不能直接运行
 */

use Nece\NestedSet\NodeService;

// 创建树服务对象
$service = new TreeService();

// 创建第1个节点
$node1 = $service->createNode();
$node1->setTitle('Node 1');
$service->save($node1);

// 创建第2个节点
$node2 = $service->createNode();
$node2->setTitle('Node 2');
$service->save($node2);

// 创建第3个节点
$node3 = $service->createNode();
$node3->setTitle('Node 3');
$service->save($node3);

// 创建第4个节点
$node4 = $service->createNode();
$node4->setTitle('Node 4');
$service->save($node4);

// 删除节点4
$service->delete($node4);

// 节点3移动到节点1后面
$service->move($node3, $node1, NodeService::POSITION_BEFORE);
