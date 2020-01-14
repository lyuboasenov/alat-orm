<?php

namespace alat\fs\commands;

class CreateCommand extends \alat\fs\commands\Command {
   private $fields;
   private $type;

   public function __construct($path, $type, $fields) {
      parent::__construct($path);
      $this->type = $type;
      $this->fields = $fields;
   }

   public function execute() {
      ksort($this->fields);
      $this->command = 'c/' . $this->type . '/0/' . json_encode($this->fields);
      parent::execute();
   }
}