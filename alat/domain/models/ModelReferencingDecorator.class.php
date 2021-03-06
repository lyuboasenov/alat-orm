<?php

namespace alat\domain\models;
use alat\domain\models\fields as fields;

class ModelReferencingDecorator extends ModelDecorator {
   private $repository;

   private $initialized = false;
   private $fields;
   protected $values;

   public function __construct($model, $repository) {
      parent::__construct($model);

      $this->repository = $repository;
   }

   public function __get($name) {
      $this->initialize();
      if ($this->isReferenceField($name)) {
         return $this->getReference($name);
      } else {
         return $this->model->__get($name);
      }
   }

   public function getReferences() {
      return array_values($this->values);
   }

   public function getHasDirtyReferences() {
      return count(array_keys($this->values)) > 0;
   }

   private function initialize() {
      if (!$this->initialized) {
         $this->fields = array();
         $this->values = array();
         foreach($this->model->getMetadata() as $name => $field) {
            if ($field instanceof fields\ReferenceField) {
               $this->fields[$name] = $field;
            }
         }
      }

      $this->initialized = true;
   }

   private function isReferenceField($name) {
      return array_key_exists($name, $this->fields);
   }

   private function getReference($name) {
      $field = $this->fields[$name];
      if (is_null($this->repository)) {
         throw new \ErrorException('Object not tracked by repository.');
      }

      if (!array_key_exists($name, $this->values)) {
         $set = $this->repository->getSet($field->getReferenceType());

         $referenceEntities = $set->findByReference($this);
         $this->values[$name] =
            new ReferenceEntities($this->repository, $field->getReferenceType(), $referenceEntities);
      }

      return $this->values[$name];
   }
}