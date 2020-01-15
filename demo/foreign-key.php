<?php

$connection = new alat\db\SqlConnection('connection-string');

//$repository = new alat\db\Repository($connection);
$repository = new \alat\fs\Repository(__DIR__ . '\\repo\\');

write('<div> <h1>Foreign Key</h1>');

$parentSet = $repository->getSet('demo\domain\models\ParentEntity');
$parents = $parentSet->all();

foreach($parents as $parent) {
   write('<div>');
   write('<p><b>id:</b>' . $parent->id . '</p>');
   write('<p><b>name:</b>' . $parent->name . '</p>');

   foreach($parent->children as $child) {
      write('<p><b>child:</b>' . $child->name . '</p>');
   }

   write('</div>');
}

$newParent = new demo\domain\models\ParentEntity();
$newParent->name = 'new-parent';

$newParent = $parentSet->add($newParent);

$newChild = new demo\domain\models\ChildEntity();
$newChild->name = 'new-child';
$newChild = $newParent->children->append($newChild);

$updateParent = $parents[0];
$updateParent->name = 'update-parent';

write('</div>');

$repository->save();

function write($str) {
   echo $str;
}