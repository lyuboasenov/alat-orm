<?php

require_once('Model.php');
require_once('Field.php');

class Role extends Model {
   public static function fromRawData($data) {
      $role = new Role();
      $role->initialize();

      foreach($data as $name => $value) {
         $role->setField($name, $value, false, false);
      }

      return $role;
   }

   protected function getFields() {
      return [
         new IntegerField('id', false, null),
         new CharField('name', false, null, 15),
         new IntegerField('value', true, 0),
      ];
   }
}