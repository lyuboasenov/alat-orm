<?php

namespace alat\db;

class SqlConnection {
   private $connectionString;

   public function __construct($connectionString) {
      $this->connectionString = $connectionString;
   }
}