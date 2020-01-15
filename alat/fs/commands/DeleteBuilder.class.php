<?php

namespace alat\fs\commands;

class DeleteBuilder extends CommandBuilder implements \alat\repository\commands\IDeleteBuilder {
   private $type;
   private $fields;

   public function __construct($type, $path) {
      parent::__construct($path);
      $this->type = \alat\common\Type::stripNamespace($type);
      $this->fields = array();
   }

   public function withId($id) {
      return $this->with('id', $id);
   }

   public function with($field, $value) {
      $this->fields[$field] = $value;
      return $this;
   }

   public function build(){
      $key = array_key_first($this->fields);
      $value = $this->fields[$key];

      return new DeleteCommand($this->path, $this->type, $key, $value);
   }
}