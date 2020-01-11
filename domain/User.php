<?php

require_once('Model.php');
require_once('Field.php');

class User extends Model {

   public static function fromRawData($data) {
      $user = new User();
      $user->initialize();

      foreach($data as $name => $value) {
         $user->setField($name, $value, false, false);
      }

      return $user;
   }

   protected function getFields() {
      return [
         new IntegerField('id', false, null),
         new CharField('username', false, null, 15),
         new CharField('password', false, null, 35),
         new IntegerField('age', true, 0),
         new IntegerField('role', false, null),
         new OneToManyForeignKeyField('emails', false, null, 'Email', 'user_id'),
      ];
   }
}