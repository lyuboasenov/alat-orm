<?php

namespace db\commands;

class DeleteBuilder {
   private $table;
   private $where;

   private function __construct($table) {
      $this->table = BuilderUtils::formatTableName($table);
      $this->fields = array();
   }

   public static function from($table) {
      return new DeleteBuilder($table);
   }

   public function where($where) {
      $this->where = $where;
      return $this;
   }

   public function build($connection){
      $commandText = 'DELETE FROM ' . $this->table;
      if (!is_null($this->where)) {
         $commandText .= ' WHERE ' . $this->where;
      }

      return new Command($connection, $commandText);
   }
}