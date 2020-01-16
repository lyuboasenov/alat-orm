<?php

namespace alat\db\generation;

use alat\domain\models\fields\ReferenceField;

class Generator {
   private $logPath;
   private $log = array();

   public function __construct($path) {
      $this->logPath = \alat\io\Path::combine($path, 'db', 'generation', 'log');
      $this->loadLog();
   }

   public function getInitializationScript($message = 'Db initialization script.') {
      return $this->getUpgradeScript(null, $message);
   }

   public function getUpgradeScript($refPoint, $message) {
      $script = $this->getCurrentScript();
      $refScript = $this->getScript($refPoint);

      if (!is_null($refScript)) {
         $script = $this->makeDiff($refScript, $script);
      }

      $this->log[] = ['id' => uniqid(), 'date' => getdate(), 'message' => $message, 'data' => $script];

      $this->saveLog();

      return $this->toSql($script);
   }

   public function getUpgradeLog() {
      $result = array();
      foreach($this->log as $entry) {
         $result[] = array_filter($entry, function($key) {
            return array_search($key, ['date', 'id', 'message']);
         }, ARRAY_FILTER_USE_KEY);
      }
      return $result;
   }

   private function loadLog() {
      if (\alat\io\file::exists($this->logPath)) {
         $content = \alat\io\File::readAsString($this->logPath);
         $log = json_decode($content, true);
      }
   }

   private function saveLog() {
      \alat\io\File::writeFile($this->logPath, json_encode($this->log));
   }

   private function getScript($refPoint) {
      $script = null;

      if (!is_null($refPoint)) {
         foreach($this->log as $entry) {
            if ($entry['id'] == $refPoint) {
               $result = $entry;
               break;
            }
         }
      }

      return $script;
   }

   private function getCurrentScript() {
      $classes = get_declared_classes();
      $tables = array();
      $fks = array();
      $modelDescriptors = array();

      echo '<pre>';
      var_dump($classes);
      echo '</pre>';

      foreach($classes as $class) {
         $reflect = new \ReflectionClass($class);
         if($reflect->implementsInterface('alat\domain\models\IModelDescriptor')
            && !$reflect->isAbstract()) {

               var_dump(1);

            $descriptor = new $class;
            $modelDescriptors[] = $descriptor;

            $descriptorName = \alat\common\Type::stripNamespace($class);
            $tables[] = [rtrim($descriptorName, 'Descriptor'), $this->getColumns($descriptor)];
         }
      }

      return ['tables' => $tables, 'fks' => $fks];
   }

   private function getColumns($descriptor) {
      $columns = array();
      foreach($descriptor->getFields() as $field) {
         if (!($field instanceof ReferenceField)) {
            $columns[] = Column::fromField($field);
         }
      }
   }

   private function makeDiff($from, $to) {
      //TODO:

      return $to;
   }

   private function toSql($script) {
      var_dump($script);

      return $script;
   }
}