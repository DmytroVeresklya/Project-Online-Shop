<?php

use App\Kernel;
// php -r "copy('https://symfony.com/favicon.ico', 'public/favicon.ico')"
// check and create .env.local

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
