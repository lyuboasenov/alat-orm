<?php

require_once(__DIR__ . '/../domain/entities/ParentEntity.php');
require_once(__DIR__ . '/../domain/entities/ChildEntity.php');
require_once(__DIR__ . '/../domain/entities/MultiParentEntity.php');
require_once(__DIR__ . '/../domain/entities/MultiChildEntity.php');
require_once(__DIR__ . '/../domain/models/Field.php');
require_once(__DIR__ . '/commands/SelectBuilder.php');
require_once(__DIR__ . '/commands/InsertBuilder.php');
require_once(__DIR__ . '/commands/UpdateBuilder.php');
require_once(__DIR__ . '/commands/DeleteBuilder.php');

class Mapper {
   private $table;
   private $model;

   private function __construct($table, $model) {
      $this->table = $table;
      $this->model = $model;
   }

   public static function fromDomainType($modelType) {
      return new Mapper($modelType, new $modelType);
   }

   public static function fromDomainModel(IModel $model) {
      return new Mapper($model->getType(), $model);
   }

   public static function getMapptingType($sourceType, $refType) {
      $sourceToRefField = Mapper::getReferenceFieldOfType($sourceType, $refType);
      $refToSourceField = Mapper::getReferenceFieldOfType($refType, $sourceType);

      if ($sourceToRefField instanceof ManyOfReferenceField && $refToSourceField instanceof ManyOfReferenceField) {
         return MappingType::Association;
      } else if ($sourceToRefField instanceof ManyOfReferenceField || $refToSourceField instanceof OneOfReferenceField) {
         return MappingType::ForeignKey;
      } else {
         throw new ErrorException('Unkown reference from "' . $sourceType . '" to "' . $refType . '".');
      }
   }

   public static function getAssociationTableName($type1, $type2) {
      if (strcmp($type1, $type2) < 0) {
         return $type1 . $type2;
      } else {
         return $type2 . $type1;
      }
   }

   public function getSelectCommandBuilder() {
      $builder =  SelectBuilder::from($this->table);
      foreach($this->model->getMetadata() as $name => $field) {
         if (!($field instanceof ReferenceField)) {
            $builder->field($name);
         }
      }

      return $builder;
   }

   public function getInsertCommandBuilder() {
      $builder = InsertBuilder::into($this->table);
      foreach($this->model->getMetadata() as $name => $field) {
         if ($name != 'id' && !($field instanceof ReferenceField)) {
            $builder->value($name, $this->model->$name);
         }
      }

      return $builder;
   }

   public function getUpdateCommandBuilder() {
      $builder = UpdateBuilder::table($this->table);
      foreach($this->model->getMetadata() as $name => $field) {
         if ($name != 'id' && !($field instanceof ReferenceField) && !($field instanceof ForeignKeyField)) {
            $builder->set($name, $this->model->$name);
         }
      }

      return $builder->where('id=' . $this->model->id);
   }

   public function getDeleteCommandBuilder() {
      return DeleteBuilder::from($this->table)->where('id=' . $this->model->id);
   }

   private static function getReferenceFieldOfType($sourceType, $fieldType) {
      $sourceDescrType = $sourceType . 'Descriptor';
      $fields = (new $sourceDescrType)->getFields();
      $result = null;

      foreach($fields as $field) {
         if ($field instanceof ReferenceField) {
            $result = $field;
            break;
         }
      }

      return $result;
   }
}

abstract class MappingType {
   const Association = 0;
   const ForeignKey = 1;
}