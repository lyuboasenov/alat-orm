<?php

namespace alat\db\generation;

use alat\domain\models\fields\BooleanField;
use alat\domain\models\fields\CharField;
use alat\domain\models\fields\DateTimeField;
use alat\domain\models\fields\DecimalField;
use alat\domain\models\fields\Field;
use alat\domain\models\fields\IntegerField;
use alat\domain\models\fields\FloatField;
use alat\domain\models\fields\TextField;

abstract class Column {
   protected $field;

   protected function __construct(Field $field) {
      $this->field = $field;
   }

   public static function fromField(Field $field) {
      if ($field instanceof IntegerField) {
         return new IntegerColumn($field);
      } else if ($field instanceof BooleanField) {
         return new BooleanColumn($field);
      } else if ($field instanceof CharField) {
         return new CharColumn($field);
      } else if ($field instanceof DateTimeField) {
         return new DateTimeColumn($field);
      } else if ($field instanceof DecimalField) {
         return new DecimalColumn($field);
      } else if ($field instanceof FloatField) {
         return new DoubleColumn($field);
      } else if ($field instanceof TextField) {
         return new TextColumn($field);
      } else {
         throw new \ErrorException('Unknown field ' . $field);
      }
   }

   public abstract function getSql();
}