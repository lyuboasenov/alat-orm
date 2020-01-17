<?php

$generator = new \alat\db\generation\Generator(__DIR__);

write('<div> <h1>DB generation</h1>');

write('<pre>');
write('<h2>Log</h2>');
write('<table>');
write('<th><td>id</td><td>date</td><td>message</td></th>');
$log = $generator->getUpgradeLog();
if (!is_null($log)) {
   foreach($log as $entry) {
      write('<tr>');
      write('<td>' . $entry['id'] . '</td>');
      write('<td>' . date('m/d/Y H:i', $entry['date'][0]) . '</td>');
      write('<td>' . $entry['message'] . '</td>');
      write('</tr>');
   }
}

write('</table>');

write('<pre>');
write('<h2>DB initialization</h2>');
write($generator->getInitializationScript());
write('</pre>');

write('<pre>');
write('<h2>DB upgrade</h2>');
write($generator->getUpgradeScript('5e21cdb16a09c', 'Upgrade script'));
write('</pre>');

write('</div>');

function write($str) {
   echo $str;
}