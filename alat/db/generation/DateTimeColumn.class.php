<?php

namespace alat\db\generation;

use alat\domain\models\fields\DateTimeField;

class DateTimeColumn extends Column {
   public function __construct(DateTimeField $field) {
      parent::__construct($field);
   }

   public function getSql() {
      return  $this->field->getName() . ' tinyint(1)';
   }
}