<?php

namespace db\commands;

class InsertBuilder {
   private $table;
   private $fields;

   private function __construct($table) {
      $this->table = BuilderUtils::formatTableName($table);
      $this->fields = array();
   }

   public static function into($table) {
      return new InsertBuilder($table);
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

   public function build($connection){
      $commandText = 'INSERT INTO ' . $this->table . ' (' . implode(', ', array_keys($this->fields)) . ') VALUES (' . implode(', ', array_values($this->fields)) . ')';

      return new Command($connection, $commandText);
   }
}