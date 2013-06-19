<?php

if (!file_exists($file = __DIR__.'/../vendor/autoload.php')) {
    die(<<<'EOT'
You must set up the project dependencies, run the following commands:
wget http://getcomposer.org/composer.phar
php composer.phar install
EOT
    );
}

$loader = require_once $file;

$loader->add('MongoMQ', __DIR__ . '/../tests');
