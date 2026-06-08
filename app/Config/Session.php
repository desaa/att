<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Session\Handlers\FileHandler;

class Session extends BaseConfig
{
    public string $driver = FileHandler::class;
    public string $cookieName = 'ci_session';
    public int $expiration = 1800;
    public string $savePath = '/home/d1sk0m1nv0/public_html/adatamu/writable/session';
    public bool $matchIP = false;
    public int $timeToUpdate = 300;
    public bool $regenerateDestroy = false;
}