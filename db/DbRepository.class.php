<?php

namespace db;
use domain\repository as repository;

class DbRepository implements repository\IRepository {
   private $sets;
   private $db;

   public function __construct($db) {
      $this-> db = $db;
      $this->sets = array();
   }

   public function getSet($type) {
      if (!array_key_exists($type, $this->sets)) {
         $this->sets[$type] = new DbSet($this->db, $this, $type);
      }

      return $this->sets[$type];
   }

   public function save() {
      foreach($this->sets as $type => $set) {
         $set->saveModels();
      }

      foreach($this->sets as $type => $set) {
         $set->saveReferences();
      }
   }
}