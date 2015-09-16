<?php

/**
 * Created by cah4a.
 * Time: 13:48
 * Date: 28.05.14
 */
class Db_Adapter
{

    static function getPkType()
    {
        return "int(11)";
    }

    static function isTableExists( $tableName )
    {
        $statement = fvSite::pdo()->prepare( "SHOW TABLES LIKE '{$tableName}';" );
        $statement->execute();
        $res = $statement->fetchAll( PDO::FETCH_ASSOC );

        return count( $res ) > 0;
    }

    static function getTypeForField( fvField $field )
    {
        if( $field instanceof Field_Int ){
            return "int(11)";
        }

        if( $field instanceof Field_Text ){
            return "text";
        }

        if( $field instanceof Field_String ){
            return "varchar(255)";
        }

        if( $field instanceof Field_Bool ){
            return "tinyint(1)";
        }

        if( $field instanceof Field_Date ){
            return "date";
        }

        if( $field instanceof Field_Datetime ){
            return "datetime";
        }

        if( $field instanceof Field_Float ){
            return "double";
        }

        throw new Exception("Unknown type for field " . get_class( $field ));
    }

    static function getIsUnsigned( $field )
    {
        return $field instanceof Field_Price || $field instanceof Field_Foreign;
    }

    public static function getDefaultValue( fvField $field )
    {
        $defaultValue = $field->getDefaultValue();

        if( is_null( $defaultValue ) && $field->isNullable() ){
            return "NULL";
        }

        if( is_string( $defaultValue ) ){
            return '"' . $defaultValue . '"';
        }

        return $defaultValue;
    }

    static function fieldDefinition( $key, $field )
    {
        $type = $field["type"];

        $pipe = [
            "`{$key}`",
            $type
        ];

        if( $field["unsigned"] ){
            $pipe[] = "unsigned";
        }

        if( ! $field["nullable"] ){
            $pipe[] = "NOT NULL";
        }

        if( $field["autoincrement"] ){
            $pipe[] = "auto_increment";
        }
        else {
            if( $field["default"] ){
                $pipe[] = "DEFAULT " . $field["default"];
            }
        }

        return implode( " ", $pipe );
    }

    static function indexDefinition( $type, array $fields )
    {
        $keyName = strtolower( implode( "_", $fields ) );
        $fields = "`" . implode( "`, `", $fields ) . "`";

        switch( $type ){
            case Db_TableSchema::INDEX_PRIMARY:
                return "PRIMARY KEY ({$fields})";
            case Db_TableSchema::INDEX_UNIQUE:
                return "UNIQUE KEY `{$keyName}` ({$fields})";
            default:
                return "KEY `{$keyName}` ({$fields})";
        }
    }

    static function constraintDefinition( $prefix, $definition )
    {
        $name = strtolower( $prefix . "_fk_" . $definition["from"] );

        $update = "ON UPDATE " . self::constraintRule( $definition["update"] );
        $delete = "ON DELETE " . self::constraintRule( $definition["delete"] );

        return "CONSTRAINT `{$name}` FOREIGN KEY (`{$definition["from"]}`) REFERENCES `{$definition["table"]}` (`{$definition["to"]}`) \n\t{$update} {$delete}";
    }

    private static function constraintRule( $value )
    {
        switch( $value ){
            case Field_Foreign::RULE_SET_NULL;
                return "SET NULL";
            case Field_Foreign::RULE_RESTRICT;
                return "RESTRICT";
            case Field_Foreign::RULE_NO_ACTION;
                return "NO ACTION";
            default:
                return "CASCADE";
        }
    }

    public static function getSubclassType()
    {
        return "varchar(255)";
    }

} 