<?php

namespace alat\db\commands;

class CreateBuilder extends CommandBuilder implements \alat\repository\commands\ICreateBuilder {
   private $table;
   private $fields;

   private function __construct($table, $connection) {
      parent::__construct($connection);
      $this->table = BuilderUtils::formatTableName($table);
      $this->fields = array();
   }

   public static function into($table, $connection) {
      return new CreateBuilder($table, $connection);
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
      $commandText = 'INSERT INTO ' . $this->table . ' (' . implode(', ', array_keys($this->fields)) . ') VALUES (' . implode(', ', array_values($this->fields)) . ')';

      return new SqlCommand($this->connection, $commandText);
   }
}