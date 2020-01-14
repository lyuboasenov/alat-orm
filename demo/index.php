<?php

$connection = new alat\db\SqlConnection('connection-string');

//$repository = new alat\db\Repository($connection);
$repository = new \alat\fs\Repository(__DIR__ . '\\repo\\');

write('<div> Foreign Key');

$parents = $repository->getSet('demo\domain\models\ParentEntity')->findById(12);

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

$newParent = new demo\domain\models\ParentEntity();
$newParent->name = 'new-parent';

$newParent = $repository->getSet('demo\domain\models\ParentEntity')->add($newParent);

$newChild = new demo\domain\models\ChildEntity();
$newChild->name = 'new-child';

$updateParent = $parents[0];
$updateParent->name = 'update-parent';
$newChild = $updateParent->children->append($newChild);

write('</div>');

write('<div> Association');

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

write('<div> DB generation');

write('<code>');

write(alat\db\DbGenerator::getDbGenerationScript());

write('</code>');

write('</div>');



function write($str) {
   echo $str;
}