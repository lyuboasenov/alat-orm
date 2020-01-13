<?php

namespace domain\models;

interface IModelDescriptor {
   public function getFields();
   public function getMetadata();
}