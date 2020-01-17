<?php

$generator = new \alat\db\generation\Generator(__DIR__);

write('<div> <h1>DB generation</h1>');

write('<pre>');

write('<h2>DB initialization</h2>');
write($generator->getInitializationScript());

write('</pre>');

write('</div>');

function write($str) {
   echo $str;
}