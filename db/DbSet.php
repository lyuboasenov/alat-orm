<?php

require_once(__DIR__ . '\..\domain\repository\Set.php');
require_once('Mapper.php');

class DbSet extends Set {
   private $db;

   public function __construct($db, $repository, $domainType) {
      parent::__construct($repository, $domainType);

      $this->db = $db;
   }

   protected function saveObjectInternal(IModel $model) {
      $mapper = Mapper::fromDomainModel($model);

      $command = null;
      if (is_null($model->id)){
         $command = $mapper->getInsertCommandBuilder()->build($this->db);
      } else {
         $command = $mapper->getUpdateCommandBuilder()->build($this->db);
      }

      $result = $command->executeScalar();
      if ($result != 1) {
         throw new ErrorException('Invalid row count on insert/update "' . $result . '".');
      }

      if (is_null($model->id)){
         $model->id = $command->newId();
      }

      $model->clean();
   }

   protected function saveObjectReferencesInternal(IModel $model, $references) {
      $modelType = $model->getType();
      $refType = $references->getType();

      $mappingType = Mapper::getMapptingType($modelType, $refType);

      if ($mappingType == MappingType::ForeignKey_ParentChild) {
         foreach($references as $ref) {
            UpdateBuilder::table($refType)
               ->set(Mapper::getReferenceColumnName($modelType), $model->id)
               ->where('id=' . $ref->id)
               ->build($this->db)
               ->executeNonQuery();
         }
      } else if ($mappingType == MappingType::ForeignKey_ChildParent) {
         UpdateBuilder::table($modelType)
            ->set(Mapper::getReferenceColumnName($refType), $ref->id)
            ->where('id=' . $model->id)
            ->build($this->db)
            ->executeNonQuery();
      } else if ($mappingType == MappingType::Association) {
         $associationTable = Mapper::getAssociationTableName($modelType, $refType);
         DeleteBuilder::from($associationTable)
            ->where(mapper::getReferenceColumnName($modelType) . '=' . $model->id)
            ->build($this->db)->executeNonQuery();

         foreach($references as $ref) {
            InsertBuilder::into($associationTable)
               ->value(Mapper::getReferenceColumnName($modelType), $model->id)
               ->value(Mapper::getReferenceColumnName($refType), $ref->id)
               ->build($this->db)->executeNonQuery();
         }
      }
   }

   protected function removeObjectInternal(IModel $model) {
      $mapper = Mapper::fromDomainModel($model);

      $command = null;
      if (is_null($model->id)){
         throw new ErrorException('Object with no id can\'t be deleted. "' . $model . '"');
      } else {
         $command = $mapper->getDeleteCommandBuilder()->build($this->db);
      }

      $result = $command->executeScalar();
      if ($result != 1) {
         throw new ErrorException('Invalid row count on delete "' . $result . '".');
      }
   }

   protected function findByIdInternal($id) {
      return $this->findInternal('id=' . $id);
   }

   protected function findByReferenceInternal($ref) {
      $mappingType = Mapper::getMapptingType($ref->getType(), $this->domainModelType);

      $mapper = Mapper::fromDomainType($this->domainModelType);
      $builder = $mapper->getSelectCommandBuilder();

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

   protected function findInternal($criteria) {
      $mapper = Mapper::fromDomainType($this->domainModelType);
      $command = $mapper->getSelectCommandBuilder()
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