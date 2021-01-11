## php 操作mysql PDO composer包类
###  配置
```
php 扩展需求 yaf swoole
```
### 使用例子
#### 默认配置文件写到项目conf/database.ini 如：
```
    Host=192.168.0.247
    Port=3306
    Dbname=test
    Dbcharset=utf8mb4
    Dbuser=admin
    Dbpasswd=g7bPlR/32Xs3w
    
```

本包提供MySQL长连接的服务见 ServiceDb.md 文档

### 初始化：
```
$param=[
    \PDO::ATTR_PERSISTENT=>true, ：在nginx php-fpm 下不要使用
    \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true
];
$db =  Db_DAOPDO::getInstance();
```
### 查询方法：
```
    注：$returnArray为flase 返回指针
    query($strSql, $queryMode = 'All|Row', $returnArray = true, $debug = false)
    例：
        $rows = $db->query("SELECT * FROM test"); 
    find($strSql, $debug = false)
    例：
         $rows = $db->find("select * from test");
```
### 更新方法：
 ```   
    update($table, $arrayDataValue, $where = '', $debug = false)
    例：
        $rows = $db->update("test", ["tel" => "1336"], "id=1");
```
### 插入方法：
```    
    insert($table, $arrayDataValue, $debug = false)
    例：
        $rows = $db->insert("test", ["tel" => "1399999999"]);
```
### 覆盖方式插入方法：
```    
    replace($table, $arrayDataValue, $debug = false)
    例：
        $rows = $db->replace("test", ["tel" => "1399999999"];
```
### 删除方法：
```    
    delete($table, $where = '', $debug = false)
    例：
        $rows = $db->delete("test", "tel= '1399999999'");
```
### 执行SQL方法：
```    
    execSql($strSql, $debug = false)
    例：
         $rows = $db->execSql("insert into test (tel)values('135555')");
```
### 获取字段最大值：
```    
    getMaxValue($table, $field_name, $where = '', $debug = false)
    例：
         $rows = $db->getMaxValue("test", "tel", "id=1");
```
### 获取指定列的数量：
```    
    getCount($table, $field_name, $where = '', $debug = false)
    例：
         $rows = $db->getCount("test", "tel", "id=1");
```
### 获取表引擎
```    
    getTableEngine($dbName, $tableName) 
    例：
        $rows = $db->getTableEngine("test", "test");
```
### 预处理执行和执行预处
```    
    prepareSql($sql = '')
    execute($presql)
    例：
        插入
        $p = $db->prepareSql("insert into test(tel)values(:tel)");
        $rows = $p->execute([":tel" => "132222"]);
        查询
        $result = $db->prepareSql("select * from test where id=:id");
        $result->execute([":id" => 1]);
        $rows  =  $result->fetchAll();
        
```
### pdo属性设置
```    
    setAttribute($p, $d)
    参考pdo手册
```
### 事务
```    
    beginTransaction() 
    commit() 
    rollback()
    例：
        try {
               // $db = \Kcwcpdo\Db\DAOPDO::getInstance();
                $db->beginTransaction();
                $p = $db->prepareSql("insert into test(tel)values(:tel)");
                $rows = $p->execute([":tel" => "131111"]);
                $db->insert("test", ["tel" => "10000"]);
                $db->commit();
            } catch (\Exception $e) {
                $db->rollback();
                echo $e->getMessage();
            }
```            
### 通过事务处理多条SQL语句
```
    execTransaction($arraySql)
    例：
        $rows = $db->execTransaction(["insert into test(tel)values('111')", "insert into test(tel)values('2222')"]);
```
### PDO执行sql语句,返回改变的条数
```
    exec($sql = '')
    例：
         $rows = $db->exec("insert into test(tel)values('333');insert into test(tel)values('444')");
```
