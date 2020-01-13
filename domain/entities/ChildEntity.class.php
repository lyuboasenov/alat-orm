<?php

namespace domain\entities;
use domain\models as models;

class ChildEntity extends models\Model {
   public function __construct($data = null) {
      parent::__construct($data);
   }
}