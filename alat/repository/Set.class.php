<?php

namespace alat\repository;
use alat\domain\models as models;

class Set {
   private $repository;
   private $domainModelType;
   private $builderFactory;

   private $trackedObjects;
   private $removedObjects;


   public function __construct($repository, $domainModelType, $commandBuilderFactory) {
      $this->repository = $repository;
      $this->domainModelType = $domainModelType;
      $this->builderFactory = $commandBuilderFactory;

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

   public function all() {
      return $this->getModels($this->getReadBuilder());
   }

   public function find($criteria) {
      //return $this->addMultiple($this->findInternal($criteria));
   }

   public function findById($id) {
      return $this->getModels($this->getReadBuilder()
         ->filter($this->domainModelType, 'id', \alat\common\ComparisonOperator::eq, $id));
   }

   public function findByReference($ref) {
      $mapper = Mapper::fromDomainType($this->builderFactory, $this->domainModelType);
      return $this->getModels($mapper->getReferenceReadBuilder($ref));
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

   private function saveObjectInternal(models\IModel $model) {
      $mapper = Mapper::fromDomainModel($this->builderFactory, $model);

      $command = null;
      if (is_null($model->id)){
         $command = $mapper->getCreateBuilder()->build();
      } else {
         $command = $mapper->getUpdateBuilder()->build();
      }

      $command->execute();

      if (is_null($model->id)){
         $model->id = $command->getId();
      }

      $model->clean();
   }

   private function saveObjectReferencesInternal(models\IModel $model, $references) {
      $modelType = $model->getType();
      $refType = $references->getType();

      $mappingType = Mapper::getMappingType($modelType, $refType);

      if ($mappingType == MappingType::ForeignKey_ParentChild) {
         foreach($references as $ref) {
            $this->builderFactory->update($refType)
               ->set(Mapper::getReferenceColumnName($modelType), $model->id)
               ->withId($ref->id)
               ->build()
               ->execute();
         }
      } else if ($mappingType == MappingType::ForeignKey_ChildParent) {
         $this->builderFactory->update($modelType)
            ->set(Mapper::getReferenceColumnName($refType), $ref->id)
            ->withId($model->id)
            ->build()
            ->execute();
      } else if ($mappingType == MappingType::Association) {
         $associationTable = Mapper::getAssociationTableName($modelType, $refType);
         $this->builderFactory->delete($associationTable)
            ->with(mapper::getReferenceColumnName($modelType), $model->id)
            ->build()
            ->execute();

         foreach($references as $ref) {
            $this->builderFactory->create($associationTable)
               ->value(Mapper::getReferenceColumnName($modelType), $model->id)
               ->value(Mapper::getReferenceColumnName($refType), $ref->id)
               ->build()
               ->execute();
         }
      }
   }

   private function removeObjectInternal(models\IModel $model) {
      if (is_null($model->id)){
         throw new \ErrorException('Object with no id can\'t be deleted. "' . $model . '"');
      } else {
         $mapper = Mapper::fromDomainModel($this->builderFactory, $model);
         $mapper->getDeleteBuilder()->build()->execute();
      }
   }

   private function getReadBuilder() {
      $mapper = Mapper::fromDomainType($this->builderFactory, $this->domainModelType);
      return $mapper->getReadBuilder();
   }

   private function getModels($builder) {
      $command = $builder->build();
      $command->execute();

      $result = array();
      foreach($command->getResult() as $entry) {
         $model = new $this->domainModelType($entry);
         $result[] = $this->add($model);
      }

      return $result;
   }
}