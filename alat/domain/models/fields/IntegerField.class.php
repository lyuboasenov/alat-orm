<?php

namespace alat\domain\models\fields;

class IntegerField extends Field {
   public function __construct($name, $null, $default) {
      parent::__construct($name, $null, $default);
   }

   public function isValid($value) {
      return is_int($value);
   }
}