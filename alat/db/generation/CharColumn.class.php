<?php

namespace alat\db\generation;

use alat\domain\models\fields\CharField;

class CharColumn extends Column {
   public function __construct(CharField $field) {
      parent::__construct($field);
   }

   public function getSql() {
      $charField = (CharField) ($this->field);
      return  $this->field->getName() . ' varchar(' . $charField->getMaxLength() . ')';
   }
}