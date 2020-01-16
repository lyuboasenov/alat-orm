<?php

namespace alat\domain\models\fields;

use alat\common\Type;

abstract class Field implements \JsonSerializable {
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

   public function jsonSerialize() {
      return array_merge(
         ['type' => Type::stripNamespace(get_class($this)), 'name' => $this->name, 'null' => $this->null, 'default' => $this->default],
         $this->jsonSerializeAdditionalFields());
   }

   protected function jsonSerializeAdditionalFields() {
      return array();
   }
}