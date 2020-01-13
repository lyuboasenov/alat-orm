<?php

namespace domain\entities;
use domain\models as models;

class MultiParentEntity extends models\Model {

   public function __construct($data = null) {
      parent::__construct($data);
   }
}