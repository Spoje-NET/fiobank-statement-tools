<?php

/**
 * RaiffeisenBank - Statements downloader.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.com>
 * @copyright  (C) 2023 Spoje.Net
 */

namespace SpojeNet\RaiffeisenBank;

use Ease\Shared;
use VitexSoftware\Raiffeisenbank\Statementor;
use VitexSoftware\Raiffeisenbank\ApiClient;

require_once('../vendor/autoload.php');

define('APP_NAME', 'RaiffeisenBank Statement Downloader');

if (array_key_exists(1, $argv) && $argv[1] == '-h') {
    echo 'raiffeisenbank-statement-downloader [save/to/directory] [format] [path/to/.env]';
    exit;
}

Shared::init(['CERT_FILE', 'CERT_PASS', 'XIBMCLIENTID', 'ACCOUNT_NUMBER'], array_key_exists(3, $argv) ? $argv[3] : '../.env');
ApiClient::checkCertificatePresence(Shared::cfg('CERT_FILE'));
$engine = new Statementor(Shared::cfg('ACCOUNT_NUMBER'));
$engine->setScope(Shared::cfg('STATEMENT_IMPORT_SCOPE', 'last_month'));
$statements = $engine->getStatements(Shared::cfg('ACCOUNT_CURRENCY', 'CZK'), Shared::cfg('STATEMENT_LINE', 'MAIN'));
if (empty($statements) === false) {
    $engine->download(
        array_key_exists(1, $argv) ? $argv[1] : Shared::cfg('STATEMENTS_DIR', getcwd()),
        $statements,
        array_key_exists(2, $argv) ? $argv[2] : Shared::cfg('STATEMENT_FORMAT', 'pdf')
    );
} else {
    echo "no statements returned\n";
}
