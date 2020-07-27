<?php
require __DIR__.'/../vendor/autoload.php';
require 'AppUtility.php';

$path = $argv[1];

AppUtility::initiateCommisionHandling($path);

