<?php

namespace alat\repository;

interface IRepository {
   public function getSet($type);

   public function save();
}