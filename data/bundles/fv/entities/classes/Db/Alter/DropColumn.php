<?php

/**
 * Created by cah4a.
 * Time: 19:04
 * Date: 28.05.14
 */
class Db_Alter_DropColumn extends Db_Command
{

    function __construct( $tableName, $column )
    {
        $this->tableName = $tableName;
        $this->column = $column;
    }

    function getSql()
    {
        return "ALTER TABLE `{$this->tableName}` DROP COLUMN `{$this->column}`";
    }


} 