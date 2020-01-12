<?php

require_once(__DIR__ . '\..\models\Model.php');

interface ISet {
   public function add(Model $model);
   public function remove(Model $model);
   public function find($criteria);
   public function findById($id);
   public function findByParent($parent);
   public function save();
}