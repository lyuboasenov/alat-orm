<?php

namespace alat\repository;
use alat\domain\models as models;

interface ISet {
   public function add(models\Model $model);

   public function remove(models\Model $model);

   public function all();
   public function find($criteria);
   public function findById($id);
   public function findByParent($parent);

   public function saveModels();
   public function saveReferences();
}