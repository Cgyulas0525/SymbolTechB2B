<?php

class oop {
     public $path;

     function __construct() {
         $this->path = dirname(__DIR__,2) . '/public/xml/';
     }

     function getPath() {
         return $this->path;
     }
}
