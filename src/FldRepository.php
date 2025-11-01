<?php

namespace Nece\NestedSet;

/**
 * 仓储字段
 *
 * @author nece001@163.com
 * @create 2025-11-01 16:39:00
 */
trait FldRepository
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
}
