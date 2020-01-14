<?php

namespace alat\domain\models;

interface IModelDescriptor {
   public function getFields();
   public function getMetadata();
}