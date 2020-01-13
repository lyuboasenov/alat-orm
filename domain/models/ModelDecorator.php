<?php

require_once('Model.php');
require_once('IModel.php');

class ModelDecorator implements IModel {
   protected $model;

   public function __construct($model) {
      $this->model = $model;
   }

   public function __get($name) {
      return $this->model->__get($name);
   }

   public function __set($name, $value) {
      $this->model->__set($name, $value);
   }

   public function __call($method, $args) {
      return $this->model->$method($args);
   }

   public function getType() {
      return $this->model->getType();
   }

   public function getMetadata() {
      return $this->model->getMetadata();
   }
}