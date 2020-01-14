<?php

namespace alat\db\commands;

class BuilderUtils {
   public static function formatTableName($table) {
      $pos = strrpos($table, '\\');
      if ($pos === false) {
         $pos = -1;
      }
      return substr($table, $pos + 1);
   }
}