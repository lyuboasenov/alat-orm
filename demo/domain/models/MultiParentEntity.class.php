<?php

namespace demo\domain\models;
use alat\domain\models as models;

class MultiParentEntity extends models\Model {

   public function __construct($data = null) {
      parent::__construct($data);
   }
}