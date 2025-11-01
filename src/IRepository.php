<?php

namespace Nece\NestedSet;

/**
 * 仓储对象接口
 *
 * @author nece001@163.com
 * @create 2025-10-31 21:43:23
 */
interface IRepository
{

    /**
     * 获取父级ID字段名
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:56:19
     *
     * @return string
     */
    public function getFldPid();

    /**
     * 获取左值字段名
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:56:19
     *
     * @return string
     */
    public function getFldLft();

    /**
     * 获取右值字段名
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:56:19
     *
     * @return string
     */
    public function getFldRit();

    /**
     * 开启事务
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:41:34
     *
     * @return void
     */
    public function startTrans();

    /**
     * 提交事务
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:41:34
     *
     * @return void
     */
    public function commit();

    /**
     * 回滚事务
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:41:34
     *
     * @return void
     */
    public function rollback();

    /**
     * 根据ID查询节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:41:34
     *
     * @param int $id
     * @return INode|null
     */
    public function findById($id);

    /**
     * 查询根节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:41:34
     *
     * @return INode|null
     */
    public function findRoot();

    /**
     * 记录是否存在，包含软删除的记录
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:50:34
     *
     * @param int $id
     * @return bool
     */
    public function recordExists($id);

    /**
     * 删除节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:41:34
     *
     * @param INode $node
     * @return void
     */
    public function delete($node);

    /**
     * 保存节点
     *
     * @author nece001@163.com
     * @create 2025-10-31 21:41:34
     *
     * @param INode $node
     * @return void
     */
    public function save($node);

    /**
     * 更新节点的左值和右值
     *
     * @author nece001@163.com
     * @created 2025-10-31 21:41:34
     *
     * @param array $where
     * @param int $lft_offset
     * @param int $rit_offset
     * @return void
     */
    public function updateOffset(array $where, $lft_offset = null, $rit_offset = null);

    /**
     * 获取子节点列表
     *
     * @author nece001@163.com
     * @create 2025-10-31 22:41:56
     *
     * @param int $lft 父级的左值
     * @param int $rit 父级的右值
     * @param int $level 获取第几层的子类
     * @return array
     */
    public function childList($lft, $rit, $level = 0);
}
