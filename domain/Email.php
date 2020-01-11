<?php

require_once('Model.php');
require_once('Field.php');

class Email extends MOdel {
   public static function fromRawData($data) {
      $email = new Email();
      $email->initialize();

      foreach($data as $name => $value) {
         $email->setField($name, $value, false, false);
      }

      return $email;
   }

   protected function getFields() {
      return [
         new IntegerField('id', false, null),
         new CharField('email', false, null, 15)
      ];
   }
}