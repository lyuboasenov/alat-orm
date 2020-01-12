<?php

require_once('Model.php');
require_once('ModelDecorator.php');

class ModelTrackingDecorator extends ModelDecorator {
   private $isDirty = false;

   public function __construct($model) {
      parent::__construct($model);
   }

   public function __set($name, $value) {
      $this->model->__set($name, $value);
      $this->isDirty = true;
   }

   public function getIsDirty() {
      return $this->isDirty || is_null($this->model->id);
   }

   public function clean() {
      $this->isDirty = false;
   }
}