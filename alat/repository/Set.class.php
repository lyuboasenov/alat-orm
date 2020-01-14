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

   private function saveObjectInternal(models\IModel $model) {
      $mapper = Mapper::fromDomainModel($this->builderFactory, $model);

      $command = null;
      if (is_null($model->id)){
         $command = $mapper->getCreateBuilder()->build($this->db);
      } else {
         $command = $mapper->getUpdateBuilder()->build($this->db);
      }

      $result = $command->executeScalar();
      if ($result != 1) {
         throw new \ErrorException('Invalid row count on insert/update "' . $result . '".');
      }

      if (is_null($model->id)){
         $model->id = $command->newlyCreatedId();
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
               ->build($this->db)
               ->executeNonQuery();
         }
      } else if ($mappingType == MappingType::ForeignKey_ChildParent) {
         $this->builderFactory->update($modelType)
            ->set(Mapper::getReferenceColumnName($refType), $ref->id)
            ->withId($model->id)
            ->build($this->db)
            ->executeNonQuery();
      } else if ($mappingType == MappingType::Association) {
         $associationTable = Mapper::getAssociationTableName($modelType, $refType);
         $this->builderFactory->delete($associationTable)
            ->with(mapper::getReferenceColumnName($modelType), $model->id)
            ->build($this->db)->executeNonQuery();

         foreach($references as $ref) {
            $this->builderFactory->create($associationTable)
               ->value(Mapper::getReferenceColumnName($modelType), $model->id)
               ->value(Mapper::getReferenceColumnName($refType), $ref->id)
               ->build($this->db)->executeNonQuery();
         }
      }
   }

   private function removeObjectInternal(models\IModel $model) {
      $mapper = Mapper::fromDomainModel($this->builderFactory, $model);

      $command = null;
      if (is_null($model->id)){
         throw new \ErrorException('Object with no id can\'t be deleted. "' . $model . '"');
      } else {
         $command = $mapper->getDeleteBuilder()->build($this->db);
      }

      $result = $command->executeScalar();
      if ($result != 1) {
         throw new \ErrorException('Invalid row count on delete "' . $result . '".');
      }
   }

   private function findByIdInternal($id) {
      return $this->findInternal('id=' . $id);
   }

   private function findByReferenceInternal($ref) {
      $mappingType = Mapper::getMappingType($ref->getType(), $this->domainModelType);

      $mapper = Mapper::fromDomainType($this->builderFactory, $this->domainModelType);
      $builder = $mapper->getReadBuilder();

      $parentType = $ref->getType();

      if ($mappingType == MappingType::ForeignKey_ParentChild) {
         $builder
            ->where(Mapper::getReferenceColumnName($parentType) . '=' . $ref->id);
      } else if ($mappingType == MappingType::ForeignKey_ChildParent) {
         $builder
            ->join($parentType, $parentType . '.id=' . $this->domainModelType . '.' . Mapper::getReferenceColumnName($parentType))
            ->where($parentType . '.id=' . $ref->id);
      } else if ($mappingType == MappingType::Association) {
         $associationTable = Mapper::getAssociationTableName($parentType, $this->domainModelType);

         $builder
            ->join($associationTable, $this->domainModelType . '.id=' . $associationTable . '.' . Mapper::getReferenceColumnName($this->domainModelType))
            ->join($parentType, $parentType . '.id=' . $associationTable . '.' . Mapper::getReferenceColumnName($parentType))
            ->where($parentType . '.id=' . $ref->id);
      }

      $data = $builder->build($this->db)->executeQuery();
      $result = array();
      foreach($data as $entry) {
         $result[] = new $this->domainModelType($entry);
      }

      return $result;
   }

   private function findInternal($criteria) {
      $mapper = Mapper::fromDomainType($this->builderFactory, $this->domainModelType);
      $command = $mapper->getReadBuilder()
         ->where($criteria)
         ->build($this->db);

      $data = $command->executeQuery();
      $result = array();
      foreach($data as $entry) {
         $result[] = new $this->domainModelType($entry);
      }

      return $result;
   }
}