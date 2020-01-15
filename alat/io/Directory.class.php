<?php

namespace alat\io;

class Directory {
   public static function getFiles($path) {
      $result = array();

      if (file_exists($path) && is_dir($path)) {
         $result = array_values(array_diff(scandir($path), ['.', '..']));
      }

      return $result;
   }
}