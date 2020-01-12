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

   protected function findByParentInternal($parent) {
      $mappingType = Mapper::getMapptingType($parent->getType(), $this->domainModelType);

      $mapper = Mapper::fromDomainType($this->domainModelType);
      $builder = $mapper->getSelectCommandBuilder();

      $parentType = $parent->getType();

      if ($mappingType == MappingType::ForeignKey) {
         $builder
            ->join($parentType, $parentType . '.id=' . $this->domainModelType . '.' . strtolower($parentType) . '_id')
            ->where($parentType . '.id=' . $parent->id);
      } else if ($mappingType == MappingType::Association) {
         $associationTable = Mapper::getAssociationTableName($parentType, $this->domainModelType);

         $builder
            ->join($associationTable, $this->domainModelType . '.id=' . $associationTable . '.' . strtolower($this->domainModelType) . '_id')
            ->join($parentType, $parentType . '.id=' . $associationTable . '.' . strtolower($parentType) . '_id')
            ->where($parentType . '.id=' . $parent->id);
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