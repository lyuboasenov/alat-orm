<?php

namespace alat\domain\models\fields;

class ForeignKeyField extends IntegerField {
   public function __construct($name) {
      parent::__construct($name, false, null);
   }
}