<?php

namespace domain\models\fields;

class DecimalField extends Field {
   private $maxDigits;
   private $decimalPlaces;

   public function __construct($name, $null, $default, $maxDigits, $decimalPlaces) {
      parent::__construct($name, $null, $default);
      $this->maxDigits = $decimalPlaces;
   }

   public function getMaxDigits() {
      return $this->maxDigits;
   }

   public function getDecimalPlaces() {
      return $this->decimalPlaces;
   }

   public function isValid($value) {
      return is_double($value);
   }
}