<?php

namespace alat\db\generation;

use alat\domain\models\fields\IntegerField;
use alat\domain\models\fields\ReferenceField;
use alat\Environment;
use alat\repository\Mapper;
use alat\repository\MappingType;

class Table implements \JsonSerializable {
   private $modelType;
   private $name;
   private $columns = array();
   private $fks = array();

   private function __construct() {

   }

   public static function buildSchemas($descriptors) {
      $tables = array();
      $fks = array();
      foreach($descriptors as $descriptor) {
         $table = Table::fromDescriptor($descriptor);
         foreach($descriptors as $ref) {
            $modelType = Table::getModelType($descriptor);
            $refType = Table::getModelType($ref);
            $mappingType = Mapper::getMappingType($refType, $modelType);
            if ($mappingType == MappingType::Association) {
               $name = Mapper::getAssociationTableName($modelType, $refType);
               if (!array_key_exists($name, $tables)) {
                  $associationTable = new Table();
                  $associationTable->name = Mapper::getAssociationTableName($modelType, $refType);
                  $associationTable->columns = array();
                  $column1 = Mapper::getReferenceColumnName($modelType);
                  $column2 = Mapper::getReferenceColumnName($refType);
                  $associationTable->columns[$column1] = new IntegerColumn(new IntegerField($column1, false, null));
                  $associationTable->columns[$column2] = new IntegerColumn(new IntegerField($column2, false, null));

                  $associationTable->fks[Mapper::getReferenceColumnName($modelType)] = \alat\common\Type::stripNamespace($modelType);
                  $associationTable->fks[Mapper::getReferenceColumnName($refType)] = \alat\common\Type::stripNamespace($refType);

                  $tables[$associationTable->getName()] = $associationTable;
               }
            } else if ($mappingType == MappingType::ForeignKey_ParentChild) {
               $columnName = Mapper::getReferenceColumnName($refType);
               $table->columns[$columnName] = new IntegerColumn(new IntegerField($columnName, false, null));
               $table->fks[$columnName] = \alat\common\Type::stripNamespace($refType);
            }
         }

         $tables[$table->getName()] = $table;
      }

      return $tables;
   }

   public static function buildSchemasFromArray($array) {
      $tables = array();
      foreach($array as $item) {
         $table = new Table();

         $table->name = $item['name'];
         $table->columns = array();
         foreach($item['columns'] as $column) {
            $columnObj = Column::fromArray($column);
            $table->columns[$columnObj->getName()] = $columnObj;
         }
         $table->fks = $item['fks'];
         $tables[$table->getName()] = $table;
      }

      return $tables;
   }

   private static function fromDescriptor($descriptor) {
      $table = new Table();
      $table->modelType = Table::getModelType($descriptor);
      $table->name = \alat\common\Type::stripNamespace($table->modelType);

      $table->build($descriptor);
      return $table;
   }

   private static function getModelType($descriptor) {
      return rtrim(get_class($descriptor), 'Descriptor');
   }

   public function getName() {
      return $this->name;
   }

   public function getColumns() {
      return $this->columns;
   }

   public function getFks() {
      return $this->fks;
   }

   public function toCreateSql() {
      $script = 'create table ' . $this->getName() . ' (';
      $script .= Environment::newLine();

      foreach($this->getColumns() as $column) {
         $script .= '   ' . $column->getSql() . ',' . Environment::newLine();
      }

      foreach($this->fks as $column => $table) {
         $script .= '   constraint \'fk_' . $this->getName() . '_' . $table . '\'' . Environment::newLine();
         $script .= '      foreign key (' .  $column . ') references ' . $table . ' (id)' . Environment::newLine();
         $script .= '      on delete cascade' . Environment::newLine();
         $script .= '      on update cascade' . ',' . Environment::newLine();
      }

      $script = rtrim($script);
      $script = rtrim($script, ',');
      $script .= ');';

      return $script;
   }

   public function toUpdateSql($refTable) {
      $removedColumns = array();
      $newColumns = array();
      $updateColumns = array();

      $removedFks = array();
      $addedFks = array();

      $result = '';

      foreach($refTable->columns as $name => $column) {
         if (!array_key_exists($name, $this->columns)) {
            $removedColumns[$name] = $column;
         }
      }

      foreach($this->columns as $name => $column) {
         if (!array_key_exists($name, $refTable->columns)) {
            $newColumns[$name] = $column;
         } else if ($column->getSql() != $refTable->columns[$name]->getSql()) {
            $updateColumns[$name] = $column;
         }
      }

      foreach($refTable->fks as $column => $table) {
         if (!array_key_exists($column, $this->fks)) {
            $removedFks[$column] = $table;
         }
      }

      foreach($this->fks as $column => $table) {
         if (!array_key_exists($column, $refTable->fks)) {
            $removedFks[$column] = $table;
         }
      }

      if (count($removedColumns) > 0
         || count($newColumns) > 0
         || count($updateColumns) > 0
         || count($addedFks) > 0
         || count($removedFks) > 0) {
         $result = 'alter table ' . $this->getName() . ' modify ' . Environment::newLine();

         foreach($removedColumns as $name => $column) {
            $result .= '   drop column ' . $name . ',' . Environment::newLine();
         }

         foreach($newColumns as $name => $column) {
            $result .= '   add column ' . $column->getSql() . ',' . Environment::newLine();
         }

         foreach($updateColumns as $name => $column) {
            $result .= '   modify column ' . $column->getSql() . ',' . Environment::newLine();
         }

         foreach($removedFks as $column => $table) {
            $result .= '   drop foreign key fk_' . $this->getName() . '_' . $table . ',' . Environment::newLine();
         }

         foreach($addedFks as $column => $table) {
            $result .= '   add foreign key fk_' . $this->getName() . '_' . $table . '(' .  $column . ') references ' . $table . ' (id),' . Environment::newLine();
         }

         $result = rtrim($result);
         $result = rtrim($result, ',');
         $result .= ';' . Environment::newLine();
      }

      return $result;
   }

   public function toDropSql() {
      return 'drop table ' . $this->getName() . ';';
   }

   public function jsonSerialize() {
      return [
         'name' => $this->getName(),
         'columns' => $this->getColumns(),
         'fks' => $this->getFks(),
      ];
   }

   private function build($descriptor) {
      $this->columns = array();
      foreach($descriptor->getFields() as $field) {
         if ($field instanceof ReferenceField) {
            $mappingType = Mapper::getMappingType($this->modelType, $field->getReferenceType());
            if ($mappingType == MappingType::ForeignKey_ChildParent) {
               $columnName = Mapper::getReferenceColumnName($field->getReferenceType());
               $this->columns[$columnName] = new IntegerColumn(new IntegerField($columnName, true, null));
            }
         } else {
            $column = Column::fromField($field);
            $this->columns[$column->getName()] = $column;
         }
      }
   }
}