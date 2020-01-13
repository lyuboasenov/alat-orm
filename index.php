<?php

include 'autoload.php';

$connection = new db\Connection('connection-string');
$connection->open();

write('<div> Foreign Key');

$repository = new db\DbRepository($connection);
$parents = $repository->getSet('domain\entities\ParentEntity')->findById(12);

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

$newParent = new domain\entities\ParentEntity();
$newParent->name = 'new-parent';

$newParent = $repository->getSet('domain\entities\ParentEntity')->add($newParent);

$newChild = new domain\entities\ChildEntity();
$newChild->name = 'new-child';

$updateParent = $parents[0];
$updateParent->name = 'update-parent';
$newChild = $updateParent->children->append($newChild);

write('</div>');

write('<div> Association');

//$repository = new DbRepository($connection);
$parents = $repository->getSet('domain\entities\MultiParentEntity')->findById(12);

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

$newParent = new domain\entities\MultiParentEntity();
$newParent->name = 'new-parent';

$newParent = $repository->getSet('domain\entities\MultiParentEntity')->add($newParent);

$newChild = new domain\entities\MultiChildEntity();
$newChild->name = 'new-child';

$updateParent = $parents[0];
$updateParent->name = 'update-parent';
$newChild = $updateParent->children->append($newChild);

write('</div>');


$repository->save();

function write($str) {
   echo $str;
}