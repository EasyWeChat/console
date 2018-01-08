<?php

require __DIR__.'/../vendor/autoload.php';

use EasyWeChat\Console\Application;
use EasyWeChat\Console\Commands\Payment\RsaPublicKey;

$application = new Application();

$application->add(new RsaPublicKey());

$application->run();