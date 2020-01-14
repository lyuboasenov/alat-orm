<?php

namespace alat\domain\models\fields;

class OneOfReferenceField extends ReferenceField {
   public function isValid($value) {
      return false;
   }
}