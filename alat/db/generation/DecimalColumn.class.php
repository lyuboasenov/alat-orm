<?php

namespace alat\db\generation;

use alat\domain\models\fields\DecimalField;

class DecimalColumn extends Column {
   public function __construct(DecimalField $field) {
      parent::__construct($field);
   }

   public function getSql() {
      $decField = (DecimalField) ($this->field);
      return  $this->field->getName() . ' decimal(' . $decField->getMaxDigits() . ',' . $decField->getDecimalPlaces() . ')';
   }
}