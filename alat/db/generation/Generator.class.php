<?php

namespace alat\db\generation;

use alat\common\Type;
use alat\domain\models\fields\ReferenceField;
use alat\Environment;

class Generator {
   private $logPath;
   private $log = array();
   private $appPath;

   public function __construct($appPath) {
      $this->appPath = $appPath;
      $this->logPath = \alat\io\Path::combine($appPath, 'db', 'generation', 'log');
      $this->loadLog();
   }

   public function getInitializationScript($message = 'Db initialization script.') {
      return $this->getUpgradeScript(null, $message);
   }

   public function getUpgradeScript($refPoint, $message) {
      $descriptors = $this->getModelDescriptors();

      $currentStateDbArtefacts = $this->getCurrentArtefacts($descriptors);
      $refArtefacts = $this->getArtefactsByRef($refPoint);

      $script = '';
      if (is_null($refArtefacts)) {
         foreach($currentStateDbArtefacts as $artefact) {
            $script .= $artefact->toSql() . Environment::newLine();
         }
      } else {

      }

      $this->log[] = ['id' => uniqid(), 'date' => getdate(), 'message' => $message, 'data' => $currentStateDbArtefacts];
      $this->saveLog();

      return $script;
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
      \alat\io\File::appendFile($this->logPath, json_encode($this->log));
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

   private function getModelDescriptors() {
      $classes = Type::getTypes($this->appPath);
      $modelDescriptors = array();

      foreach($classes as $class) {
         $reflect = new \ReflectionClass($class);
         if($reflect->implementsInterface('alat\domain\models\IModelDescriptor')
            && !$reflect->isAbstract()) {
            $modelDescriptors[] = new $class;
         }
      }

      return $modelDescriptors;
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

   private function getCurrentArtefacts($descriptors) {
      return Table::buildSchemas($descriptors);
   }

   private function getArtefactsByRef($ref) {
      $result = null;
      if (!is_null($ref)) {
         $entry = $this->getLogEntryById($ref);
         if (!is_null($entry)) {
            $result = Table::buildSchemasFromArray($entry['data']);
         }
      }

      return $result;
   }

   private function getLogEntryById($id) {
      $result = null;
      foreach($this->log as $entry) {
         if ($entry['id'] == $id) {
            $result = $entry;
            break;
         }
      }

      return $result;
   }
}