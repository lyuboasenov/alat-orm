<?php

namespace alat\db\generation;

use alat\domain\models\fields\CharField;

class CharColumn extends Column {
   private $charField;

   public function __construct(CharField $field) {
      parent::__construct($field);
      $this->charField = $field;
   }

   protected function jsonSerializeAdditionalFields() {
      return ['len' => $this->charField->getMaxLength()];
   }

   protected function getSqlType() {
      return ' varchar(' . $this->charField->getMaxLength() . ') charset utf8';
   }
}