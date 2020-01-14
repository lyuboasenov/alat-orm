<?php

namespace alat\repository\commands;

interface ICommandBuilderFactory {
   public function create($type);
   public function read($type);
   public function update($type);
   public function delete($type);
}