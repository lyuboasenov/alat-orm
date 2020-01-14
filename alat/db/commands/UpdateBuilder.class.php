<?php

namespace alat\db\commands;

class UpdateBuilder extends CommandBuilder implements \alat\repository\commands\IUpdateBuilder {
   private $table;
   private $fields;
   private $id;

   private function __construct($table, $connection) {
      parent::__construct($connection);
      $this->table = BuilderUtils::formatTableName($table);
      $this->fields = array();
   }

   public static function table($table, $connection) {
      return new UpdateBuilder($table, $connection);
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
      $commandText = 'UPDATE ' . $this->table . ' SET ';
      $commandText .= implode(', ', array_map(
         function ($v, $k) { return sprintf("%s='%s'", $k, $v); },
         $this->fields,
         array_keys($this->fields)
      ));

      if (!is_null($this->id)) {
         $commandText .= ' WHERE id=' . $this->id;
      }

      return new SqlCommand($this->connection, $commandText);
   }
}