<?php

write('<div> <h1>DB generation</h1>');

write('<code>');

write(alat\db\DbGenerator::getDbGenerationScript());

write('</code>');

write('</div>');

function write($str) {
   echo $str;
}