<?php

/**
 * Created by cah4a.
 * Time: 16:41
 * Date: 28.05.14
 */
class Db_CreateTableCommand extends Db_Command
{

    /** @var Db_TableSchema */
    private $schema;

    function __construct( $tableName, Db_TableSchema $schema )
    {
        $this->tableName = $tableName;
        $this->schema = $schema;
    }

    function getSql()
    {
        return "CREATE TABLE `{$this->tableName}` (\n\t{$this->getDefinitions()}\n) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    }

    private function getDefinitions()
    {
        $definitions = $this->getFieldsDefinition();
        $definitions = array_merge( $definitions, $this->getIndexesDefinitions() );
        $definitions = array_merge( $definitions, $this->getConstraintDefinitions() );
        return implode( ",\n\t", $definitions );
    }

    private function getIndexesDefinitions()
    {
        $indexes = [ ];
        foreach( $this->schema->getIndexes() as $index ){
            $indexes[] = Db_Adapter::indexDefinition( key( $index ), (array)reset( $index ) );
        }
        return $indexes;
    }

    /**
     * @return array
     */
    private function getFieldsDefinition()
    {
        $fields = [ ];
        foreach( $this->schema->getFields() as $key => $field ){
            $fields[] = Db_Adapter::fieldDefinition( $key, $field );
        }
        return $fields;
    }

    private function getConstraintDefinitions()
    {
        $constraints = [ ];
        foreach( $this->schema->getConstraints() as $constraint ){
            $constraints[] = Db_Adapter::constraintDefinition( $this->tableName, $constraint );
        }
        return $constraints;
    }
}