<?php

require_once 'PHPUnit/Framework.php';
require_once __DIR__ .'/../Core/lib/Environment.php';
spl_autoload_register(array('Environment','auto_load'));