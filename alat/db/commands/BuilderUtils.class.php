<?php

namespace alat\db\commands;

class BuilderUtils {
   public static function formatTableName($table) {
      return \alat\common\Type::stripNamespace($table);
   }
}