<?php

namespace alat\db\commands;

class DeleteBuilder extends CommandBuilder implements \alat\repository\commands\IDeleteBuilder {
   private $table;
   private $fields;

   private function __construct($table, $connection) {
      parent::__construct($connection);
      $this->table = BuilderUtils::formatTableName($table);
      $this->fields = array();
   }

   public static function from($table, $connection) {
      return new DeleteBuilder($table, $connection);
   }

   public function withId($id) {
      return $this->with('id', $id);
   }

   public function with($field, $value) {
      $this->fields[$field] = $value;
      return $this;
   }

   public function build(){
      $commandText = 'DELETE FROM ' . $this->table;
      if (count($this->fields) > 0) {
         $commandText .= ' WHERE';
         foreach($this->fields as $field => $value) {
            $commandText .= ' ' . $field . '=' . $value . ' AND';
         }
         $commandText = substr($commandText, 0, strlen($commandText) - 4);
      }

      return new SqlCommand($this->connection, $commandText);
   }
}