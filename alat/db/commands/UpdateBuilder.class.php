<?php

namespace alat\db\commands;

class UpdateBuilder {
   private $table;
   private $fields;
   private $where;

   private function __construct($table) {
      $this->table = BuilderUtils::formatTableName($table);
      $this->fields = array();
   }

   public static function table($table) {
      return new UpdateBuilder($table);
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

   public function where($where) {
      $this->where = $where;
      return $this;
   }

   public function build($connection){
      $commandText = 'UPDATE ' . $this->table . ' SET ';
      $commandText .= implode(', ', array_map(
         function ($v, $k) { return sprintf("%s='%s'", $k, $v); },
         $this->fields,
         array_keys($this->fields)
      ));

      if (!is_null($this->where)) {
         $commandText .= ' WHERE ' . $this->where;
      }

      return new Command($connection, $commandText);
   }
}