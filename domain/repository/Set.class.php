<?php

namespace domain\repository;
use domain\models as models;

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

   public function add(models\Model $obj) {
      if (is_null($obj)) {
         throw new \ErrorException('The set does not track nulls.');
      }

      if ($obj instanceof $this->domainModelType) {
         if (array_search($obj, $this->trackedObjects) === false) {
            $obj = new models\ModelReferencingDecorator($obj, $this->repository);
            $obj = new models\ModelTrackingDecorator($obj);
            $this->trackedObjects[] = $obj;

            return $obj;
         }
      } else {
         throw new \ErrorException('Passed object "' . $obj . '" is of type "' . $obj->getType() . '". Expected type "' . $this->domainModelType . '".');
      }
   }

   public function addMultiple($array) {
      $result = array();
      foreach($array as $obj) {
         $result[] = $this->add($obj);
      }

      return $result;
   }

   public function remove(models\Model $obj) {
      if (!array_search($obj, $this->trackedObjects)) {
         unset($this->trackedObjects[$obj]);
         $this->removedObjects[] = $obj;
      } else {
         throw new \ErrorException('Object "' . $obj . '" not tracked.');
      }
   }

   public function find($criteria) {
      return $this->addMultiple($this->findInternal($criteria));
   }

   public function findById($id) {
      return $this->addMultiple($this->findByIdInternal($id));
   }

   public function findByReference($ref) {
      return $this->addMultiple($this->findByReferenceInternal($ref));
   }

   public function saveModels() {
      foreach($this->trackedObjects as $obj) {
         if ($obj->getIsDirty()) {
            $this->saveObjectInternal($obj);
         }
      }

      foreach($this->removedObjects as $obj) {
         $this->removeObjectInternal($obj);
      }
   }

   public function saveReferences() {
      foreach($this->trackedObjects as $obj) {
         if ($obj->getHasDirtyReferences()) {
            foreach($obj->getReferences() as $references)
            $this->saveObjectReferencesInternal($obj, $references);
         }
      }
   }

   protected abstract function removeObjectInternal(models\IModel $obj);
   protected abstract function saveObjectInternal(models\IModel $obj);
   protected abstract function saveObjectReferencesInternal(models\IModel $obj, $references);
   protected abstract function findInternal($criteria);
   protected abstract function findByIdInternal($id);
   protected abstract function findByReferenceInternal($ref);

}