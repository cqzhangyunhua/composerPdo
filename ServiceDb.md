# ServiceDb 提供数据操作的服务接口
## 使用

## 配置
```
服务配置文件
conf/servicepdo.ini
    ip=127.0.0.1
    port=9501
数据库连接配置文件
conf/database.ini 此配置与Daopdo.php为同一文件 参考 README.md
在项目根目录public/下新建 servicepdo.php 文件内容如下：
servicepdo.php

<?php
$autoload =  __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    Yaf_Loader::import($autoload);
}
$service = new \Kcwcpdo\Db\ServiceDb();
$service->start();

?>
php service.php
```
## 提供方法

查询
```
URL:/ServiceDbController/query
Post form-data  
            key:sql         Value:select * from test where id=:id
            key:param[:id]  values:1
```
更新、插入、删除
```
插入会返回插入的ID
更新和删除会返回影响多少行
URL:/ServiceDbController/execute
Post form-data  key:sql         Value:insert into test(tel) values(:tel)
                key:param[:tel] Value:333
```

多条SQL的事务 
```
URL:/ServiceDbController/transactionexecute
Post form-data key:sql[] Value:insert into test(tel)values('8888')
               key:sql[] Value:insert into test(tel)values('9999')
注：sql[]为Post数组
```

~~事务~~
~~先调用~~
~~URL:/ServiceDbController/beginTransaction~~
~~URL:/ServiceDbController/query~~
~~URL:/ServiceDbController/execute~~
~~URL:/ServiceDbController/commit~~



~~思路：事务，先生成唯一ID 操作事物都带上，最后提交，其他没有事务的操作与query~~ exec一样


~~URL:/ServiceDbController/beginTransaction~~
~~URL:/ServiceDbController/commit ~~

