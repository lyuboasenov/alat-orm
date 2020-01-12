<?php

class ReferenceEntities implements IteratorAggregate {
   private $repository;
   private $type;
   private $items;

   public function __construct($repository, $type, $array) {
      $this->repository = $repository;
      $this->type = $type;
      $this->items = $array;

      if(!is_null($this->items)) {
         foreach($this->items as $item) {
            if ($item->getType() != $this->type) {
               throw new ErrorException('Invalid item type: expected "' . $this->type . '", given "' . $item->getType() . '".');
            }
         }
      }
   }

   public function append($value) {
      if (!($value instanceof $this->type)) {
         throw new ErrorException('Invalid item type: expected "' . $this->type . '", given "' . $value->getType() . '".');
      }

      if (!array_search($value, $this->items)) {
         $set = $this->repository->getSet($this->type);
         $set->add($value);

         $this->items[] = $value;
      }
   }

   public function remove($value) {
      if (!($value instanceof $this->type)) {
         throw new ErrorException('Invalid item type: expected "' . $this->type . '", given "' . $value->getType() . '".');
      }

      if (array_search($value, $this->items)) {
         $set = $this->repository->getSet($this->type);
         unset($this->items[$value]);
         $set->remove($value);
      } else {
         throw new ErrorException('Object "' . $value . '" not a reference entity of "' . $this . '".');
      }
   }

   public function elementAt($index) {
      return $this->items[$index];
   }

   public function getIterator() {
      return new ArrayIterator($this->items);
   }

   public function getType() {
      return $this->type;
   }
}