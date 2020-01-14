<?php

namespace alat\repository\commands;

interface IUpdateBuilder extends ICommandBuilder {
   public function set($field, $value);
   public function sets($fields);

   public function withId($id);
}