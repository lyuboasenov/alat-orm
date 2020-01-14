<?php

namespace alat\fs;

class Repository extends \alat\repository\Repository {
   public function __construct($path) {
      parent::__construct(new \alat\fs\commands\CommandBuilderFactory($path));
   }
}