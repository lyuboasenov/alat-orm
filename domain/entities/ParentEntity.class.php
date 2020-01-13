<?php

namespace domain\entities;
use domain\models as models;

class ParentEntity extends models\Model {

   public function __construct($data = null) {
      parent::__construct($data);
   }
}