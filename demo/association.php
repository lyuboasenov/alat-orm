<?php

$connection = new alat\db\SqlConnection('connection-string');

//$repository = new alat\db\Repository($connection);
$repository = new \alat\fs\Repository(__DIR__ . '\\repo\\');

write('<div> <h1>Association</h1>');

$parents = $repository->getSet('demo\domain\models\MultiParentEntity')->findById(12);

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

$newParent = new demo\domain\models\MultiParentEntity();
$newParent->name = 'new-parent';

$newParent = $repository->getSet('demo\domain\models\MultiParentEntity')->add($newParent);

$newChild = new demo\domain\models\MultiChildEntity();
$newChild->name = 'new-child';

$updateParent = $parents[0];
$updateParent->name = 'update-parent';
$newChild = $updateParent->children->append($newChild);

write('</div>');

$repository->save();

function write($str) {
   echo $str;
}