<?php

namespace alat\domain\models\fields;

class CharField extends Field {
   private $maxLength;

   public function __construct($name, $null, $default, $maxLength) {
      parent::__construct($name, $null, $default);
      $this->maxLength = $maxLength;
   }

   public function getMaxLength() {
      return $this->maxLength;
   }

   public function isValid($value) {
      return is_string($value) && strlen($value) <= $this->maxLength;
   }

   protected function jsonSerializeAdditionalFields() {
      return ['len' => $this->getMaxLength()];
   }
}
