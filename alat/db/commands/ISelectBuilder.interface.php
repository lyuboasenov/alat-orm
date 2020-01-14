<?php

namespace alat\db\commands;

interface ISelectBuilder extends ICommandBuilder {
   public function field($field);
   public function fields($fields);

   public function tableField($table, $field);
   public function tableFields($table, $fields);

   public function where($where);

   public function join($table, $condition);

   public function leftJoin($table, $condition);

   public function rightJoin($table, $condition);
}