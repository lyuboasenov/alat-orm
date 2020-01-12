<?php

require_once('Model.php');
require_once('Field.php');

class ChildEntity extends Model {
   public function __construct($data = null) {
      if (!is_null($data)) {
         $this->initialize();
         foreach($data as $name => $value) {
            $this->setField($name, $value, false, false);
         }
      }
   }

   protected function getFields() {
      return [
         new IntegerField('id', false, null),
         new CharField('name', false, null, 50),
      ];
   }
}