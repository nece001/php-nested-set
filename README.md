# php-nested-set
PHP的数据库左右值无限层级结构算法。

# 说明

```
嵌套集模型（Nested Set Model）是一种高效的树状结构数据模型，由JoeCelko提出并收录于《SQL权威指南》。它使用左值和右值表示节点位置，支持快速查询、节省空间，并简化操作。然而，频繁的树状结构修改会增加更新成本。
```

参考文章：https://www.cnblogs.com/nece001/p/14330452.html

# 使用

src目录实现了算法的抽象，使用时继承抽象并添加几个简单的实现方法即可使用。demo目录中有使用示例，可以按注释中的顺序加以实现。

# 为什么使用仓储模式

主要为了解耦ORM。这里在demo中使用的是ThinkPHP的ORM，或许你想使用Laravel或其它框架，那么只需要使用相应的ORM实现仓储类（demo 中的 NodeRepository类）即可。