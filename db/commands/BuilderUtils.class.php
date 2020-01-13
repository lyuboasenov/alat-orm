<?php

namespace db\commands;

class BuilderUtils {
   public static function formatTableName($table) {
      return str_replace('domain\\entities\\', '', $table);
   }
}