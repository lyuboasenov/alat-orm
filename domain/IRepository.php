<?php

interface IRepository {
   public function getUsers();
   public function getSet($type);

   public function save();
}