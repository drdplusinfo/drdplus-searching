<?php
if (PHP_MAJOR_VERSION >= 7) {
    include __DIR__ . '/declare_strict_types.php';
}

require_once __DIR__ . '/../../vendor/autoload.php';

error_reporting(-1);
ini_set('display_errors', '1');

ini_set('xdebug.max_nesting_level', '100');

if ((int)ini_get('zend.assertions') !== 1) {
    trigger_error("Assert() wil not be evaluated. Please set in on in php.ini for testing\n", E_USER_WARNING);
}
ini_set('assert.active', '1'); // enable assert() evaluation
ini_set('assert.except ion', '1'); // throw an exception instead of an error