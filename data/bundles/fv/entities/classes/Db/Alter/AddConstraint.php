<?php

/**
 * Created by cah4a.
 * Time: 19:04
 * Date: 28.05.14
 */
class Db_Alter_AddConstraint extends Db_Command
{

    function __construct( $tableName, $definition )
    {
        $this->tableName = $tableName;
        $this->definition = $definition;
    }

    function getSql()
    {
        return "ALTER TABLE `{$this->tableName}` ADD " . Db_Adapter::constraintDefinition( $this->tableName, $this->definition );
    }


} 