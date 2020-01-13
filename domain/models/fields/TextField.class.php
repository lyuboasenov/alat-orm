<?php

namespace domain\models\fields;

class TextField extends Field {
   public function __construct($name, $null, $default) {
      parent::__construct($name, $null, $default);
   }

   public function isValid($value) {
      return is_string($value);
   }
}