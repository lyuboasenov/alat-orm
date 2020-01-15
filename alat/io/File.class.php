<?php

namespace alat\io;

class File {
   public static function open($path, $mode, $exclusive = false) {
      $handle = fopen($path, $mode);
      if ($handle != false) {
         if ($exclusive && !flock($handle, LOCK_EX)) {
            fclose($handle);
            $handle = null;
         }
      } else {
         $handle = null;
      }

      return $handle;
   }

   public static function delete($path) {
      if (file_exists($path) && is_file($path)) {
         unlink($path);
      }
   }

   public static function readAsString($path) {
      $result = null;
      if (file_exists($path) && is_file($path)) {
         $handle = File::open($path, 'r');
         if (!is_null($handle)) {
            $result = fgets($handle);
            fclose($handle);
         }
      }

      return $result;
   }

   public static function writeNewFile($path, $content) {
      $handle = File::open($path, 'x', true);
      if (!is_null($handle)) {
         fwrite($handle, $content);
         fclose($handle);

         return true;
      } else {
         return false;
      }
   }

   public static function writeFile($path, $content) {
      $handle = File::open($path, 'c', true);
      if (!is_null($handle)) {
         ftruncate($handle, 0);
         fwrite($handle, $content);
         fclose($handle);

         return true;
      } else {
         return false;
      }
   }
}