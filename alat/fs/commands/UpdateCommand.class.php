<?php

namespace alat\fs\commands;

class UpdateCommand extends \alat\fs\commands\Command {
   private $fields;
   private $type;
   private $id;

   public function __construct($path, $type, $id, $fields) {
      parent::__construct($path);
      $this->type = $type;
      $this->id = $id;
      $this->fields = $fields;
   }

   public function execute() {
      $fileFields = ReadCommand::getFileContent($this->path, $this->type, $this->id);

      foreach($fileFields as $key => $value) {
         if (!array_key_exists($key, $this->fields)) {
            $this->fields[$key] = $value;
         }
      }

      ksort($this->fields);
      $this->saveFileContent();
   }

   public function getId() {
      return $this->id;
   }

   private function saveFileContent() {
      $handle = fopen($this->path . $this->type . '\\' . $this->id, 'w');
      fwrite($handle, json_encode($this->fields));
      fclose($handle);
   }
}