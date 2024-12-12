<?php

declare(strict_types=1);

/**
 * This file is part of the FioBank statement Tools  package
 *
 * https://github.com/Spoje-NET/fiobank-statement-tools
 *
 * (c) Spoje.Net <https://spoje.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SpojeNet\FioApi;

use Ease\Shared;

require_once '../vendor/autoload.php';

\define('APP_NAME', 'FioBank Statement Reporter');
$exitCode = 0;
$options = getopt('o::e::', ['output::environment::']);
Shared::init(['FIO_TOKEN', 'FIO_TOKEN_NAME', 'ACCOUNT_NUMBER'], \array_key_exists(1, $argv) ? $argv[1] : '../.env');
\Ease\Locale::singleton(null, '../i18n', 'fio-statement-tools');

$destination = \array_key_exists('output', $options) ? $options['output'] : Shared::cfg('RESULT_FILE', 'php://stdout');
$downloader = new \SpojeNet\FioApi\Downloader(Shared::cfg('FIO_TOKEN'));

if (Shared::cfg('APP_DEBUG', false)) {
    $downloader->logBanner();
}

$downloader->setScope(Shared::cfg('TRANSACTION_SCOPE', 'last_month'));

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
    'to' => $downloader->getUntil()->format('Y-m-d'),
];

try {
    $transactionList = $downloader->downloadFromTo($downloader->getSince(), $downloader->getUntil());
} catch (\GuzzleHttp\Exception\BadResponseException $e) {
    $exitCode = $e->getCode();

    switch ($e->getCode()) {
        case 409:
            $downloader->addStatusMessage($e->getCode().': '._('You can use one token for API call every 30 seconds'), 'error');

            break;
        case 500:
            $downloader->addStatusMessage($e->getCode().': '._('Server returned 500 Internal Error (probably invalid token?)'), 'error');

            break;
        case 404:
            $downloader->addStatusMessage($e->getCode().': '.sprintf(_('Url not found %s'), $e->getRequest()->getUri()), 'error');

            break;

        default:
            $downloader->addStatusMessage($e->getCode().': '.$e->getMessage(), 'error');

            break;
    }
}

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
            case 'Poplatek - pojištění hypoték':
            default:
                $downloader->addStatusMessage(_('Unhandled Operation').': '.$type, 'warning');
                $direction = null;

                break;
        }

        if (\is_bool($direction)) {
            $payments[$direction ? 'in' : 'out'][$transaction->getId()] = $transaction->getAmount();
            $payments[$direction ? 'in_sum_total' : 'out_sum_total'] += $transaction->getAmount();
            ++$payments[$direction ? 'in_total' : 'out_total'];
        }
    }
} else {
    $payments['status'] = 'no statements returned';
}

$written = file_put_contents($destination, json_encode($payments, Shared::cfg('DEBUG') ? \JSON_PRETTY_PRINT : 0));
$downloader->addStatusMessage(sprintf(_('Saving result to %s'), $destination), $written ? 'success' : 'error');

exit($exitCode ?: ($written ? 0 : 1));
