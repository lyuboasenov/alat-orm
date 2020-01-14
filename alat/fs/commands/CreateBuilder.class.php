<?php

namespace alat\fs\commands;

class CreateBuilder extends CommandBuilder implements \alat\repository\commands\ICreateBuilder {
   private $type;
   private $fields;

   public function __construct($type, $path) {
      parent::__construct($path);
      $this->type = \alat\common\Type::stripNamespace($type);
      $this->fields = array();
   }

   public function value($field, $value) {
      $this->fields[$field] = $value;
      return $this;
   }

   public function values($fields) {
      foreach($fields as $field => $value) {
         $this->value($field, $value);
      }

      return $this;
   }

   public function build(){
      $commandText = 'c/' . $this->type . '/0/' . json_encode(ksort($this->fields));
      return new FsCommand($this->path, $commandText);
   }
}