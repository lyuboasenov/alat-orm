<?php

namespace alat\db;

class DbGenerator {
   public static function getDbGenerationScript() {
      $classes = get_declared_classes();
      foreach($classes as $class) {
         $reflect = new \ReflectionClass($class);
         if($reflect->implementsInterface('alat\domain\models\IModelDescriptor')
            && !$reflect->isAbstract()) {
            var_dump($class);
         }
      }
   }
}