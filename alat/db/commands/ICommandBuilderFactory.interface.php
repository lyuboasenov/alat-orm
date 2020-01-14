<?php

namespace alat\db\commands;

interface ICommandBuilderFactory {
   public function insertInto($table);
   public function selectFrom($table);
   public function update($table);
   public function deleteFrom($table);
}