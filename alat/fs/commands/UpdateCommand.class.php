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

      $this->command = 'update (' . \alat\io\Path::combine($path, $type, $id) . ')';
   }

   public function execute() {
      parent::execute();

      $fileFields = ReadCommand::getFileContent($this->path, $this->type, $this->id);
      foreach($fileFields as $key => $value) {
         if (!array_key_exists($key, $this->fields)) {
            $this->fields[$key] = $value;
         }
      }
      ksort($this->fields);
      \alat\io\File::writeFile(\alat\io\Path::combine($this->path, $this->type, $this->id), json_encode($this->fields));
   }

   public function getId() {
      return $this->id;
   }
}