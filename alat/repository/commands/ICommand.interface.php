<?php

namespace alat\repository\commands;

interface ICommand {
   public function execute();

   public function getId();
   public function getResult();
}