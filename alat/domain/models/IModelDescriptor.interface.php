<?php

namespace alat\domain\models;

interface IModelDescriptor extends \JsonSerializable {
   public function getFields();
   public function getMetadata();
}