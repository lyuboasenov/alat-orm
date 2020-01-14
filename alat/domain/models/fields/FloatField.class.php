<?php

namespace alat\domain\models\fields;

class FloatField extends Field {
   public function isValid($value) {
      return is_float($value);
   }
}