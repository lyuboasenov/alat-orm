<?php

namespace domain\models\fields;

abstract class Field {
   private $name;
   private $default;
   private $null;

   public function __construct($name, $null, $default) {
      $this->name = $name;
      $this->null = $null;
      $this->default = $default;
   }

   public function getName() {
      return $this->name;
   }

   public function getDefault() {
      return $this->default;
   }

   public function getNull() {
      return $this->null;
   }

   public abstract function isValid($value);
}