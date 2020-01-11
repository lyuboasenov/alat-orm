<?php

class Connection {
   private $connectionString;

   public function __construct($connectionString) {
      $this->connectionString = $connectionString;
   }

   public function open() {

   }
}