<?php

namespace alat\db\generation;

use alat\domain\models\fields\IntegerField;

class IntegerColumn extends Column {
   public function __construct(IntegerField $field) {
      parent::__construct($field);
   }

   public function getSql() {
      if ($this->field->getName() == 'id') {
         return  'id bigint auto_increment primary key';
      } else {
         return parent::getSql();
      }
   }

   protected function getSqlType() {
      if (strpos($this->field->getName(), '_id')) {
         return 'bigint';
      } else {
         return 'int';
      }
   }
}