<?php

require_once('Model.php');
require_once('Field.php');

class ParentEntity extends Model {

   public static function fromRawData($data) {
      $user = new ParentEntity();
      $user->initialize();

      foreach($data as $name => $value) {
         $user->setField($name, $value, false, false);
      }

      return $user;
   }

   protected function getFields() {
      return [
         new IntegerField('id', false, null),
         new CharField('name', false, null, 15),
         new ManyOfReferenceField('children', false, null, 'ChildEntity'),
      ];
   }
}