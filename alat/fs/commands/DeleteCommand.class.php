<?php

namespace alat\fs\commands;

class DeleteCommand extends \alat\fs\commands\Command {
   private $type;
   private $field;
   private $value;

   public function __construct($path, $type, $field, $value) {
      parent::__construct($path);
      $this->type = $type;
      $this->field = $field;
      $this->value = $value;
   }

   public function execute() {
      if ($this->field == 'id') {
         unlink($this->path . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR . $this->value);
      } else {
         foreach(ReadCommand::getIds($this->path, $this->type) as $id) {
            $data = ReadCommand::getFileContent($this->path, $this->type, $id);
            if ($data[$this->field] == $this->value) {
               unlink($this->path . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR . $id);
            }
         }
      }
   }
}