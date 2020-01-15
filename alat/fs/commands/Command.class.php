<?php

namespace alat\fs\commands;

class Command implements \alat\repository\commands\ICommand {
   protected $command;
   protected $path;

   public function __construct($path) {
      $this->path = $path;
   }

   public function execute() {
      $this->log();
   }

   public function getResult() {
      throw new \ErrorException('Not implemented');
   }

   public function getId() {
      throw new \ErrorException('Not implemented');
   }

   private function log(){
      echo '<pre>';
      var_dump('FS COMMAND EXECUTED: {' . $this->command .'}');
      echo '</pre>';
   }
}