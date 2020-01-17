<?php

namespace alat\db\generation;

use alat\domain\models\fields\BooleanField;

class BooleanColumn extends Column {
   public function __construct(BooleanField $field) {
      parent::__construct($field);
   }

   protected function getSqlType() {
      return 'tinyint(1)';
   }
}