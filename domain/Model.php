<?php

abstract class Model {
   protected $metadata;
   protected $values;
   protected $foreignKeyValues;

   private $isInitialized = false;
   private $isDirty = false;

   private $repository;

   public function __set($name, $value) {
      $this->setField($name, $value, true, true);
   }

   public function __get($name) {
      $this->initialize();

      if (array_key_exists($name, $this->metadata)) {
         if ($this->metadata[$name] instanceof ForeignKeyField) {
            return $this->getForeignKeyField($name);
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

   protected function setField($name, $value, $riseIsDirty, $riseIdSetError) {
      $this->initialize();

      if (array_key_exists($name, $this->metadata)) {
         if ($this->metadata[$name] instanceof ForeignKeyField) {
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
            if (!($field instanceof ForeignKeyField)) {
               $this->values[$field->getName()] = !is_null($field->getDefault()) ? $field->getDefault() : null;
            }
         }
         $this->isInitialized = true;
      }
   }

   private function getForeignKeyField($name) {
      $field = $this->metadata[$name];
      if (is_null($this->repository)) {
         throw new ErrorException('Object not tracked by repository.');
      }

      if (!array_key_exists($name, $this->foreignKeyValues)) {
         $set = $this->repository->getSet($field->getReferenceType());

         $this->foreignKeyValues[$name] = $set->find($field->getReferenceField() . '=' . $this->id);
      }

      return $this->foreignKeyValues[$name];
   }
}