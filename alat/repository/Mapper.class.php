<?php

namespace alat\repository;
use alat\domain\models as models;
use alat\domain\models\fields as fields;

class Mapper {
   private $type;
   private $model;
   private $builderFactory;

   private function __construct($commandBuilderFactory, $type, $model) {
      $this->builderFactory = $commandBuilderFactory;
      $this->type = $type;
      $this->model = $model;
   }

   public static function fromDomainType($commandBuilderFactory, $modelType) {
      return new Mapper($commandBuilderFactory, $modelType, new $modelType);
   }

   public static function fromDomainModel($commandBuilderFactory, models\IModel $model) {
      return new Mapper($commandBuilderFactory, $model->getType(), $model);
   }

   public static function getMappingType($sourceType, $refType) {
      $sourceToRefField = Mapper::getReferenceFieldOfType($sourceType, $refType);
      $refToSourceField = Mapper::getReferenceFieldOfType($refType, $sourceType);

      if ($sourceToRefField instanceof fields\ManyOfReferenceField && $refToSourceField instanceof fields\ManyOfReferenceField) {
         return MappingType::Association;
      } else if ($sourceToRefField instanceof fields\ManyOfReferenceField) {
         return MappingType::ForeignKey_ParentChild;
      } else if ($refToSourceField instanceof fields\OneOfReferenceField) {
         return MappingType::ForeignKey_ChildParent;
      } else {
         return MappingType::None;
      }
   }

   public static function getAssociationTableName($type1, $type2) {
      $type1 = \alat\common\Type::stripNamespace($type1);
      $type2 = \alat\common\Type::stripNamespace($type2);

      if (strcmp($type1, $type2) < 0) {
         return $type1 . $type2;
      } else {
         return $type2 . $type1;
      }
   }

   public static function getReferenceColumnName($type) {
      return strtolower(\alat\common\Type::stripNamespace($type)) . '_id';
   }

   public function getReadBuilder() {
      $builder = $this->builderFactory->read($this->type);
      foreach($this->model->getMetadata() as $name => $field) {
         if (!($field instanceof fields\ReferenceField)) {
            $builder->field($name);
         }
      }

      return $builder;
   }

   public function getReferenceReadBuilder($ref) {
      $type = $this->type;
      $refType = $ref->getType();

      $builder = $this->getReadBuilder();

      $mappingType = Mapper::getMappingType($refType, $type);

      if ($mappingType == MappingType::ForeignKey_ParentChild) {
         $builder
            ->filter($type, Mapper::getReferenceColumnName($refType), \alat\common\ComparisonOperator::eq, $ref->id);
      } else if ($mappingType == MappingType::ForeignKey_ChildParent) {
         $field = Mapper::getReferenceColumnName($refType);
         $builder
            ->filter($type, 'id', \alat\common\ComparisonOperator::eq, $ref->$field);
      } else if ($mappingType == MappingType::Association) {
         $associationTable = Mapper::getAssociationTableName($refType, $type);

         $builder
            ->join($associationTable, Mapper::getReferenceColumnName($type), $type, 'id')
            ->filter($associationTable, Mapper::getReferenceColumnName($refType), \alat\common\ComparisonOperator::eq, $ref->id);
      }

      return $builder;
   }

   public function getCreateBuilder() {
      $builder = $this->builderFactory->create($this->type);
      foreach($this->model->getMetadata() as $name => $field) {
         if ($name != 'id' && !($field instanceof fields\ReferenceField)) {
            $builder->value($name, $this->model->$name);
         }
      }

      return $builder;
   }

   public function getUpdateBuilder() {
      $builder = $this->builderFactory->update($this->type);
      foreach($this->model->getMetadata() as $name => $field) {
         if ($name != 'id' && !($field instanceof fields\ReferenceField)) {
            $builder->set($name, $this->model->$name);
         }
      }

      return $builder->withId($this->model->id);
   }

   public function getDeleteBuilder() {
      return $this->builderFactory->delete($this->type)->withId($this->model->id);
   }

   private static function getReferenceFieldOfType($sourceType, $fieldType) {
      $sourceDescrType = $sourceType . 'Descriptor';
      $fields = (new $sourceDescrType)->getFields();
      $result = null;

      foreach($fields as $field) {
         if ($field instanceof fields\ReferenceField && $field->getReferenceType() == $fieldType) {
            $result = $field;
            break;
         }
      }

      return $result;
   }
}

abstract class MappingType {
   const Association = 0;
   const ForeignKey_ParentChild = 1;
   const ForeignKey_ChildParent = 2;
   const None = 3;
}