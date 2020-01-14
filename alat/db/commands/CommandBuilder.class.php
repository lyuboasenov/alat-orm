<?php

namespace alat\db\commands;

class CommandBuilder {
   protected $connection;

   protected function __construct($connection) {
      $this->connection = $connection;
   }
}