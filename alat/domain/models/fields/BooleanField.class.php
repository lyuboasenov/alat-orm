<?php

namespace alat\domain\models\fields;

class BooleanField extends Field {
   public function __construct($name, $null, $default) {
      parent::__construct($name, $null, $default);
   }

   public function isValid($value) {
      return is_bool($value);
   }
}