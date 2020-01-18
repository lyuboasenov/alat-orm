<?php

$generator = new \alat\db\generation\Generator(__DIR__);

$initScript = $generator->getInitializationScript();
$upgradeScript = $generator->getUpgradeScript('5e22f9e545efd', 'Upgrade script');

$log = $generator->getUpgradeLog();

write('<div> <h1>DB generation</h1>');

write('<pre>');
write('<h2>Log</h2>');
write('<table>');
write('<th><td>id</td><td>date</td><td>message</td></th>');

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
write($initScript);
write('</pre>');

write('<pre>');
write('<h2>DB upgrade</h2>');
write($upgradeScript);
write('</pre>');

write('</div>');

function write($str) {
   echo $str;
}