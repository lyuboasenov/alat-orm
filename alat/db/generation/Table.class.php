<?php

namespace alat\db\generation;

use alat\domain\models\fields\IntegerField;
use alat\domain\models\fields\ReferenceField;
use alat\Environment;
use alat\repository\Mapper;
use alat\repository\MappingType;

class Table implements \JsonSerializable {
   private $descriptor;

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
                  $associationTable->columns[] = new IntegerColumn(new IntegerField(Mapper::getReferenceColumnName($modelType), false, null));
                  $associationTable->columns[] = new IntegerColumn(new IntegerField(Mapper::getReferenceColumnName($refType), false, null));

                  $associationTable->fks[Mapper::getReferenceColumnName($modelType)] = \alat\common\Type::stripNamespace($modelType);
                  $associationTable->fks[Mapper::getReferenceColumnName($refType)] = \alat\common\Type::stripNamespace($refType);

                  $tables[$associationTable->getName()] = $associationTable;
               }
            } else if ($mappingType == MappingType::ForeignKey_ParentChild) {
               $columnName = Mapper::getReferenceColumnName($refType);
               $table->columns[] = new IntegerColumn(new IntegerField($columnName, false, null));
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
            $table->columns[] = Column::fromArray($column);
         }
         $table->fks = $item['fks'];
         $tables[] = $table;
      }

      return $tables;
   }

   private static function fromDescriptor($descriptor) {
      $table = new Table();
      $table->descriptor = $descriptor;
      $table->modelType = Table::getModelType($table->descriptor);
      $table->name = \alat\common\Type::stripNamespace($table->modelType);

      $table->build();
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

   public function toSql($update = false) {
      $script = ($update ? 'alter' : 'create') . ' table ' . $this->getName() . ' (';
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

   public function jsonSerialize() {
      return [
         'name' => $this->getName(),
         'columns' => $this->getColumns(),
         'fks' => $this->getFks(),
      ];
   }

   private function build() {
      $this->columns = array();
      foreach($this->descriptor->getFields() as $field) {
         if ($field instanceof ReferenceField) {
            $mappingType = Mapper::getMappingType($this->modelType, $field->getReferenceType());
            if ($mappingType == MappingType::ForeignKey_ChildParent) {
               $this->columns[] = new IntegerColumn(new IntegerField(Mapper::getReferenceColumnName($field->getReferenceType()), true, null));
            }
         } else {
            $this->columns[] = Column::fromField($field);
         }
      }
   }
}