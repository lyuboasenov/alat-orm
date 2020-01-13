<?php

namespace domain\models\fields;

class ManyOfReferenceField extends ReferenceField {
   public function __construct($name, $null, $default, $referenceType) {
      parent::__construct($name, $null, $default, $referenceType);
   }

   public function isValid($value) {
      return false;
   }
}