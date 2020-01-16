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

   public static function getParent($path) {
      return dirname($path);
   }

   public static function create($path) {
      mkdir($path, 0777, true);
   }

   public static function exists($path) {
      return file_exists($path) && is_dir($path);
   }
}