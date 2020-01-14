<?php

namespace alat\fs\commands;

class UpdateBuilder extends CommandBuilder implements \alat\repository\commands\IUpdateBuilder {
   private $type;
   private $fields;
   private $id;

   public function __construct($type, $path) {
      parent::__construct($path);
      $this->type = \alat\common\Type::stripNamespace($type);
      $this->fields = array();
   }

   public function set($field, $value) {
      $this->fields[$field] = $value;
      return $this;
   }

   public function sets($fields) {
      foreach($fields as $field => $value) {
         $this->set($field, $value);
      }

      return $this;
   }

   public function withId($id) {
      $this->id = $id;
      return $this;
   }

   public function build() {
      return new UpdateCommand($this->path, $this->type, $this->id, $this->fields);
   }
}