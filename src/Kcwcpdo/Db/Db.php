<?php

namespace Kcwcpdo\Db;



/**

 * DAOPDO Table
 * 未完成，暂时不考虑去写
 */

class Db extends DAOPDO
{

    protected static $self = null;

    protected static $table = '';

    protected static $where = '';


    public static function table($table = '')
    {

        self::$table = $table;
        self::$self = new self();
        return self::$self;
    }
    public function where($where)
    {
        self::$where = $where;
        return self::$self;
    }
    public  function findOne()
    {

        self::$where = self::$where == '' ? '' : " where " . self::$where;
        $rows =  $this->query("select * from " . self::$table . "" . self::$where . " limit 0,1;");
        if (count($rows) >= 1)
            return $rows[0];
        else
            return [];
    }
}
