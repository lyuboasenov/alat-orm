<?php

namespace alat\repository\commands;

interface ICreateBuilder extends ICommandBuilder {
   public function value($field, $value);
   public function values($fields);
}