<?php

namespace alat\repository\commands;

interface IReadBuilder extends ICommandBuilder {
   public function field($field);
   public function fields($fields);

   public function typeField($type, $field);
   public function typeFields($type, $fields);

   public function where($where);

   public function join($type, $condition);
}