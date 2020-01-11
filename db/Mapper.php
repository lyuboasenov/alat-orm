<?php

require_once(__DIR__ . '/../domain/ParentEntity.php');
require_once(__DIR__ . '/../domain/ChildEntity.php');
require_once(__DIR__ . '/../domain/Field.php');
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

   public static function fromDomainModel(Model $model) {
      return new Mapper(get_class($model), $model);
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
}