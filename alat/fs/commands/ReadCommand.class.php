<?php

namespace alat\fs\commands;

class ReadCommand extends \alat\fs\commands\Command {
   private $types;
   private $fields;
   private $filterType;
   private $filterField;
   private $filterOperator;
   private $filterValue;

   private $result;

   public function __construct($path, $types, $fields, $filterType, $filterField, $filterOperator, $filterValue) {
      parent::__construct($path);
      $this->types = $types;
      $type = array_key_first($this->types);
      $this->fields = $fields[$type];
      $this->filterType = $filterType;
      $this->filterField = $filterField;
      $this->filterOperator = $filterOperator;
      $this->filterValue = $filterValue;

      $this->command = 'read (' . \alat\io\Path::combine($this->path, $type) . ')';
   }

   public function execute() {
      parent::execute();

      $result = array();
      $type = array_key_first($this->types);

      if (!is_null($this->filterType)) {
         $result[$this->filterType] = array();
         foreach(ReadCommand::getIds($this->path, $this->filterType) as $id) {
            $model = ReadCommand::getFileContent($this->path, $this->filterType, $id);
            if (\alat\common\ComparisonOperator::Compare($this->filterOperator, $this->filterValue, $model[$this->filterField])) {
               $result[$this->filterType][] = $model;
            }
         }

         if ($type != $this->filterType) {
            $joinCondition = $this->types[$this->filterType];
            $parentType = array_key_last($joinCondition);
            $field = $joinCondition[$parentType];
            $refField = $joinCondition[$this->filterType];

            // only one join level is supported
            if ($type != $parentType) {
               throw new \ErrorException('Odd join further debugging is needed.');
            }

            foreach($result[$this->filterType] as $ref) {
               // join are made only by id
               $id = $ref[$refField];
               $result[$type][] = ReadCommand::getFileContent($this->path, $type, $id);
            }
         }
      } else {
         foreach(ReadCommand::getIds($this->path, $type) as $id) {
            $model = ReadCommand::getFileContent($this->path, $type, $id);
            $result[$type][] = $model;
         }
      }

      if (!is_null($result[$type])) {
         $this->result = array();

         foreach($result[$type] as $data) {
            $this->result[] = array_filter($data, function ($key) {
               return array_search($key, $this->fields) !== false;
            }, ARRAY_FILTER_USE_KEY);
         }
      }
   }

   public function getResult() {
      return $this->result;
   }

   public static function getIds($path, $type) {
      return array_values(array_diff(scandir(\alat\io\Path::combine($path, $type)), ['.', '..']));
   }

   public static function getFileContent($path, $type, $id) {
      $handle = fopen(\alat\io\Path::combine($path, $type, $id), 'r');
      $content = fgets($handle);
      fclose($handle);

      return json_decode($content, true);
   }
}