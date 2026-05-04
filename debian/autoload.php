<?php

declare(strict_types=1);

require_once '/usr/share/php/Composer/InstalledVersions.php';
require_once '/usr/share/php/Composer/CaBundle/autoload.php';
require_once '/usr/share/php/Ease/autoload.php';
require_once '/usr/share/php/GuzzleHttp/autoload.php';
require_once '/usr/share/php/GuzzleHttp/Psr7/autoload.php';
require_once '/usr/share/php/GuzzleHttp/Promise/autoload.php';
require_once '/usr/share/php/FioApi/autoload.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'SpojeNet\\FioApi\\';
    if (str_starts_with($class, $prefix)) {
        $file = '/usr/lib/fiobank-statement-tools/SpojeNet/FioApi/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

(function (): void {
    $versions = [];
    foreach (\Composer\InstalledVersions::getAllRawData() as $d) {
        $versions = array_merge($versions, $d['versions'] ?? []);
    }
    $name    = 'unknown';
    $version = '0.0.0';
    $versions[$name] = ['pretty_version' => $version, 'version' => $version,
        'reference' => null, 'type' => 'project', 'install_path' => __DIR__,
        'aliases' => [], 'dev_requirement' => false];
    \Composer\InstalledVersions::reload([
        'root' => ['name' => $name, 'pretty_version' => $version, 'version' => $version,
            'reference' => null, 'type' => 'project', 'install_path' => __DIR__,
            'aliases' => [], 'dev' => false],
        'versions' => $versions,
    ]);
})();
