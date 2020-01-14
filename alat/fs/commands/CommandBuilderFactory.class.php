<?php

namespace alat\fs\commands;

class CommandBuilderFactory implements \alat\repository\commands\ICommandBuilderFactory {
   private $path;

   public function __construct($path) {
      $this->path = $path;
   }

   public function create($type) {
      return new CreateBuilder($type, $this->path);
   }

   public function read($type) {
      return new ReadBuilder($type, $this->path);
   }

   public function update($type) {
      return new UpdateBuilder($type, $this->path);
   }

   public function delete($type) {
      return new DeleteBuilder($type, $this->path);
   }
}