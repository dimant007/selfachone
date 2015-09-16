<?php

/**
 * Created by cah4a.
 * Time: 17:39
 * Date: 27.05.14
 */
class DbGenerator
{

    private $entityName;

    /** @var fvRoot */
    private $entity;

    /** @var Db_Command[] */
    private $commands = [ ];

    private $status;

    /** @var Db_TableSchema */
    private $tableSchemaLocaled;

    /** @var Db_TableSchema */
    private $tableSchema;

    const STATUS_NOT_EXIST = 1;
    const STATUS_EXIST = 2;
    const STATUS_EXACT = 3;

    function __construct( $entity )
    {
        $this->entityName = $entity;
        $this->entity = new $entity;

        $this->tableSchema = Db_TableSchema::makeFromEntity( $this->entity );

        if( $this->entity->isLanguaged() ){
            $this->tableSchemaLocaled = Db_TableSchema::makeFromEntityLocaled( $this->entity );
        }

        if( Db_Adapter::isTableExists( $this->entity->getTableName() ) ){
            $this->tableExists();
        }
        else {
            $this->tableNotExists();
        }

        /** @var Field_References[] $refs */
        $refs = $this->entity->getFields( "Field_References" );
        foreach( $refs as $key => $ref ){
            //$refEntityShema = fvManagersPool::get( $ref->getReferenceTableName() );
            $refSchema = Db_TableSchema::makeFromFieldReferences( $this->entity, $ref );
            $refTableName = $ref->getReferenceTableName();

            if( Db_Adapter::isTableExists( $refTableName ) ){
                //$this->tableExists();
                $real = Db_TableSchema::makeFromDb( $ref->getReferenceTableName() );
                $diff = Db_TableSchema::getDifference( $refSchema, $real );

                if( ! empty($diff) ){
                    if( $this->status != self::STATUS_NOT_EXIST ){
                        $this->status = self::STATUS_EXIST;
                    }

                    $this->createAlters( $refTableName, $diff );
                }
            }
            else {
                if( $this->status != self::STATUS_NOT_EXIST ){
                    $this->status = self::STATUS_EXIST;
                }
                $this->commands[] = new Db_CreateTableCommand($refTableName, $refSchema);
            }
        }
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getCommands()
    {
        return $this->commands;
    }

    public function perform()
    {
        foreach( $this->getCommands() as $command ){
            fvSite::pdo()->exec( $command );
        }
    }

    private function tableExists()
    {
        $tableName = $this->entity->getTableName();
        $languageTableName = $this->entity->getLanguageTableName();

        $real = Db_TableSchema::makeFromDb( $tableName );
        $diff = Db_TableSchema::getDifference( $this->tableSchema, $real );

        if( empty($diff) ){
            $this->status = self::STATUS_EXACT;
        }
        else {
            $this->status = self::STATUS_EXIST;
            $this->createAlters( $tableName, $diff );
        }

        if( $this->entity->isLanguaged() ){
            if( Db_Adapter::isTableExists( $languageTableName ) ){
                $realLanguaged = Db_TableSchema::makeFromDb( $languageTableName );
                $diff = Db_TableSchema::getDifference( $this->tableSchemaLocaled, $realLanguaged );
                if( ! empty($diff) ){
                    $this->status = self::STATUS_EXIST;
                    $this->createAlters( $languageTableName, $diff );
                }
            }
            else {
                $this->status = self::STATUS_EXIST;
                $this->commands[] = new Db_CreateTableCommand($languageTableName, $this->tableSchemaLocaled);
            }
        }
    }

    private function createAlters( $tableName, $diff )
    {
        if( isset($diff["drop"]["constraints"]) ){
            foreach( $diff["drop"]["constraints"] as $name ){
                $this->commands[] = new Db_Alter_DropConstraint($tableName, $name);
            }
        }

        if( isset($diff["drop"]["fields"]) ){
            foreach( $diff["drop"]["fields"] as $field ){
                $this->commands[] = new Db_Alter_DropColumn($tableName, $field);
            }
        }

        if( isset($diff["add"]["fields"]) ){
            foreach( $diff["add"]["fields"] as $key => $definition ){
                $this->commands[] = new Db_Alter_AddColumn($tableName, $key, $definition);
            }
        }

        if( isset($diff["change"]["fields"]) ){
            foreach( $diff["change"]["fields"] as $key => $definition ){
                $this->commands[] = new Db_Alter_Column($tableName, $key, $definition);
            }
        }

        if( isset($diff["indexes"]) ){
            foreach( $diff["indexes"] as $definition ){
                $this->commands[] = new Db_Alter_Index($tableName, key( $definition ), (array)reset( $definition ));
            }
        }

        if( isset($diff["add"]["constraints"]) ){
            foreach( $diff["add"]["constraints"] as $definition ){
                $this->commands[] = new Db_Alter_AddConstraint($tableName, $definition);
            }
        }
    }

    private function tableNotExists()
    {
        $tableName = $this->entity->getTableName();
        $languageTableName = $this->entity->getLanguageTableName();

        $this->status = self::STATUS_NOT_EXIST;

        $this->commands = [ new Db_CreateTableCommand($tableName, $this->tableSchema) ];
        if( $this->entity->isLanguaged() ){
            $this->commands[] = new Db_CreateTableCommand($languageTableName, $this->tableSchemaLocaled);
        }
    }


}