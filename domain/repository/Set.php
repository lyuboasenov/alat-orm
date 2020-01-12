<?php

require_once(__DIR__ . '\..\models\Model.php');
require_once(__DIR__ . '\..\models\IModel.php');
require_once(__DIR__ . '\..\models\ModelTrackingDecorator.php');
require_once(__DIR__ . '\..\models\ModelReferencingDecorator.php');

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
         if (array_search($obj, $this->trackedObjects) === false) {
            $obj = new ModelReferencingDecorator($obj, $this->repository);
            $obj = new ModelTrackingDecorator($obj);
            $this->trackedObjects[] = $obj;

            return $obj;
         }
      } else {
         throw new ErrorException('Passed object "' . $obj . '" is of type "' . $obj->getType() . '". Expected type "' . $this->domainModelType . '".');
      }
   }

   public function addMultiple($array) {
      $result = array();
      foreach($array as $obj) {
         $result[] = $this->add($obj);
      }

      return $result;
   }

   public function remove(Model $obj) {
      if (!array_search($obj, $this->trackedObjects)) {
         unset($this->trackedObjects[$obj]);
         $this->removedObjects[] = $obj;
      } else {
         throw new ErrorException('Object "' . $obj . '" not tracked.');
      }
   }

   public function find($criteria) {
      return $this->addMultiple($this->findInternal($criteria));
   }

   public function findById($id) {
      return $this->addMultiple($this->findByIdInternal($id));
   }

   public function findByParent($parent) {
      return $this->addMultiple($this->findByParentInternal($parent));
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

      // TODO: save references
      // foreach($this->trackedObjects as $obj) {
      //    if ($obj->getHasDirtyReferences()) {
      //       foreach($obj->getReferences() as $references)
      //       $this->saveObjectReferencesInternal($obj, $references);
      //    }
      // }
   }

   protected abstract function removeObjectInternal(IModel $obj);
   protected abstract function saveObjectInternal(IModel $obj);
   protected abstract function saveObjectReferencesInternal(IModel $obj, $references);
   protected abstract function findInternal($criteria);
   protected abstract function findByIdInternal($id);
   protected abstract function findByParentInternal($parent);

}