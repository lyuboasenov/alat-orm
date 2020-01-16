<?php

namespace alat\domain\models;

abstract class ModelDescriptor implements IModelDescriptor {
   public abstract function getFields();
   public function getMetadata() {
      $metadata = array();
      foreach($this->getFields() as $field) {
         $metadata[$field->getName()] = $field;
      }

      return $metadata;
   }

   public function jsonSerialize() {
        return [ 'name' => rtrim(\alat\common\Type::stripNamespace(get_class($this)), 'Descriptor'), 'fields' => $this->getFields() ];
    }
}