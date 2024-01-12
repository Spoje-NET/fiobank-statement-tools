<?php

/**
 * RaiffeisenBank - Statements downloader.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.com>
 * @copyright  (C) 2024 Spoje.Net
 */

namespace SpojeNet\RaiffeisenBank;

use Ease\Shared;
use VitexSoftware\Raiffeisenbank\Statementor;
use VitexSoftware\Raiffeisenbank\ApiClient;

require_once('../vendor/autoload.php');

define('APP_NAME', 'RaiffeisenBank Statement Reporter');

Shared::init(['CERT_FILE', 'CERT_PASS', 'XIBMCLIENTID', 'ACCOUNT_NUMBER'], array_key_exists(3, $argv) ? $argv[3] : '../.env');
ApiClient::checkCertificatePresence(Shared::cfg('CERT_FILE'));
$engine = new Statementor(Shared::cfg('ACCOUNT_NUMBER'));
$engine->setScope(Shared::cfg('STATEMENT_IMPORT_SCOPE', 'last_month'));

if (\Ease\Shared::cfg('APP_DEBUG', false)) {
    $engine->logBanner();
}
$engine->setScope(Shared::cfg('STATEMENT_IMPORT_SCOPE', 'last_month'));
$statements = $engine->getStatements(Shared::cfg('ACCOUNT_CURRENCY', 'CZK'), Shared::cfg('STATEMENT_LINE', 'MAIN'));
$payments = [
    'source' => \Ease\Logger\Message::getCallerName($engine),
    'account' => Shared::cfg('ACCOUNT_NUMBER'),
    'in' => [],
    'out' => [],
    'in_total' => 0,
    'out_total' => 0,
    'in_sum_total' => 0,
    'out_sum_total' => 0,
    'from' => $engine->getSince()->format('Y-m-d'),
    'to' => $engine->getUntil()->format('Y-m-d')
];

if (empty($statements) === false) {
    foreach ($engine->download(sys_get_temp_dir(), $statements, 'xml') as $statement => $xmlFile) {
        // ISO 20022 XML to transaction array
        $statementArray = json_decode(json_encode(simplexml_load_file($xmlFile)), true);

        $payments['iban'] = $statementArray['BkToCstmrStmt']['Stmt']['Acct']['Id']['IBAN'];

        $entries = (array_key_exists('Ntry', $statementArray['BkToCstmrStmt']['Stmt']) ? (array_keys($statementArray['BkToCstmrStmt']['Stmt']['Ntry'])[0] == 0 ? $statementArray['BkToCstmrStmt']['Stmt']['Ntry'] : [$statementArray['BkToCstmrStmt']['Stmt']['Ntry']] ) : []);
        foreach ($entries as $payment) {
            $payments[$payment['CdtDbtInd'] == 'CRDT' ? 'in' : 'out'][$payment['BookgDt']['DtTm']] = $payment['Amt'];
            $payments[$payment['CdtDbtInd'] == 'CRDT' ? 'in_sum_total' : 'out_sum_total'] += floatval($payment['Amt']);
            $payments[$payment['CdtDbtInd'] == 'CRDT' ? 'in_total' : 'out_total'] += 1;
        }
        unlink($xmlFile);
    }
    echo json_encode($payments, \Ease\Shared::cfg('DEBUG') ? JSON_PRETTY_PRINT : 0);
} else {
    echo "no statements returned\n";
}
