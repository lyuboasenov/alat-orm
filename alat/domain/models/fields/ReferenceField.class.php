<?php

namespace alat\domain\models\fields;

abstract class ReferenceField extends Field {
   private $referenceType;

   public function __construct($name, $null, $default, $referenceType) {
      parent::__construct($name, $null, $default);
      $this->referenceType = $referenceType;
   }

   public function getReferenceType() {
      return $this->referenceType;
   }
}