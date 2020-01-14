<?php

namespace alat\db\commands;

interface IInsertBuilder extends ICommandBuilder {
   public function value($field, $value);
   public function values($fields);
}