<?php

require_once (__DIR__.'\db\dbRepository.php');
require_once (__DIR__.'\db\Connection.php');


$connection = new Connection('connection-string');
$connection->open();

$repository = new DbRepository($connection);
$parents = $repository->getSet('ParentEntity')->findById(12);

foreach($parents as $parent) {
   write('<div>');
   write('<p><b>id:</b>' . $parent->id . '</p>');
   write('<p><b>name:</b>' . $parent->name . '</p>');

   foreach($parent->children as $child) {
      write('<p><b>child:</b>' . $child->name . '</p>');
   }

   write('<p><b>child:</b>' . $parent->children->elementAt(1)->name . '</p>');

   write('</div>');
}

$newParent = new ParentEntity();
$newParent->name = 'new-parent';

$repository->getSet('ParentEntity')->add($newParent);

$newChild = new ChildEntity();
$newChild->name = 'new-child';

$updateParent = $parents[0];
$updateParent->name = 'update-parent';
$updateParent->children->append($newChild);

$repository->save();

function write($str) {
   echo $str;
}