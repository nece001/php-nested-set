<?php

namespace Nece\NestedSet;

use Throwable;

/**
 * 节点的操作逻辑
 *
 * @author nece001@163.com
 * @create 2025-10-31 22:14:43
 */
abstract class NodeService
{
    // 位置常量
    const POSITION_BEFORE = 1; // 节点之前
    const POSITION_AFTER = 2; // 节点之后
    const POSITION_FIRSTT_CHILD = 3; // 第一个子节点
    const POSITION_LAST_CHILD = 4; // 最后一个子节点

    /**
     * 仓储对象
     *
     * @var IRepository
     */
    protected $repository;

    /**
     * 构造函数
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:45:33
     */
    public function __construct()
    {
        $this->repository = $this->getRepository();
    }

    /**
     * 获取仓储对象，返回仓储对象实例
     * 示例：return new Repository();
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:45:45
     *
     * @return IRepository
     */
    abstract protected function getRepository();

    /**
     * 创建一个空节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:53:23
     *
     * @return INode
     */
    abstract protected function createNode();

    /**
     * 创建根节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 22:11:36
     *
     * @return INode
     */
    abstract protected function createRootNode();

    /**
     * 查询节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:53:23
     *
     * @param int $id
     * @return INode|null
     */
    public function findNode($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * 删除节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:53:23
     *
     * @param INode $node
     * @param bool $force
     * @return void
     */
    public function delete(INode $node, $force = false)
    {
        if (!$node->getPid()) {
            throw new NodeException('根节点不能删除', 1001);
        }

        if (!$force && $node->hasChild()) {
            throw new NodeException('节点下有子节点，不能删除', 1002);
        }

        $this->repository->startTrans();

        try {
            $this->repository->delete($node);

            // 记录是否还存在，存在的是软删除，不存在的是物理删除，物理删除的要收缩空出来的位置
            $exists = $this->repository->recordExists($node->getId());
            if (!$exists) {
                $width = $node->getWidth();
                $lft = $this->repository->getFldLft();
                $rit = $this->repository->getFldRit();

                $where = array(
                    [$lft, '<', $node->getLft()],
                    [$rit, '>', $node->getRit()]
                );
                $this->repository->updateOffset($where, -$width, -$width);

                $where = array(
                    [$lft, '>', $node->getRit()],
                );
                $this->repository->updateOffset($where, -$width, -$width);
            }
            $this->repository->commit();
        } catch (Throwable $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * 保存节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:53:23
     *
     * @param INode $node
     * @return void
     */
    public function save($node)
    {
        $lft = $this->repository->getFldLft();
        $rit = $this->repository->getFldRit();

        $id = $node->getId();
        $pid = $node->getPid();
        $parent = $this->findParentNode($pid);

        $this->repository->startTrans();

        try {
            if ($id) {
                $raw = $this->repository->findById($id);
                if (!$raw) {
                    throw new NodeException('节点不存在', 1003);
                }

                if ($raw->getPid() != $node->getPid()) {
                    $this->move($node, $parent, self::POSITION_LAST_CHILD);
                    $fresh = $this->repository->findById($node->getId());

                    $node->setLft($fresh->getLft());
                    $node->setRit($fresh->getRit());
                }
            } else {
                $node->setPid($parent->getId());
                $node->setLft($parent->getRit());
                $node->setRit($parent->getRit() + 1);
                $width = $node->getWidth();

                $where = array(
                    [$lft, '<=', $parent->getLft()],
                    [$rit, '>=', $parent->getRit()]
                );
                $this->repository->updateOffset($where, null, $width);

                $where = array(
                    [$lft, '>', $node->getLft()],
                );
                $this->repository->updateOffset($where, $width, $width);
            }

            $this->repository->save($node);
            $this->repository->commit();
        } catch (Throwable $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * 移动节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:53:23
     *
     * @param INode $node
     * @param INode $target
     * @param int $position
     * @return void
     */
    public function move($node, $target, int $position)
    {
        // 防止节点移动到自身
        if ($node->getId() != $target->getId()) {

            $this->repository->startTrans();

            try {
                if ($position == self::POSITION_BEFORE) {
                    if ($node->getRit() - $target->getLft() != 1) {
                        $this->moveToBefore($node, $target);
                    }
                } elseif ($position == self::POSITION_AFTER) {
                    if ($target->getRit() - $node->getLft() != 1) {
                        $this->moveToAfter($node, $target);
                    }
                } elseif ($position == self::POSITION_FIRSTT_CHILD) {
                    if ($node->getLft() - $target->getLft() != 1) {
                        $this->moveToFirstChild($node, $target);
                    }
                } elseif ($position == self::POSITION_LAST_CHILD) {
                    if ($node->getRit() - $target->getRit() != 1) {
                        $this->moveToLastChild($node, $target);
                    }
                }

                $this->repository->commit();
            } catch (Throwable $e) {
                $this->repository->rollback();
                throw $e;
            }
        }
    }

    /**
     * 查询父节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:53:23
     *
     * @param int $pid
     * @return INode
     */
    protected function findParentNode($pid)
    {
        if (!$pid) {
            $parent = $this->repository->findRoot();
            if (!$parent) {
                $parent = $this->insertRootNode();
            }
            return $parent;
        }

        $parent = $this->repository->findById($pid);
        if (!$parent) {
            throw new NodeException('父节点不存在', 1004);
        }
        return $parent;
    }

    /**
     * 插入根节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:54:46
     *
     * @return INode
     */
    protected function insertRootNode()
    {
        $root = $this->createRootNode();
        $root->setPid(0);
        $root->setLft(1);
        $root->setRit(2);
        $this->repository->save($root);
        return $root;
    }

    /**
     * 移到目标节点前面
     *
     * @author nece001@163.com
     * @created 2025-10-31 21:41:34
     *
     * @param INode $node
     * @param INode $target
     * @return void
     */
    private function moveToBefore($node, $target)
    {
        $width = $node->getWidth();
        $lft = $this->repository->getFldLft();
        $rit = $this->repository->getFldRit();

        $where = array(
            [$lft, '<', $target->getLft()],
            [$rit, '>', $target->getRit()]
        );
        $this->repository->updateOffset($where, null, $width);

        $where = array(
            [$lft, '>=', $target->getLft()],
        );
        $this->repository->updateOffset($where, $width, $width);

        $distance = $node->getDistance($target->getLft());
        $offset = 0;
        if ($distance < 0) {
            $offset = $width;
            $distance -= $width;
        }
        $where = array(
            [$lft, '>=', $node->getLft() + $offset],
            [$rit, '<=', $node->getRit() + $offset],
        );
        $this->repository->updateOffset($where, $distance, $distance);

        $where = array(
            [$lft, '<', $node->getLft() + $offset],
            [$rit, '>', $node->getRit() + $offset]
        );
        $this->repository->updateOffset($where, null, -$width);

        $where = array(
            [$lft, '>', $node->getRit() + $offset],
        );
        $this->repository->updateOffset($where, -$width, -$width);
    }

    /**
     * 移到目标节点后面
     *
     * @author nece001@163.com
     * @created 2025-10-31 21:41:34
     *
     * @param INode $node
     * @param INode $target
     * @return void
     */
    private function moveToAfter($node, $target)
    {
        $width = $node->getWidth();
        $lft = $this->repository->getFldLft();
        $rit = $this->repository->getFldRit();

        $where = array(
            [$lft, '<', $target->getLft()],
            [$rit, '>', $target->getRit()]
        );
        $this->repository->updateOffset($where, null, $width);

        $where = array(
            [$lft, '>', $target->getRit()],
        );
        $this->repository->updateOffset($where, $width, $width);

        $distance = $node->getDistance($target->getLft());
        $offset = 0;
        if ($distance < 0) {
            $offset = $width;
            $distance = $distance - $width + $target->getWidth();
        } else {
            $distance += $target->getWidth();
        }
        $where = array(
            [$lft, '>=', $node->getLft() + $offset],
            [$rit, '<=', $node->getRit() + $offset],
        );
        $this->repository->updateOffset($where, $distance, $distance);

        $where = array(
            [$lft, '<', $node->getLft() + $offset],
            [$rit, '>', $node->getRit() + $offset]
        );
        $this->repository->updateOffset($where, null, -$width);

        $where = array(
            [$lft, '>', $node->getRit() + $offset],
        );
        $this->repository->updateOffset($where, -$width, -$width);
    }

    /**
     * 移为目标节点首子节点
     *
     * @author nece001@163.com
     * @created 2025-10-31 21:41:34
     *
     * @param INode $node
     * @param INode $target
     * @return void
     */
    private function moveToFirstChild($node, $target)
    {
        $width = $node->getWidth();
        $lft = $this->repository->getFldLft();
        $rit = $this->repository->getFldRit();

        $where = array(
            [$lft, '<=', $target->getLft()],
            [$rit, '>=', $target->getRit()]
        );
        $this->repository->updateOffset($where, null, $width);

        $where = array(
            [$lft, '>', $target->getLft()],
        );
        $this->repository->updateOffset($where, $width, $width);

        $distance = $node->getDistance($target->getLft());
        $offset = 0;
        if ($distance < 0) { // 从后往前
            $offset = $width;
            $distance = $distance - $width + 1;
        } else {
            $distance += 1;
        }
        $where = array(
            [$lft, '>=', $node->getLft() + $offset],
            [$rit, '<=', $node->getRit() + $offset],
        );
        $this->repository->updateOffset($where, $distance, $distance);

        $where = array(
            [$lft, '<', $node->getLft() + $offset],
            [$rit, '>', $node->getRit() + $offset]
        );
        $this->repository->updateOffset($where, null, -$width);

        $where = array(
            [$lft, '>', $node->getRit() + $offset],
        );
        $this->repository->updateOffset($where, -$width, -$width);
    }

    /**
     * 移为目标节点末子节点
     *
     * @author nece001@163.com
     * @created 2025-10-31 21:41:34
     *
     * @param INode $node
     * @param INode $target
     * @return void
     */
    private function moveToLastChild($node, $target)
    {
        $width = $node->getWidth();
        $lft = $this->repository->getFldLft();
        $rit = $this->repository->getFldRit();

        $where = array(
            [$lft, '<=', $target->getLft()],
            [$rit, '>=', $target->getRit()]
        );
        $this->repository->updateOffset($where, null, $width);

        $where = array(
            [$lft, '>', $target->getRit()], // 与移到节点首子的差别
        );
        $this->repository->updateOffset($where, $width, $width);

        $distance = $node->getDistance($target->getLft());
        $offset = 0;
        if ($distance < 0) {
            $offset = $width;
            $distance = $distance - $width + ($target->getWidth() - 1);
        } else {
            $distance = $distance + $target->getWidth() - 1;
        }
        $where = array(
            [$lft, '>=', $node->getLft() + $offset],
            [$rit, '<=', $node->getRit() + $offset],
        );
        $this->repository->updateOffset($where, $distance, $distance);

        $where = array(
            [$lft, '<', $node->getLft() + $offset],
            [$rit, '>', $node->getRit() + $offset]
        );
        $this->repository->updateOffset($where, null, -$width);

        $where = array(
            [$lft, '>', $node->getRit() + $offset],
        );
        $this->repository->updateOffset($where, -$width, -$width);
    }
}
