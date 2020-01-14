<?php

namespace alat\db\commands;

class CommandBuilderFactory implements \alat\repository\commands\ICommandBuilderFactory {
   private $connection;

   public function __construct($connection) {
      $this->connection = $connection;
   }

   public function create($type) {
      return CreateBuilder::into($type, $this->connection);
   }

   public function read($type) {
      return ReadBuilder::from($type, $this->connection);
   }

   public function update($type) {
      return UpdateBuilder::table($type, $this->connection);
   }

   public function delete($type) {
      return DeleteBuilder::from($type, $this->connection);
   }
}