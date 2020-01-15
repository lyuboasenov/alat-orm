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

      $this->command = 'delete (' . \alat\io\Path::combine($this->path, $this->type) . ') ' . $field . '=' . $value;
   }

   public function execute() {
      parent::execute();

      if ($this->field == 'id') {
         unlink(\alat\io\Path::combine($this->path, $this->type, $this->value));
      } else {
         foreach(ReadCommand::getIds($this->path, $this->type) as $id) {
            $data = ReadCommand::getFileContent($this->path, $this->type, $id);
            if ($data[$this->field] == $this->value) {
               unlink(\alat\io\Path::combine($this->path, $this->type, $id));
            }
         }
      }
   }
}