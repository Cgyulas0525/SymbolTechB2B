<?php

require_once 'Database.php';

class bladeClass {
    public static $path = NULL;
    public static $fileArray = array();
    public static $textArray = array();

    public static function start()
    {
        self::$path = dirname(__DIR__,2);
    }

    public static function getFolder($path) {
        $files = array_diff(scandir($path), array('.', '..'));
        if (count($files) > 0) {
            foreach($files as $file) {
                if (stripos($file, '.blade.php')) {
                    array_push(self::$fileArray, $path . "/" . $file);
                }
                if ( $file !== 'temp' && $file !== 'vendor' && $file !== 'auth' && $file !== 'errors' && $file !== 'hsjs' && $file !== 'functions') {
                    if (is_dir($path . "/" . $file)) {
                        self::getFolder($path. "/" . $file);
                    }
                }
            }
        }
    }

    public static function textToArray($text) {
        if (!empty($text)) {
            array_push(self::$textArray, $text);
        }
    }

    public static function lineProcessing($line) {
        for ($i = 1; $i < 7; $i++) {
            if (stripos($line, '<h'.$i)) {
                $startPos = stripos($line, '<h'.$i) + 4;
                $endPos = stripos($line, '</h'.$i);
                self::textToArray(substr($line, $startPos, ($endPos - $startPos)));
            }
        }
        if (stripos($line, "{title")) {
            $titlePos = stripos($line, "{title");
            if (!stripos($line, "'<a", $titlePos)) {
                $startPos = stripos($line, "'", $titlePos);
                $endPos = stripos($line, "'", $startPos + 1);
                self::textToArray(substr($line, $startPos + 1, ($endPos - ($startPos + 1))));
            }
        }
        if (stripos($line, "{!! Form::label(")) {
            $titlePos = stripos($line, "{!! Form::label(");
            if (stripos($line, ",", $titlePos)) {
                $pos = stripos($line, ",", $titlePos);
                $startPos = stripos($line, "'", $pos);
                $endPos = stripos($line, "'", $startPos + 1);
                self::textToArray(substr($line, $startPos + 1, ($endPos - ($startPos + 2))));
            }
        }
        if (stripos($line, "<a")) {
            echo $line;
        }
        if (stripos($line, "<p")) {
            echo $line;
        }
    }

}



