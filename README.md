Raiffeisenbank Statement Downloader
===================================

![raiffeisenbank-statement-downloader](raiffeisenbank-statement-downloader.svg?raw=true)

Usage
-----

```shell
raiffeisenbank-statement-downloader [save/to/directory] [path/to/.env]
```

Configuration
-------------

Please set this environment variables or specify path to .env file

* `CERT_FILE`='RAIFF_CERT.p12'
* `CERT_PASS`=CertPass
* `XIBMCLIENTID`=PwX4XXXXXXXXXXv6I
* `ACCOUNT_NUMBER`=666666666
* `ACCOUNT_CURRENCY`=CZK
* `STATEMENT_LINE`=MAIN
* `STATEMENT_IMPORT_SCOPE`=last_two_months
* `TRANSACTION_IMPORT_SCOPE`=yesterday
* `STATEMENTS_DIR`=~/Documents/

* `API_DEBUG`=True
* `APP_DEBUG`=True
* `EASE_LOGGER`=syslog|console

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
