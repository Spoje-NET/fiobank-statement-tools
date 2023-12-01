Raiffeisenbank Statement Downloader
===================================

![raiffeisenbank-statement-downloader](raiffeisenbank-statement-downloader.svg?raw=true)

Usage
-----

```shell
raiffeisenbank-statement-downloader [save/to/directory] [format] [path/to/.env]
```

Example output when EASE_LOGGER=console

```
12/01/2023 16:37:10 ⚙ ❲RaiffeisenBank Statement Downloader⦒123456789@VitexSoftware\Raiffeisenbank\Statementor❳ Request statements from 2023-11-30 to 2023-11-30
12/01/2023 16:37:13 🌼 ❲RaiffeisenBank Statement Downloader⦒123@VitexSoftware\Raiffeisenbank\Statementor❳ 10_2023_123_3780381_CZK_2023-11-01.xml saved
12/01/2023 16:37:13 ℹ ❲RaiffeisenBank Statement Downloader⦒123456789@VitexSoftware\Raiffeisenbank\Statementor❳ Download done. 1 of 1 saved

```

Configuration
-------------

Please set this environment variables or specify path to .env file

* `CERT_FILE`='RAIFF_CERT.p12'
* `CERT_PASS`=CertPass
* `XIBMCLIENTID`=PwX4XXXXXXXXXXv6I
* `ACCOUNT_NUMBER`=666666666
* `ACCOUNT_CURRENCY`=CZK
* `STATEMENT_FORMAT`=pdf | xml | MT940
* `STATEMENT_LINE`=MAIN
* `STATEMENT_IMPORT_SCOPE`=last_two_months
* `STATEMENTS_DIR`=~/Documents/


* `API_DEBUG`=True
* `APP_DEBUG`=True
* `EASE_LOGGER`=syslog|eventlog|console

Availble Import Scope Values
----------------------------

* 'yesterday'
* 'current_month'
* 'last_month'
* 'last_two_months'
* 'previous_month'
* 'two_months_ago'
* 'this_year'
* 'January'
* 'February'
* 'March'
* 'April'
* 'May'
* 'June'
* 'July'
* 'August'
* 'September'
* 'October'
* 'November',
* 'December'

Created using the library [php-rbczpremiumapi](https://github.com/VitexSoftware/php-vitexsoftware-rbczpremiumapi)
