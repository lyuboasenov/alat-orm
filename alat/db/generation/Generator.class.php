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
      $id = uniqid();
      $descriptors = $this->getModelDescriptors();

      $currentStateDbTables = $this->getCurrentTables($descriptors);
      $refTables = $this->getTablesByRef($refPoint);

      $script = '';
      if (is_null($refTables)) {
         foreach($currentStateDbTables as $table) {
            $script .= $table->toCreateSql() . Environment::newLine();
         }
      } else {
         foreach($refTables as $table) {
            if (!array_key_exists($table->getName(), $currentStateDbTables)) {
               $script .= $table->toDropSql() . Environment::newLine();
            }
         }

         foreach($currentStateDbTables as $table) {
            if (!array_key_exists($table->getName(), $refTables)) {
               $script .= $table->toCreateSql() . Environment::newLine();
            } else {
               $script .= $table->toUpdateSql($refTables[$table->getName()]) . Environment::newLine();
            }
         }
      }

      if ($script != '') {

         $revisionScript = '';

         if (is_null($refPoint)) {
            $revisionScript .= 'create table _revisions_ (' . Environment::newLine() .
               '   revision varchar(13) charset utf8 not null,' .Environment::newLine() .
               '   date timestamp not null default NOW(),' .Environment::newLine() .
               ');' . Environment::newLine();
         } else {
            $revisionScript .= 'set @lastRevision = (select top 1 revision from _revision_ order by date desc);' . Environment::newLine();
            $revisionScript .= 'if lastRevision <> \'' . $refPoint . '\' then signal sqlstate \'45000\' set message_text \'DB revision differece the expected ' . $refPoint . '\'; endif;' . Environment::newLine();
         }

         $revisionScript .= 'insert into _revisions_ (revision) values (\'' . $id . '\');' . Environment::newLine();

         $script = $revisionScript . $script;

         $this->log[] = ['id' => $id, 'date' => getdate(), 'message' => $message, 'data' => $currentStateDbTables];
         //$this->saveLog();
      }

      return $script;
   }

   public function getUpgradeLog() {
      $result = array();
      if (!is_null($this->log)) {
         foreach($this->log as $entry) {
            $result[] = array_filter($entry, function($key) {
               return array_search($key, ['date', 'id', 'message']) !== false;
            }, ARRAY_FILTER_USE_KEY);
         }
      }
      return $result;
   }

   private function loadLog() {
      if (\alat\io\file::exists($this->logPath)) {
         $content = \alat\io\File::readAsString($this->logPath);
         $this->log = json_decode($content, true, JSON_THROW_ON_ERROR | JSON_ERROR_SYNTAX);
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
      return $script;
   }

   private function getCurrentTables($descriptors) {
      return Table::buildSchemas($descriptors);
   }

   private function getTablesByRef($ref) {
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