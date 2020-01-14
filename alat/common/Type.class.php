<?php

namespace alat\common;

class Type {
   public static function stripNamespace($type) {
      $pos = strrpos($type, '\\');
      if ($pos === false) {
         $pos = -1;
      }
      return substr($type, $pos + 1);
   }
}