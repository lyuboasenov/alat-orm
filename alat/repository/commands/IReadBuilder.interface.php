<?php

namespace alat\repository\commands;

interface IReadBuilder extends ICommandBuilder {
   public function field($field);
   public function fields($fields);

   public function filter($type, $field, $operator, $value);

   public function join($type, $field, $parentType, $parentField);
}