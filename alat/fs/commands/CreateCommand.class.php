<?php

namespace alat\fs\commands;

class CreateCommand extends \alat\fs\commands\Command {
   private $fields;
   private $type;

   private $id;

   public function __construct($path, $type, $fields) {
      parent::__construct($path);
      $this->type = $type;
      $this->fields = $fields;

      $this->command = 'create (' . \alat\io\Path::combine($this->path, $this->type) . ')';
   }

   public function execute() {
      parent::execute();

      $id = 0;

      $path = \alat\io\Path::combine($this->path, $this->type);
      $files = \alat\io\Directory::getFiles($path);
      $lastKey = array_key_last($files);

      if (!is_null($lastKey)) {
         $lastValue = $files[$lastKey];
         $id = intval($lastValue);
      }

      $this->fields['id'] = $id;
      ksort($this->fields);

      $content = null;
      do {
         $id++;
         $this->fields['id'] = $id;
         $content = json_encode($this->fields);
      } while(!\alat\io\File::writeNewFile(\alat\io\Path::combine($this->path, $this->type, $id), $content));

      $this->id = $id;
   }

   public function getId() {
      return $this->id;
   }
}