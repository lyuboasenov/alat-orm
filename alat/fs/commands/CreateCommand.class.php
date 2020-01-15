<?php

namespace alat\fs\commands;

class CreateCommand extends \alat\fs\commands\Command {
   private $fields;
   private $type;

   private $id;

   public function __construct($path, $type, $fields) {
      parent::__construct($path);
      $this->type = $type;
      $this->fields = $fields;

      $this->command = 'create (' . \alat\io\Path::combine($this->path, $this->type) . ')';
   }

   public function execute() {
      parent::execute();

      $handle = $this->getNextFileStream();
      $this->fields['id'] = $this->id;
      ksort($this->fields);
      fwrite($handle, json_encode($this->fields));
      fclose($handle);
   }

   private function getNextFileStream() {
      $id = 1;

      $path = \alat\io\Path::combine($this->path, $this->type);
      $files = scandir($path, 1);

      $firstKey = array_key_first($files);
      $firstValue = $files[$firstKey];

      if ($firstValue != '..') {
         $id = intval($firstValue);
      }

      $handle = null;
      do {
         $id++;
         $handle = fopen(\alat\io\Path::combine($this->path, $this->type, $id), 'w');
      } while (!flock($handle, LOCK_EX));

      $this->id = $id;
      return $handle;
   }

   public function getId() {
      return $this->id;
   }
}