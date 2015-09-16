<?php

/**
 * Created by cah4a.
 * Time: 19:04
 * Date: 28.05.14
 */
class Db_Alter_Index extends Db_Command
{

    function __construct( $tableName, $type, $columns )
    {
        $this->tableName = $tableName;
        $this->type = $type;
        $this->columns = $columns;
    }

    function getSql()
    {
        return "ALTER TABLE `{$this->tableName}` ADD " . Db_Adapter::indexDefinition( $this->type, $this->columns );
    }

}