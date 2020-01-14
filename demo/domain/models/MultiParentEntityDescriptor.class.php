<?php

namespace demo\domain\models;
use alat\domain\models as models;
use alat\domain\models\fields as fields;

class MultiParentEntityDescriptor extends models\ModelDescriptor {
   public function getFields() {
      return [
         new fields\IntegerField('id', false, null),
         new fields\CharField('name', false, null, 15),
         new fields\ManyOfReferenceField('children', false, null, 'demo\domain\models\MultiChildEntity'),
      ];
   }
}