<?php

namespace alat\fs\commands;

class ReadCommand extends \alat\fs\commands\Command {
   private $fields;
   public function __construct($path, $fields) {
      parent::__construct($path);
      $this->fields = $fields;
   }

   public function execute() {
      ksort($this->fields);
      $this->command = 'c/' . $this->type . '/0/' . json_encode($this->fields);
      parent::execute();
   }
}