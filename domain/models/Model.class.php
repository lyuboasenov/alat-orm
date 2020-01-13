<?php

namespace domain\models;
use domain\models\fields as fields;

abstract class Model implements IModel {
   protected $metadata;
   protected $values;

   private $isInitialized = false;

   public function __construct($data = null) {
      if (!is_null($data)) {
         $this->initialize();
         foreach($data as $name => $value) {
            $this->setField($name, $value, false, false);
         }
      }
   }

   public function __set($name, $value) {
      $this->setField($name, $value, true, true);
   }

   public function __get($name) {
      $this->initialize();

      if (array_key_exists($name, $this->metadata)) {
         if ($this->metadata[$name] instanceof fields\ReferenceField) {
            throw new \ErrorException('Reference fields should not be handled by model base.');
         } else if ($this->metadata[$name] instanceof fields\ForeignKeyField) {
            return $this->values[$name]->id;
         } else {
            return $this->values[$name];
         }
      } else {
         throw new \ErrorException('Unknown field "'. $name . '".');
      }
   }

   public function getMetadata() {
      $this->initialize();
      return $this->metadata;
   }

   public function getType() {
      return get_class($this);
   }

   protected function setField($name, $value, $riseIdSetError) {
      $this->initialize();

      if (array_key_exists($name, $this->metadata)) {
         if ($this->metadata[$name] instanceof fields\ReferenceField) {
            throw new \ErrorException('Foreign key fields can not be set.');
         } else {
            if ($this->metadata[$name]->isValid($value)) {
               $this->values[$name] = $value;
            } else {
               throw new \ErrorException('Value "' . $value . '" not valid for field "' . $name . '".');
            }
         }
      } else {
         throw new \ErrorException('Unknown field "'. $name . '" for type ' . $this->getType() . '.');
      }
   }

   protected function getDescriptor() {
      $type = $this->getType();
      $type .= 'Descriptor';
      return new $type();
   }

   protected function initialize() {
      if (!$this->isInitialized) {
         $this->metadata = $this->getDescriptor()->getMetadata();
         foreach($this->metadata as $name => $field) {
            if (!($field instanceof fields\ReferenceField)) {
               $this->values[$name] = !is_null($field->getDefault()) ? $field->getDefault() : null;
            }
         }
         $this->isInitialized = true;
      }
   }
}