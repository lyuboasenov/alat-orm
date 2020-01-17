<?php

namespace alat\db\generation;

use alat\domain\models\fields\FloatField;

class DoubleColumn extends Column {
   public function __construct(FloatField $field) {
      parent::__construct($field);
   }

   protected function getSqlType() {
      return 'double';
   }
}