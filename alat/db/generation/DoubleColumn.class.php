<?php

namespace alat\db\generation;

use alat\domain\models\fields\BooleanField;

class DoubleColumn extends Column {
   public function __construct(BooleanField $field) {
      parent::__construct($field);
   }

   public function getSql() {
      return  $this->field->getName() . ' double';
   }
}