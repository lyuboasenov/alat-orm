<?php

namespace domain\models;

abstract class ModelDescriptor implements IModelDescriptor {
   public abstract function getFields();
   public function getMetadata() {
      $metadata = array();
      foreach($this->getFields() as $field) {
         $metadata[$field->getName()] = $field;
      }

      return $metadata;
   }
}