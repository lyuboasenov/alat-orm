<?php

$connection = new \alat\db\SqlConnection('connection-string');

//$repository = new \alat\db\Repository($connection);
$repository = new \alat\fs\Repository(__DIR__ . '\\repo\\');
$set = $repository->getSet('demo\domain\models\MultiParentEntity');

write('<div> <h1>Association</h1>');

$parents = $set->all();

foreach($parents as $parent) {
   write('<div>');
   write('<p><b>id:</b>' . $parent->id . '</p>');
   write('<p><b>name:</b>' . $parent->name . '</p>');

   foreach($parent->children as $child) {
      write('<p><b>child:</b>' . $child->name . '</p>');
   }

   write('</div>');
}

$newParent = new demo\domain\models\MultiParentEntity();
$newParent->name = 'new-parent';

$newParent = $set->add($newParent);

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