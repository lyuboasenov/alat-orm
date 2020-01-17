<?php

namespace alat\db\generation;

use alat\domain\models\fields\DecimalField;

class DecimalColumn extends Column {
   private $decField;
   public function __construct(DecimalField $field) {
      parent::__construct($field);
      $this->decField = $field;
   }

   protected function jsonSerializeAdditionalFields() {
      return ['digits' => $this->decField->getMaxDigits(), 'decimals' => $this->decField->getDecimalPlaces()];
   }

   protected function getSqlType() {
      return 'decimal(' . $this->decField->getMaxDigits() . ',' . $this->decField->getDecimalPlaces() . ')';
   }
}