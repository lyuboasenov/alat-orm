<?php

require_once(__DIR__ . '\..\models\Model.php');
require_once(__DIR__ . '\..\models\ModelDescriptor.php');
require_once(__DIR__ . '\..\models\Field.php');

class ChildEntity extends Model {
   public function __construct($data = null) {
      parent::__construct($data);
   }
}

class ChildEntityDescriptor extends ModelDescriptor {
   public function getFields() {
      return [
         new IntegerField('id', false, null),
         new CharField('name', false, null, 50),
      ];
   }
}