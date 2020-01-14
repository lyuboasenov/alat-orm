<?php

namespace alat\fs\commands;

class FsCommand implements \alat\repository\commands\ICommand {
   private $command;
   private $path;

   public function __construct($path, $command) {
      $this->path = $path;
      $this->command = $command;
   }

   public function executeNonQuery() {
      $this->log();
   }

   public function executeScalar() {
      $this->log();
      return 1;
   }

   public function executeQuery() {
      $this->log();

      if (strpos($this->command, 'FROM ParentEntity') !== false) {
         return array(array('id' => 12, 'name' => 'parent'));
      } else if (strpos($this->command, 'FROM ChildEntity') !== false) {
         return array(
            array('id' => 12, 'name' => 'child-1'),
            array('id' => 13, 'name' => 'child-2'),
         );
      } else if (strpos($this->command, 'FROM MultiChildEntity') !== false) {
         return array(
            array('id' => 12, 'name' => 'multi-child-1'),
            array('id' => 13, 'name' => 'multi-child-2'),
         );
      } else if (strpos($this->command, 'FROM MultiParentEntity') !== false) {
         return array(
            array('id' => 22, 'name' => 'multi-parent-1'),
            array('id' => 33, 'name' => 'multi-parent-2'),
         );
      }
   }

   public function newlyCreatedId() {
      return random_int(0, 1000000);
   }

   private function log(){
      echo '<pre>';
      var_dump('FS COMMAND EXECUTED: {' . $this->command .'}');
      echo '</pre>';
   }
}