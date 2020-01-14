<?php

namespace alat\fs\commands;

class ReadBuilder extends CommandBuilder implements \alat\repository\commands\IReadBuilder {
   private $types;
   private $fields;
   private $filterType;
   private $filterField;
   private $filterOperator;
   private $filterValue;

   public function __construct($type, $path) {
      parent::__construct($path);
      $this->types = array();
      $this->types[\alat\common\Type::stripNamespace($type)] = null;

      $this->fields = array();
   }

   public function field($field) {
      $mainTable = array_keys($this->types)[0];
      $this->fields[$mainTable][] = $field;
      return $this;
   }

   public function fields($fields) {
      $mainTable = array_keys($this->types)[0];
      $this->fields[$mainTable] = array_merge($this->fields, $fields);
      return $this;
   }

   public function filter($type, $field, $operator, $value) {
      $type = \alat\common\Type::stripNamespace($type);
      $this->filterType = $type;
      $this->filterField = $field;
      $this->filterOperator = $operator;
      $this->filterValue = $value;
      return $this;
   }

   public function join($type, $field, $parentType, $parentField) {
      $type = \alat\common\Type::stripNamespace($type);
      $parentType = \alat\common\Type::stripNamespace($parentType);
      $this->types[$type] = [ $type => $field, $parentType => $parentField ];
      return $this;
   }

   public function build(){
      $types = array_keys($this->types);

      $tableSpecificFields = array();
      foreach($this->fields as $type => $fields) {
         foreach($fields as $field) {
            $tableSpecificFields[] = $type . '.' . $field;
         }
      }

      $fromClause = $types[0];
      for($i = 1; $i < count($types); $i++) {
         $currentTable = $types[$i];
         $joinType = $this->types[$currentTable][0];
         $condition = $this->types[$currentTable][1];

         $fromClause .= ' ' . $joinType . ' ' . $currentTable . ' ON ' . $condition;
      }

      $commandText = 'SELECT ' . implode(', ', $tableSpecificFields) . ' FROM ' . $fromClause;
      if (!is_null($this->where)) {
         $commandText .= ' WHERE ' . $this->where;
      }

      return new ReadCommand($this->path, $this->types, $this->fields, $this->filterType, $this->filterField, $this->filterOperator, $this->filterValue);
   }
}