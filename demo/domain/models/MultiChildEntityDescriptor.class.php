<?php

namespace demo\domain\models;
use alat\domain\models as models;
use alat\domain\models\fields as fields;

class MultiChildEntityDescriptor extends models\ModelDescriptor {
   public function getFields() {
      return [
         new fields\IntegerField('id', false, null),
         new fields\CharField('name', false, null, 50),
         new fields\ManyOfReferenceField('parents', false, null, 'demo\domain\models\MultiParentEntity'),
      ];
   }
}