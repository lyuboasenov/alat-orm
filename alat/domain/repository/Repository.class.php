<?php

namespace alat\domain\repository;

abstract class Repository implements IRepository {
   private $sets = array();

   public function getSet($type) {
      if (!array_key_exists($type, $this->sets)) {
         $this->sets[$type] = $this->createSet($type);
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

   protected abstract function createSet($type);
}