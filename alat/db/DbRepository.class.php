<?php

namespace alat\db;
use alat\domain\repository as repository;

class DbRepository extends repository\Repository {
   private $db;

   public function __construct($db) {
      $this-> db = $db;
   }

   protected function createSet($type) {
      return new DbSet($this->db, $this, $type);
   }
}