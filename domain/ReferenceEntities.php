<?php

class ReferenceEntities implements IteratorAggregate {
   private $repository;
   private $field;
   private $type;
   private $items;
   private $parent;

   public function __construct($repository, $field, $parent, $array) {
      $this->repository = $repository;
      $this->field = $field;
      $this->type = $this->field->getReferenceType();
      $this->parent = $parent;
      $this->items = $array;

      if(!is_null($this->items)) {
         foreach($this->items as $item) {
            if (!($item instanceof $this->type)) {
               throw new ErrorException('Invalid item type: expected "' . $this->type . '", given "' . get_class($item) . '".');
            }
         }
      }
   }

   public function append($value) {
      if (!($value instanceof $this->type)) {
         throw new ErrorException('Invalid item type: expected "' . $this->type . '", given "' . get_class($value) . '".');
      }

      if (!array_search($value, $this->items)) {
         $set = $this->repository->getSet($this->type);

         $value->addReference($this->parent);
         $set->add($value);

         $this->items[] = $value;
      }
   }

   public function remove($value) {
      if (!($value instanceof $this->type)) {
         throw new ErrorException('Invalid item type: expected "' . $this->type . '", given "' . get_class($value) . '".');
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
}