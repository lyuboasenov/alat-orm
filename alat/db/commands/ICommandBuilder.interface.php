<?php

namespace alat\db\commands;

interface ICommandBuilder {
   public function build($connection);
}