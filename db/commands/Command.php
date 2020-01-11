<?php

class Command {
   private $command;
   private $connection;

   public function __construct($connection, $command) {
      $this->connection = $connection;
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

      if (strpos($this->command, 'FROM User') !== false) {
         return array(array('id' => 12, 'username' => 'ogi', 'age' => 1, 'role' => 1000));
      } else if (strpos($this->command, 'FROM Email') !== false) {
         return array(
            array('id' => 12, 'email' => 'ogi@asenov.com'),
            array('id' => 13, 'email' => 'ogi@gmail.com'),
         );
      }
   }

   private function log(){
      echo '<pre>';
      var_dump('SQL COMMAND EXECUTED: {' . $this->command .'}');
      echo '</pre>';
   }
}
