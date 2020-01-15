<?php

namespace alat\io;

class Path {
   public static function combine(...$paths) {
      $result = '';

      foreach($paths as $path) {
         $result .= Path::stripTraillingDirSeparator($path) . DIRECTORY_SEPARATOR;
      }

      return Path::stripTraillingDirSeparator($result);
   }

   private static function stripTraillingDirSeparator($path) {
      return rtrim($path, DIRECTORY_SEPARATOR);
   }
}