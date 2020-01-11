<?php

require_once('Model.php');

abstract class Set {
   protected $repository;
   protected $domainModelType;
   private $trackedObjects;
   private $removedObjects;


   public function __construct($repository, $domainModelType) {
      $this->repository = $repository;
      $this->domainModelType = $domainModelType;
      $this->trackedObjects = array();
      $this->removedObjects = array();
   }

   public function add(Model $obj) {
      if (is_null($obj)) {
         throw new ErrorException('The set does not track nulls.');
      }

      if ($obj instanceof $this->domainModelType) {
         if (array_search($obj, $this->trackedObjects) === false){
            $this->trackedObjects[] = $obj;
            $obj->setRepository($this->repository);
         }
      } else {
         throw new ErrorException('Passed object "' . $obj . '" is of type "' . gettype($obj) . '". Expected type "' . $this->domainModelType . '".');
      }
   }

   public function remove(Model $obj) {
      if (!array_search($obj, $this->trackedObjects)) {
         unset($this->trackedObjects[$obj]);
         $this->removedObjects[] = $obj;
      } else {
         throw new ErrorException('Object "' . $obj . '" not tracked.');
      }
   }

   public function findById($id) {
      $found = $this->findByIdInternal($id);

      foreach($found as $obj) {
         $this->add($obj);
      }

      return $found;
   }

   public function find($criteria) {
      $found = $this->findInternal($criteria);

      foreach($found as $obj) {
         $this->add($obj);
      }

      return $found;
   }

   public function save() {
      foreach($this->trackedObjects as $obj) {
         if ($obj->getIsDirty()) {
            $this->saveObjectInternal($obj);
         }
      }

      foreach($this->removedObjects as $obj) {
         $this->removeObjectInternal($obj);
      }
   }

   protected abstract function removeObjectInternal(Model $obj);
   protected abstract function saveObjectInternal(Model $obj);
   protected abstract function findByIdInternal($id);
   protected abstract function findInternal($criteria);
}