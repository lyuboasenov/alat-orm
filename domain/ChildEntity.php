<?php

require_once('Model.php');
require_once('Field.php');

class ChildEntity extends Model {
   public static function fromRawData($data) {
      $child = new ChildEntity();
      $child->initialize();

      foreach($data as $name => $value) {
         $child->setField($name, $value, false, false);
      }

      return $child;
   }

   protected function getFields() {
      return [
         new IntegerField('id', false, null),
         new CharField('name', false, null, 50),
      ];
   }
}