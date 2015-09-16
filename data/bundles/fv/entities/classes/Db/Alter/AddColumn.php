<?php

/**
 * Created by cah4a.
 * Time: 19:04
 * Date: 28.05.14
 */
class Db_Alter_AddColumn extends Db_Command
{

    function __construct( $tableName, $column, $definition )
    {
        $this->tableName = $tableName;
        $this->column = $column;
        $this->definition = $definition;
    }

    function getSql()
    {
        return "ALTER TABLE `{$this->tableName}` ADD " . Db_Adapter::fieldDefinition( $this->column, $this->definition );
    }


} 