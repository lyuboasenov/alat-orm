<?php

namespace alat\domain\repository;

interface IRepository {
   public function getSet($type);

   public function save();
}