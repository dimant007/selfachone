<?php

/**
 * Created by cah4a.
 * Time: 13:28
 * Date: 28.05.14
 */
class Db_TableSchema
{

    private $fields = [ ];
    private $indexes = [ ];
    private $constraints = [ ];

    const INDEX_PRIMARY = 1;
    const INDEX_UNIQUE = 2;
    const INDEX_KEY = 3;

    static function makeFromDb( $tableName )
    {
        $statement = fvSite::pdo()->prepare( "SHOW COLUMNS FROM {$tableName}" );
        $statement->execute();
        $res = $statement->fetchAll( PDO::FETCH_ASSOC );

        $schema = new Db_TableSchema();

        $schema->fields = [ ];
        foreach( $res as $field ){
            $key = $field["Field"];

            $unsigned = strpos( $field["Type"], "unsigned" ) > 0;
            $type = trim( str_replace( "unsigned", "", $field["Type"] ) );

            $nullable = $field["Null"] == "YES";
            $default = $field["Default"];
            $autoincrement = $field["Extra"] == "auto_increment";

            if( is_null( $default ) && ! $autoincrement && $nullable ){
                $default = "NULL";
            } elseif( strlen($default) > 0 ) {
                $default = '"' . $default . '"';
            }

            $schema->fields[$key] = [
                "type" => $type,
                "unsigned" => $unsigned,
                "nullable" => $nullable,
                "default" => $default,
                "autoincrement" => $autoincrement
            ];
        }

        $statement = fvSite::pdo()->prepare( "SHOW KEYS FROM {$tableName}" );
        $statement->execute();
        $res = $statement->fetchAll( PDO::FETCH_ASSOC );

        $indexes = [ ];
        foreach( $res as $index ){
            $type = self::INDEX_KEY;
            if( ! $index["Non_unique"] ){
                if( $index['Key_name'] == "PRIMARY" ){
                    $type = self::INDEX_PRIMARY;
                }
                else {
                    $type = self::INDEX_UNIQUE;
                }
            }
            $indexes[$type][$index['Key_name']][$index['Seq_in_index']] = $index['Column_name'];
        }

        foreach( $indexes as $type => $indexesByTypes ){
            foreach( $indexesByTypes as $index ){
                $fields = array_values( $index );
                if( count( $fields ) == 1 ){
                    $fields = reset( $fields );
                }

                $schema->indexes[] = [ $type => $fields ];
            }
        }

        $statement = fvSite::pdo()->prepare( "
            select
                k.constraint_name,
                k.column_name,
                k.referenced_table_name,
                k.referenced_column_name,
                c.update_rule,
                c.delete_rule
            from
                information_schema.key_column_usage k
                JOIN
                information_schema.referential_constraints c ON
                ( k.table_schema = c.constraint_schema AND c.constraint_name = k.constraint_name )
            where
                k.referenced_table_name is not null
                and k.table_schema = DATABASE()
                and k.table_name = '{$tableName}'" );

        $statement->execute();
        $res = $statement->fetchAll( PDO::FETCH_ASSOC );

        foreach( $res as $constraint ){
            $schema->constraints[$constraint["constraint_name"]] = [
                "from" => $constraint["column_name"],
                "table" => $constraint["referenced_table_name"],
                "to" => $constraint["referenced_column_name"],
                "update" => self::rule( $constraint["update_rule"] ),
                "delete" => self::rule( $constraint["delete_rule"] ),
            ];
        }

        return $schema;
    }

    static private function rule( $string )
    {
        switch( trim( strtolower( $string ) ) ){
            case "cascade":
                return Field_Foreign::RULE_CASCADE;
            case "restrict":
                return Field_Foreign::RULE_RESTRICT;
            case "set null":
                return Field_Foreign::RULE_SET_NULL;
            case "no action":
                return Field_Foreign::RULE_NO_ACTION;
        }
        return null;
    }

    static function makeFromEntityLocaled( fvRoot $entity )
    {
        $schema = new Db_TableSchema();

        $schema->fields[$entity->getPkName()] = [
            "type" => Db_Adapter::getPkType(),
            "unsigned" => true,
            "nullable" => false,
            "default" => null,
            "autoincrement" => false,
        ];

        $schema->fields["languageId"] = [
            "type" => Db_Adapter::getPkType(),
            "unsigned" => true,
            "nullable" => false,
            "default" => null,
            "autoincrement" => false,
        ];

        foreach( $entity->getFields() as $key => $field ){
            if( ! $field->isLanguaged() ){
                continue;
            }

            $schema->fields[$key] = [
                "type" => Db_Adapter::getTypeForField( $field ),
                "unsigned" => Db_Adapter::getIsUnsigned( $field ),
                "nullable" => $field->isNullable(),
                "default" => Db_Adapter::getDefaultValue( $field ),
                "autoincrement" => false
            ];
        }

        $schema->indexes[] = [ self::INDEX_PRIMARY => [ $entity->getPkName(), "languageId" ] ];

        $language = new Language();
        $schema->constraints[] = [
            "from" => $entity->getPkName(),
            "to" => $entity->getPkName(),
            "table" => $entity->getTableName(),
            "update" => Field_Foreign::RULE_CASCADE,
            "delete" => Field_Foreign::RULE_CASCADE
        ];

        $schema->constraints[] = [
            "from" => "languageId",
            "to" => $language->getPkName(),
            "table" => $language->getTableName(),
            "update" => Field_Foreign::RULE_CASCADE,
            "delete" => Field_Foreign::RULE_RESTRICT
        ];

        return $schema;
    }

    static function makeFromEntity( fvRoot $entity )
    {
        $schema = new Db_TableSchema();

        $schema->fields[$entity->getPkName()] = [
            "type" => Db_Adapter::getPkType(),
            "unsigned" => true,
            "nullable" => false,
            "default" => null,
            "autoincrement" => true
        ];

        if( $entity->getSubclassKeyName() ){
            $schema->fields[$entity->getSubclassKeyName()] = [
                "type" => Db_Adapter::getSubclassType(),
                "unsigned" => false,
                "nullable" => false,
                "default" => null,
                "autoincrement" => false
            ];
        }

        $schema->indexes[] = [ self::INDEX_PRIMARY => $entity->getPkName() ];

        self::parseFields( $entity->getFields(), $schema );

        if( $entity->getSubclassKeyName() ){
            foreach( $entity->getSubclasses() as $subClassName ){
                /** @var fvRoot $subclass */
                $subclass = new $subClassName;
                self::parseFields( $subclass->getFields(), $schema );
            }
        }

        return $schema;
    }

    /**
     * @param fvField[] $fields
     * @param Db_TableSchema $schema
     * @throws Exception
     */
    private static function parseFields( array $fields, Db_TableSchema $schema ){
        foreach( $fields as $key => $field ){
            if( $field->isLanguaged() ){
                continue;
            }

            if( $field instanceof Field_Virtual ){
                continue;
            }

            $schema->fields[$key] = [
                "type" => Db_Adapter::getTypeForField( $field ),
                "unsigned" => Db_Adapter::getIsUnsigned( $field ),
                "nullable" => $field->isNullable(),
                "default" => Db_Adapter::getDefaultValue( $field ),
                "autoincrement" => false
            ];
        }

        foreach( $fields as $field ){
            if( $field->isLanguaged() ){
                continue;
            }

            if( $field->isUnique() ){
                $schema->indexes[] = [ self::INDEX_UNIQUE => $field->getKey() ];
            }
        }

        foreach( $fields as $key => $field ){
            if( ! $field instanceof Field_Foreign ){
                continue;
            }

            $schema->constraints[] = [
                "from" => $field->getKey(),
                "to" => $field->getForeignEntityPkName(),
                "table" => $field->getForeignEntityTableName(),
                "update" => $field->getOnUpdate(),
                "delete" => $field->getOnDelete(),
            ];
        }
    }

    static function getDifference( Db_TableSchema $need, Db_TableSchema $real )
    {
        $diff = [ ];

        foreach( $need->fields as $key => $definition ){
            if( ! isset($real->fields[$key]) ){
                $diff["add"]["fields"][$key] = $definition;
            }
            else {
                if( $definition != $real->fields[$key] ){
                    $diff["change"]["fields"][$key] = $definition;
                }
            }
        }

        foreach( $real->fields as $key => $definition ){
            if( ! isset($need->fields[$key]) ){
                $diff["drop"]["fields"][] = $key;
            }
        }

        foreach( $need->indexes as $definition ){
            foreach( $real->indexes as $index ){
                if( $index == $definition ){
                    continue 2;
                }
            }
            $diff["indexes"][] = $definition;
        }

        foreach( $need->constraints as $definition ){
            foreach( $real->constraints as $keyName => $constraint ){
                if( $constraint == $definition ){
                    continue 2;
                }

                if( $constraint["from"] == $definition["from"] ){
                    $diff["drop"]["constraints"][] = $keyName;
                    break;
                }
            }
            $diff["add"]["constraints"][] = $definition;
        }

        return $diff;
    }

    public static function makeFromFieldReferences( fvRoot $entity, Field_References $field )
    {
        $schema = new Db_TableSchema();

        $schema->fields[$field->getForeignEntityKey()] = [
            "type" => Db_Adapter::getPkType(),
            "unsigned" => true,
            "nullable" => false,
            "default" => null,
            "autoincrement" => false
        ];

        $schema->fields[$field->getCurrentEntityKey()] = [
            "type" => Db_Adapter::getPkType(),
            "unsigned" => true,
            "nullable" => false,
            "default" => null,
            "autoincrement" => false
        ];

        $schema->constraints[] = [
            "from" => $field->getCurrentEntityKey(),
            "to" => $entity->getPkName(),
            "table" => $entity->getTableName(),
            "update" => Field_Foreign::RULE_CASCADE,
            "delete" => Field_Foreign::RULE_CASCADE,
        ];

        $schema->constraints[] = [
            "from" => $field->getForeignEntityKey(),
            "to" => $field->getForeignEntityPkName(),
            "table" => $field->getForeignEntityTableName(),
            "update" => Field_Foreign::RULE_CASCADE,
            "delete" => Field_Foreign::RULE_CASCADE,
        ];

        $fields = [
            $field->getCurrentEntityKey(),
            $field->getForeignEntityKey()
        ];
        sort( $fields );

        $schema->indexes[] = [ self::INDEX_PRIMARY => $fields ];
        $schema->indexes[] = [ self::INDEX_KEY => array_reverse( $fields ) ];

        return $schema;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getIndexes()
    {
        return $this->indexes;
    }

    public function getConstraints()
    {
        return $this->constraints;
    }

} 