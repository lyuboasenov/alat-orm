<?php

namespace alat\db\commands;

interface IUpdateBuilder extends ICommandBuilder {
   public function set($field, $value);
   public function sets($fields);

   public function where($where);
}