<?php

namespace alat\repository;

abstract class Repository implements IRepository {
   private $sets = array();
   private $builderFactory;

   protected function __construct(commands\ICommandBuilderFactory $commandBuilderFactory) {
      $this->builderFactory = $commandBuilderFactory;
   }

   public function getSet($type) {
      if (!array_key_exists($type, $this->sets)) {
         $this->sets[$type] = new Set($this, $type, $this->builderFactory);
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