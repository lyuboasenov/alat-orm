<?php

namespace alat\db\generation;

use alat\domain\models\fields\TextField;

class TextColumn extends Column {
   public function __construct(TextField $field) {
      parent::__construct($field);
   }

   public function getSql() {
      return  $this->field->getName() . ' text';
   }
}