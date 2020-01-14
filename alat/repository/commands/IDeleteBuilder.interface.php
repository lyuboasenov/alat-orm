<?php

namespace alat\repository\commands;

interface IDeleteBuilder extends ICommandBuilder {
   public function where($where);
}