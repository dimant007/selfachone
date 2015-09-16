<?php

/**
 * Created by cah4a.
 * Time: 19:04
 * Date: 28.05.14
 */
class Db_Alter_DropConstraint extends Db_Command
{

    function __construct( $tableName, $name )
    {
        $this->tableName = $tableName;
        $this->name = $name;
    }

    function getSql()
    {
        return "ALTER TABLE `{$this->tableName}` DROP FOREIGN KEY `{$this->name}`";
    }


} 