<?php

namespace alat\db\commands;

class DeleteBuilder extends CommandBuilder implements \alat\repository\commands\IDeleteBuilder {
   private $table;
   private $where;

   private function __construct($table, $connection) {
      parent::__construct($connection, $connection);
      $this->table = BuilderUtils::formatTableName($table);
      $this->fields = array();
   }

   public static function from($table, $connection) {
      return new DeleteBuilder($table, $connection);
   }

   public function where($where) {
      $this->where = $where;
      return $this;
   }

   public function build(){
      $commandText = 'DELETE FROM ' . $this->table;
      if (!is_null($this->where)) {
         $commandText .= ' WHERE ' . $this->where;
      }

      return new SqlCommand($this->connection, $commandText);
   }
}