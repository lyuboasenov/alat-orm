<?php

namespace alat\fs\commands;

class DeleteCommand extends \alat\fs\commands\Command {
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
         unlink($this->path . PATH_SEPARATOR . $this->type . PATH_SEPARATOR . $this->value);
      } else {
         throw new \ErrorException('Deleting by field other than id not implemented.');
      }
   }
}