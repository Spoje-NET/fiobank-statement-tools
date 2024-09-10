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

use Ease\Shared;

require_once '../vendor/autoload.php';

\define('APP_NAME', 'FioBank Statement Downloader');
\Ease\Locale::singleton(null, '../i18n', 'fio-statement-tools');

if (\array_key_exists(1, $argv) && $argv[1] === '-h') {
    echo 'fiobank-statement-mailer [recipient1@server.cz,recipient2@server.com] [format] [path/to/.env]';

    exit;
}

Shared::init(['FIO_TOKEN', 'FIO_TOKEN_NAME', 'ACCOUNT_NUMBER'], \array_key_exists(3, $argv) ? $argv[3] : '../.env');
$downloader = new \SpojeNet\FioApi\Downloader(Shared::cfg('FIO_TOKEN'));

if (Shared::cfg('APP_DEBUG', false)) {
    $downloader->logBanner();
}

$start = new \DateTime();
$start->modify('first day of last month');
$end = new \DateTime();
$end->modify('last day of last month');
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

$subject = sprintf(
    _('FIO Statement %s - %s to %s'),
    Shared::cfg('ACCOUNT_NUMBER'),
    $period->getStartDate()->format('m/d/Y'),
    $period->getEndDate()->format('m/d/Y'),
);

$format = \array_key_exists(2, $argv) ? $argv[2] : Shared::cfg('STATEMENTS_FORMAT', 'pdf');
$client = $downloader->getClient();

$url = \FioApi\UrlBuilder::BASE_URL.'by-id/'.Shared::cfg('FIO_TOKEN').'/'.$start->format('Y').'/'.$start->format('n').'/transactions.'.$format;

try {
    $filename = Shared::cfg('STATEMENTS_DIR', sys_get_temp_dir()).'/'.strtolower(Shared::cfg('FIO_TOKEN_NAME')).'-'.$start->format('Y').'_'.$start->format('n').'.'.$format;
    $statements[$start->format('n')] = [Shared::cfg('FIO_TOKEN_NAME') => Shared::cfg('ACCOUNT_NUMBER')];
    $response = $client->request(
        'get',
        $url,
        ['verify' => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath()],
    );
    $saved = file_put_contents($filename, $response->getBody());
    $downloader->addStatusMessage($subject.': '.$filename.' '._('saved'), $saved ? 'success' : 'error');

    if ($saved) {
        $downloaded = [$filename];
        $recipient = Shared::cfg('STATEMENTS_TO', \array_key_exists(1, $argv) ? $argv[1] : '');

        if (empty($recipient)) {
            fwrite(fopen('php://stderr', 'wb'), Shared::appName().': '._('No recipient provided! Check arguments or environment').\PHP_EOL);

            exit(1);
        }

        try {
            $mailer = new \Ease\Mailer($recipient, sprintf(_('Bank Statements %s'), Shared::cfg('ACCOUNT_NUMBER')));
            $headers = [];

            if (Shared::cfg('STATEMENTS_FROM')) {
                $headers['From'] = Shared::cfg('STATEMENTS_FROM');
            } else {
                $mailer->addStatusMessage('💌  The From header not set', 'warning');
            }

            if (Shared::cfg('STATEMENTS_REPLYTO')) {
                $headers['Reply-To'] = Shared::cfg('STATEMENTS_REPLYTO');
            }

            if (Shared::cfg('STATEMENTS_CC')) {
                $headers['Cc'] = Shared::cfg('STATEMENTS_CC');
            }

            $mailer->setMailHeaders($headers);
            $mailer->addText(sprintf(_('Statements from %s to %s'), $start->format('Y-m-d'), $end->format('Y-m-d'))."\n\n");

            foreach ($statements as $stId => $statement) {
                $mailer->addText(_('Statement').' '.(string) ($stId + 1)."\n");
                $mailer->addText("----------------------------\n");

                foreach ($statement as $statementKey => $statementValue) {
                    $mailer->addText($statementKey.': '.(\is_array($statementValue) ? implode(',', $statementValue) : (string) $statementValue)."\n");
                }

                $mailer->addText("\n");
            }

            $mailer->addText("\n".sprintf(_('Generated by %s %s.'), Shared::appName(), Shared::AppVersion())."\nhttps://github.com/Spoje-NET/fiobank-statement-tools");

            foreach ($downloaded as $statement) {
                $mailer->addFile($statement, mime_content_type($statement));

                if (file_exists($statement)) {
                    unlink($statement);
                }
            }

            $mailer->send();
        } catch (Exception $exc) {
        }
    }
} catch (\GuzzleHttp\Exception\BadResponseException $e) {
    switch ($e->getCode()) {
        case 409:
            $downloader->addStatusMessage($e->getCode().': '._('You can use one token for API call every 30 seconds'), 'error');

            break;
        case 500:
            $downloader->addStatusMessage($e->getCode().': '._('Server returned 500 Internal Error (probably invalid token?)'), 'error');

            break;
        case 404:
            $downloader->addStatusMessage($e->getCode().': '.sprintf(_('Url not found %s'), $url), 'error');

            break;

        default:
            break;
    }
}
