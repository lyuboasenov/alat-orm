<?php

namespace alat\fs\commands;

class DeleteCommand extends \alat\fs\commands\Command {
   public function __construct($path, $type, $id) {
      parent::__construct($path);
      $this->type = $type;
      $this->id = $id;
   }

   public function execute() {
      $this->command = 'd/' . $this->type . '/' . $this->id . '/';
      parent::execute();
   }
}