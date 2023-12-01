<?php

/**
 * RaiffeisenBank - Statements downloader.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.com>
 * @copyright  (C) 2023 Spoje.Net
 */

namespace SpojeNet\RaiffeisenBank;

require_once('../vendor/autoload.php');

define('APP_NAME', 'RaiffeisenBank Statement Downloader');

\Ease\Shared::init(['CERT_FILE', 'CERT_PASS', 'XIBMCLIENTID', 'ACCOUNT_NUMBER'], array_key_exists(2, $argv) ? $argv[2] : '../.env');
\VitexSoftware\Raiffeisenbank\ApiClient::checkCertificatePresence(\Ease\Shared::cfg('CERT_FILE'));
$engine = new \VitexSoftware\Raiffeisenbank\Statementor(\Ease\Shared::cfg('ACCOUNT_NUMBER'));
$engine->setScope(\Ease\Shared::cfg('STATEMENT_IMPORT_SCOPE', 'last_month'));
$engine->download(array_key_exists(1, $argv) ? $argv[1] : \Ease\Shared::cfg('STATEMENTS_DIR', getcwd()), $engine->getStatements(\Ease\Shared::cfg('ACCOUNT_CURRENCY', 'CZK'), \Ease\Shared::cfg('STATEMENT_LINE', 'MAIN')));
