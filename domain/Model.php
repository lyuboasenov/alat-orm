<?php

require_once('ReferenceEntities.php');

abstract class Model {
   protected $metadata;
   protected $values;
   protected $foreignKeyValues;

   private $isInitialized = false;
   private $isDirty = false;
   private $references;

   private $repository;

   public function __set($name, $value) {
      $this->setField($name, $value, true, true);
   }

   public function __get($name) {
      $this->initialize();

      if (array_key_exists($name, $this->metadata)) {
         if ($this->metadata[$name] instanceof ReferenceField) {
            return $this->getReferenceField($name);
         } else if ($this->metadata[$name] instanceof ForeignKeyField) {
            return $this->values[$name]->id;
         } else {
            return $this->values[$name];
         }
      } else {
         throw new ErrorException('Unknown field "'. $name . '".');
      }
   }

   public function getIsDirty() {
      return $this->isDirty;
   }

   public function getMetadata() {
      $this->initialize();
      return $this->metadata;
   }

   public function getRepository() {
      return $this->repository;
   }

   public function setRepository($repository) {
      $this->repository = $repository;
   }

   public function addReference($model) {
      if (!$this->hasFieldOfType(get_class($model)) && !$model->hasFieldOfType(get_class($this))) {
         throw new ErrorException('Unkown reference from "' . get_class($this) . '" to "' . get_class($model) . '".');
      }

      $thisToRefField = $this->getFieldOfType(get_class($model));
      $refToThisField = $model->getFieldOfType(get_class($this));

      if ($thisToRefField instanceof ManyOfReferenceField && $refToThisField instanceof ManyOfReferenceField) {
         // assosiation
      }

      if ($thisToRefField instanceof OneOfReferenceField || $refToThisField instanceof ManyOfReferenceField) {
         $name = strtolower(get_class($model)) . '_id';
         $this->metadata[$name] = new ForeignKeyField($name);
         $this->values[$name] = $model;
      }
   }

   public function hasFieldOfType($type) {
      return !is_null($this->getFieldOfType($type));
   }

   public function getFieldOfType($type) {
      $this->initialize();
      foreach($this->getMetadata() as $name => $field) {
         if ($field instanceof ReferenceField && $field->getReferenceType() == $type) {
            return $field;
         }
      }

      return null;
   }

   protected function setField($name, $value, $riseIsDirty, $riseIdSetError) {
      $this->initialize();

      if (array_key_exists($name, $this->metadata)) {
         if ($this->metadata[$name] instanceof ReferenceField) {
            throw new ErrorException('Foreign key fields can not be set.');
         } else {
            if ($this->metadata[$name]->isValid($value)) {

               if ($riseIdSetError && $name == 'id') {
                  throw new ErrorException ('Id can not be set explicitly.');
               }

               $this->values[$name] = $value;
            } else {
               throw new ErrorException('Value "' . $value . '" not valid for field "' . $name . '".');
            }

            if ($riseIsDirty) {
               $this->isDirty = true;
            }
         }
      } else {
         throw new ErrorException('Unknown field "'. $name . '" for type ' . get_class($this) . '.');
      }
   }

   protected abstract function getFields();

   protected function initialize() {
      if (!$this->isInitialized) {
         $this->foreignKeyValues = array();
         foreach($this->getFields() as $field) {
            $this->metadata[$field->getName()] = $field;
            if (!($field instanceof ReferenceField)) {
               $this->values[$field->getName()] = !is_null($field->getDefault()) ? $field->getDefault() : null;
            }
         }
         $this->isInitialized = true;
      }
   }

   private function getReferenceField($name) {
      $field = $this->metadata[$name];
      if (is_null($this->repository)) {
         throw new ErrorException('Object not tracked by repository.');
      }

      if (!array_key_exists($name, $this->foreignKeyValues)) {
         $set = $this->repository->getSet($field->getReferenceType());

         $referenceEntities = $set->find(strtolower(get_class($this)) . '_' . 'id' . '=' . $this->id);
         $this->foreignKeyValues[$name] =
            new ReferenceEntities($this->repository, $field, $this, $referenceEntities);
      }

      return $this->foreignKeyValues[$name];
   }
}