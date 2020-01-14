<?php

namespace alat\db;

use alat\db\commands\CommandBuilderFactory;

class Repository extends \alat\repository\Repository {
   public function __construct(SqlConnection $sqlConnection) {
      parent::__construct(new CommandBuilderFactory($sqlConnection));
   }
}