<?php

namespace alat\db;
use alat\domain\models as models;
use alat\domain\models\fields as fields;
use alat\db\commands as commands;

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

   public static function fromDomainModel(models\IModel $model) {
      return new Mapper($model->getType(), $model);
   }

   public static function getMapptingType($sourceType, $refType) {
      $sourceToRefField = Mapper::getReferenceFieldOfType($sourceType, $refType);
      $refToSourceField = Mapper::getReferenceFieldOfType($refType, $sourceType);

      if ($sourceToRefField instanceof fields\ManyOfReferenceField && $refToSourceField instanceof fields\ManyOfReferenceField) {
         return MappingType::Association;
      } else if ($sourceToRefField instanceof fields\ManyOfReferenceField) {
         return MappingType::ForeignKey_ParentChild;
      } else if ($refToSourceField instanceof fields\OneOfReferenceField) {
         return MappingType::ForeignKey_ChildParent;
      } else {
         throw new \ErrorException('Unkown reference from "' . $sourceType . '" to "' . $refType . '".');
      }
   }

   public static function getAssociationTableName($type1, $type2) {
      if (strcmp($type1, $type2) < 0) {
         return $type1 . $type2;
      } else {
         return $type2 . $type1;
      }
   }

   public static function getReferenceColumnName($type) {
      return strtolower($type) . '_id';
   }

   public function getSelectCommandBuilder() {
      $builder =  commands\SelectBuilder::from($this->table);
      foreach($this->model->getMetadata() as $name => $field) {
         if (!($field instanceof fields\ReferenceField)) {
            $builder->field($name);
         }
      }

      return $builder;
   }

   public function getInsertCommandBuilder() {
      $builder = commands\InsertBuilder::into($this->table);
      foreach($this->model->getMetadata() as $name => $field) {
         if ($name != 'id' && !($field instanceof fields\ReferenceField)) {
            $builder->value($name, $this->model->$name);
         }
      }

      return $builder;
   }

   public function getUpdateCommandBuilder() {
      $builder = commands\UpdateBuilder::table($this->table);
      foreach($this->model->getMetadata() as $name => $field) {
         if ($name != 'id' && !($field instanceof fields\ReferenceField) && !($field instanceof fields\ForeignKeyField)) {
            $builder->set($name, $this->model->$name);
         }
      }

      return $builder->where('id=' . $this->model->id);
   }

   public function getDeleteCommandBuilder() {
      return commands\DeleteBuilder::from($this->table)->where('id=' . $this->model->id);
   }

   private static function getReferenceFieldOfType($sourceType, $fieldType) {
      $sourceDescrType = $sourceType . 'Descriptor';
      $fields = (new $sourceDescrType)->getFields();
      $result = null;

      foreach($fields as $field) {
         if ($field instanceof fields\ReferenceField) {
            $result = $field;
            break;
         }
      }

      return $result;
   }
}

abstract class MappingType {
   const Association = 0;
   const ForeignKey_ParentChild = 1;
   const ForeignKey_ChildParent = 2;
}