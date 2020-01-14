<?php

namespace alat\fs\commands;

class CommandBuilder {
   protected $path;

   protected function __construct($path) {
      $this->path = $path;
   }
}