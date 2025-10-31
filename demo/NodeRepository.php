<?php

use Nece\NestedSet\INode;
use Nece\NestedSet\IRepository;
use think\facade\Db;

/**
 * 3.创建仓储对象类
 *
 * @author nece001@163.com
 * @create 2025-10-31 22:04:11
 */
class NodeRepository implements IRepository
{
    public function getFldPid()
    {
        return 'pid';
    }

    public function getFldLft()
    {
        return 'lft';
    }

    public function getFldRit()
    {
        return 'rit';
    }

    public function recordExists($id)
    {
        // 不使用软删除时的查询
        return Tree::where('id', $id)->count() > 0;

        // 使用软删除时的查询
        return Tree::where('id', $id)->withTrashed()->count() > 0;
    }

    public function startTrans()
    {
        Db::startTrans();
    }

    public function commit()
    {
        Db::commit();
    }

    public function rollback()
    {
        Db::rollback();
    }

    public function findById($id)
    {
        $item = Tree::find($id);
        if ($item) {
            $node = new Node();
            $node->fromArray($item->toArray());
            return $node;
        }
        return null;
    }

    public function findRoot()
    {
        $item = Tree::where('pid', 0)->find();
        if ($item) {
            $node = new Node();
            $node->fromArray($item->toArray());
            return $node;
        }
        return null;
    }

    public function delete(INode $node)
    {
        $item = Tree::find($node->getId());
        if ($item) {
            Tree::where('lft', '>=', $item->lft)
                ->where('rit', '<=', $item->rit)
                ->delete();
        }
    }

    public function save(INode $node)
    {
        $item = null;
        if ($node->getId()) {
            $item = Tree::find($node->getId());
        }
        if (!$item) {
            $item = new Tree();
        }
        $data = $node->toArray();
        $item->save($data);
        $node->setId($item->id);
    }

    /**
     * 更新节点的左值和右值
     *
     * @author gjw
     * @created 2025-10-24 11:13:50
     *
     * @param array $where
     * @param int $lft_offset
     * @param int $rit_offset
     * @return void
     */
    public function updateOffset(array $where, $lft_offset = null, $rit_offset = null)
    {
        $data = array();
        if ($lft_offset) {
            $data['lft'] = Db::raw('lft + ' . $lft_offset);
        }
        if ($rit_offset) {
            $data['rit'] = Db::raw('rit + ' . $rit_offset);
        }

        Tree::where($where)->update($data);
    }
}
