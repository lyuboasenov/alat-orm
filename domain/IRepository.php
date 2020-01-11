<?php

interface IRepository {
   public function getSet($type);

   public function save();
}