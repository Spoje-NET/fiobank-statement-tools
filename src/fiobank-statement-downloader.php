<?php

/**
 * FioBank - Statements downloader.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.com>
 * @copyright  (C) 2024 Spoje.Net
 */

use Ease\Shared;

require_once('../vendor/autoload.php');

define('APP_NAME', 'FioBank Statement Downloader');

if (array_key_exists(1, $argv) && $argv[1] == '-h') {
    echo 'fiobank-statement-downloader [save/to/directory] [format] [path/to/.env]';
    exit;
}

Shared::init(['FIO_TOKEN', 'FIO_TOKEN_NAME', 'ACCOUNT_NUMBER'], array_key_exists(3, $argv) ? $argv[3] : '../.env');
$downloader = new \SpojeNet\FioApi\Downloader(\Ease\Shared::cfg('FIO_TOKEN'));

if (\Ease\Shared::cfg('APP_DEBUG', false)) {
    $downloader->logBanner();
}

$start = new \DateTime();
$start->modify('first day of last month');
$end = new \DateTime();
$end->modify('last day of last month');
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

$subject = sprintf(
    _('Výpis z účtu FIO Spoje.Net - %s to %s'),
    \strftime('%x', $period->getStartDate()->getTimestamp()),
    \strftime('%x', $period->getEndDate()->getTimestamp())
);

$destDir = array_key_exists(1, $argv) ? $argv[1] : getcwd() . '/';
$format = array_key_exists(2, $argv) ? $argv[2] : 'pdf';

$client = $downloader->getClient();

$url = \FioApi\UrlBuilder::BASE_URL . 'by-id/' . \Ease\Shared::cfg('FIO_TOKEN') . '/' . $start->format('Y') . '/' . $start->format('n') . '/transactions.' . $format;
try {
    $filename = $destDir . strtolower(\Ease\Shared::cfg('FIO_TOKEN_NAME')) . '-' . $start->format('Y') . '_' . $start->format('n') . '.' . $format;
    /** @var ResponseInterface $response */
    $response = $client->request(
        'get',
        $url,
        ['verify' => $downloader->getCertificatePath()]
    );
    $saved = file_put_contents($filename, $response->getBody());
    $downloader->addStatusMessage($subject . ': ' . $filename . ' ' . _('saved'), $saved ? 'success' : 'error');
    exit($saved ? 0 : 1);
} catch (\GuzzleHttp\Exception\BadResponseException $e) {
    switch ($e->getCode()) {
        case 409:
            $downloader->addStatusMessage($e->getCode() . ': ' . _('You can use one token for API call every 30 seconds'), 'error');
            break;
        case 500:
            $downloader->addStatusMessage($e->getCode() . ': ' . _('Server returned 500 Internal Error (probably invalid token?)'), 'error');
            break;
        case 404:
            $downloader->addStatusMessage($e->getCode() . ': ' . sprintf(_('Url not found %s'), $url), 'error');
            break;
        default:
            break;
    }
}


//$engine->setScope(Shared::cfg('STATEMENT_IMPORT_SCOPE', 'last_month'));
//
//$statements = $engine->getStatements(Shared::cfg('ACCOUNT_CURRENCY', 'CZK'), Shared::cfg('STATEMENT_LINE', 'MAIN'));
//
//if (empty($statements) === false) {
//    $engine->download(
//            array_key_exists(1, $argv) ? $argv[1] : Shared::cfg('STATEMENTS_DIR', getcwd()),
//            $statements,
//            array_key_exists(2, $argv) ? $argv[2] : Shared::cfg('STATEMENT_FORMAT', 'pdf')
//    );
//} else {
//    echo "no statements returned\n";
//}
