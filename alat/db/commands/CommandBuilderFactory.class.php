<?php

namespace alat\db\commands;

class CommandBuilderFactory implements ICommandBuilderFactory {
   public function insertInto($table) {
      return InsertBuilder::into($table);
   }

   public function selectFrom($table) {
      return SelectBuilder::from($table);
   }

   public function update($table) {
      return UpdateBuilder::table($table);
   }

   public function deleteFrom($table) {
      return DeleteBuilder::from($table);
   }
}