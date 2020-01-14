<?php

namespace alat\db\commands;

class ReadBuilder extends CommandBuilder implements \alat\repository\commands\IReadBuilder {
   private $tables;
   private $fields;
   private $where;

   private function __construct($table, $connection) {
      parent::__construct($connection);
      $this->tables = array();
      $this->tables[BuilderUtils::formatTableName($table)] = array('MAIN', null);

      $this->fields = array();
   }

   public static function from($table, $connection) {
      return new ReadBuilder($table, $connection);
   }

   public function field($field) {
      $mainTable = array_keys($this->tables)[0];
      $this->fields[$mainTable][] = $field;
      return $this;
   }

   public function fields($fields) {
      $mainTable = array_keys($this->tables)[0];
      $this->fields[$mainTable] = array_merge($this->fields, $fields);
      return $this;
   }

   public function typeField($table, $field) {
      $formatedTable = BuilderUtils::formatTableName($table);
      $this->fields[$formatedTable][] = $field;
      return $this;
   }

   public function typeFields($table, $fields) {
      $formatedTable = BuilderUtils::formatTableName($table);
      $this->fields[$formatedTable] = array_merge($this->fields[$formatedTable], $fields);
      return $this;
   }

   public function where($where) {
      $this->where = $where;
      return $this;
   }

   public function join($table, $condition) {
      $formatedTable = BuilderUtils::formatTableName($table);
      $this->tables[$formatedTable] = array('INNER JOIN', $condition);
      return $this;
   }

   public function build(){
      $tables = array_keys($this->tables);

      $tableSpecificFields = array();
      foreach($this->fields as $table => $fields) {
         foreach($fields as $field) {
            $tableSpecificFields[] = $table . '.' . $field;
         }
      }

      $fromClause = $tables[0];
      for($i = 1; $i < count($tables); $i++) {
         $currentTable = $tables[$i];
         $joinType = $this->tables[$currentTable][0];
         $condition = $this->tables[$currentTable][1];

         $fromClause .= ' ' . $joinType . ' ' . $currentTable . ' ON ' . $condition;
      }

      $commandText = 'SELECT ' . implode(', ', $tableSpecificFields) . ' FROM ' . $fromClause;
      if (!is_null($this->where)) {
         $commandText .= ' WHERE ' . $this->where;
      }

      return new SqlCommand($this->connection, $commandText);
   }
}