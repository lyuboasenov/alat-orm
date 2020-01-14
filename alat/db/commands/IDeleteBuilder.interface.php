<?php

namespace alat\db\commands;

interface IDeleteBuilder extends ICommandBuilder {
   public function where($where);
}