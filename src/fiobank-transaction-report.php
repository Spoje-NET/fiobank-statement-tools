<?php

/**
 * fiobank - Statements downloader.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.com>
 * @copyright  (C) 2024 Spoje.Net
 */

namespace SpojeNet\FioApi;

use Ease\Shared;

require_once('../vendor/autoload.php');

define('APP_NAME', 'FioBank Statement Reporter');

Shared::init(['FIO_TOKEN', 'FIO_TOKEN_NAME', 'ACCOUNT_NUMBER'], array_key_exists(1, $argv) ? $argv[1] : '../.env');
$downloader = new \SpojeNet\FioApi\Downloader(\Ease\Shared::cfg('FIO_TOKEN'));

if (\Ease\Shared::cfg('APP_DEBUG', false)) {
    $downloader->logBanner();
}
$downloader->setScope('last_month');

$payments = [
    'source' => \Ease\Logger\Message::getCallerName($downloader),
    'account' => Shared::cfg('ACCOUNT_NUMBER'),
    'in' => [],
    'out' => [],
    'in_total' => 0,
    'out_total' => 0,
    'in_sum_total' => 0,
    'out_sum_total' => 0,
    'from' => $downloader->getSince()->format('Y-m-d'),
    'to' => $downloader->getUntil()->format('Y-m-d')
];

$transactionList = $downloader->downloadFromTo($downloader->getSince(), $downloader->getUntil());
if (empty($transactionList) === false) {
    $payments['iban'] = $transactionList->getAccount()->getIban();
    foreach ($transactionList->getTransactions() as $transaction) {
        $type = $transaction->getTransactionType();
        switch ($type) {
            case 'Příjem převodem uvnitř banky':
            case 'Vklad pokladnou':
            case 'Vklad v hotovosti':
            case 'Příjem':
            case 'Bezhotovostní příjem':
            case 'Připsaný úrok':
            case 'Okamžitá příchozí platba':
            case 'Převod mezi bankovními konty (příjem)':
            case 'Neidentifikovaný příjem na bankovní konto':
            case 'Vlastní příjem na bankovní konto':
            case 'Vlastní příjem pokladnou':
            case 'Příjem inkasa z cizí banky':
            case 'Posel – příjem':
                $direction = true;
                break;
            case 'Posel – předání':
            case 'Platba převodem uvnitř banky':
            case 'Výběr pokladnou':
            case 'Výběr v hotovosti':
            case 'Platba':
            case 'Bezhotovostní platba':
            case 'Platba kartou':
            case 'Úrok z úvěru':
            case 'Sankční poplatek':
            case 'Okamžitá odchozí platba':
            case 'Platba v jiné měně':
            case 'Vlastní platba z bankovního konta':
            case 'Vlastní platba pokladnou':
            case 'Převod mezi bankovními konty (platba)':
                $direction = false;
                break;

            case 'Převod uvnitř konta':
            case 'Vyplacený úrok':
            case 'Odvod daně z úroků':
            case 'Evidovaný úrok':
            case 'Poplatek':
            case 'Evidovaný poplatek':
            case 'Neidentifikovaná platba z bankovního konta':
            case 'Opravný pohyb':
            case 'Přijatý poplatek':
            case 'Poplatek – platební karta':
            case 'Inkaso':
            case 'Inkaso ve prospěch účtu':
            case 'Inkaso z účtu':
            case 'Evidovaný úrok':
            case 'Poplatek - pojištění hypoték':
            default:
                $downloader->addStatusMessage(_('Unhandled Operation') . ': ' . $type, 'warning');
                $direction = null;
                break;
        }


        if (is_bool($direction)) {
            $payments[$direction ? 'in' : 'out'][$transaction->getId()] = $transaction->getAmount();
            $payments[$direction ? 'in_sum_total' : 'out_sum_total'] += $transaction->getAmount();
            $payments[$direction ? 'in_total' : 'out_total'] += 1;
        }
    }
    echo json_encode($payments, \Ease\Shared::cfg('DEBUG') ? JSON_PRETTY_PRINT : 0);
} else {
    echo "no statements returned\n";
}
