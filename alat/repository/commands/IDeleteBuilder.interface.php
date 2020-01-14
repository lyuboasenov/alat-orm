<?php

namespace alat\repository\commands;

interface IDeleteBuilder extends ICommandBuilder {
   public function withId($id);
   public function with($field, $value);
}