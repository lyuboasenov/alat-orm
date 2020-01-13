<?php

namespace domain\models\fields;

class OneOfReferenceField extends ReferenceField {
   public function isValid($value) {
      return false;
   }
}