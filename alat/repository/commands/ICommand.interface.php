<?php

namespace alat\repository\commands;

interface ICommand {
   public function executeNonQuery();
   public function executeScalar();
   public function executeQuery();

   public function newlyCreatedId();
}