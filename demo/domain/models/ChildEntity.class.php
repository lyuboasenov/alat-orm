<?php

namespace demo\domain\models;
use alat\domain\models as models;

class ChildEntity extends models\Model {
   public function __construct($data = null) {
      parent::__construct($data);
   }
}