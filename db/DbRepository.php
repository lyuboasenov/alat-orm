<?php

require_once(__DIR__ . '\..\domain\repository\IRepository.php');
require_once('DbSet.php');

class DbRepository implements IRepository {
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
         $set->save();
      }
   }
}