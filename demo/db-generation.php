<?php

$generator = new \alat\db\generation\Generator(__DIR__);

write('<div> <h1>DB generation</h1>');

write('<code>');

write($generator->getInitializationScript());

write('</code>');

write('</div>');

function write($str) {
   echo $str;
}