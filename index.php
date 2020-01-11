<?php

require_once (__DIR__.'\db\dbRepository.php');
require_once (__DIR__.'\db\Connection.php');


$connection = new Connection('connection-string');
$connection->open();

$repository = new DbRepository($connection);
$users = $repository->getUsers()->findById(12);

foreach($users as $user) {
    write('<div>');
    write('<p><b>id:</b>' . $user->id . '</p>');
    write('<p><b>username:</b>' . $user->username . '</p>');
    write('<p><b>age:</b>' . $user->age . '</p>');
    write('<p><b>role:</b>' . $user->role . '</p>');

    foreach($user->emails as $email) {
    write('<p><b>email:</b>' . $email->email . '</p>');
    }

    foreach($user->emails as $email) {
    write('<p><b>email:</b>' . $email->email . '</p>');
    }

    write('</div>');
}

$newUser = new User();
$newUser->username = 'pesho';
$newUser->password = 'pesho1';
$newUser->role = 99;
$repository->getUsers()->add($newUser);

$updateUser = $users[0];
$updateUser->age = 2;

$repository->save();

function write($str) {
   echo $str;
}