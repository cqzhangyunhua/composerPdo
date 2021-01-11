<?php
require_once "src\DB\DAOPDO";

$db =  Db_DAOPDO::getInstance("192.168.0.247", "admin", "g7bPlR/32Xs3w", "test", "utf8");
$rows = $db->query("SELECT * FROM test", "Row", false);


foreach ($rows as $row) {
    $id = $row["tel"];
    var_dump($id);
}
            //    var_dump($rs);