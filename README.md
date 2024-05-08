Fiobank Statement Downloader
===================================

![fiobank-statement-downloader](fiobank-statement-downloader.svg?raw=true)

Usage
-----

```shell
fiobank-statement-downloader [save/to/directory] [format] [path/to/.env]
```

Example output when EASE_LOGGER=console

```
01/16/2024 16:46:11 ⚙ ❲FioBank Statement Downloader⦒SpojeNet\FioApi\Downloader❳ FioBank Statement Downloader EaseCore dev-main (PHP 8.3.1)
01/16/2024 16:46:12 🌼 ❲FioBank Statement Downloader⦒SpojeNet\FioApi\Downloader❳ Výpis z účtu FIO - 12/01/23 to 12/31/23: hlavni_fio-2023_12.pdf saved
```

Configuration
-------------

Please set this environment variables or specify path to .env file

* `FIO_TOKEN`='KitMuWyajissajPishtuwolth8ojyukMaldryavAcsOotuhuaksaf'
* `FIO_TOKEN_NAME`='Fio Main'
* `ACCOUNT_NUMBER`=666666666


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

fiobank-transaction-mailer
--------------------------

![Mailer](fiobank-statement-mailer.svg?raw=true)

Share configuration with downloader and use few own keys:

* `STATEMENTS_FROM`
* `STATEMENTS_REPLYTO`
* `STATEMENTS_CC`
* `EASE_SMTP` - optional json string `{"port": "587", "starttls": true, "auth": true, "host": "smtp.office365.com", "username": "@spojenet.cz", "password": "pw"}`


fiobank-transaction-report
--------------------------

![Report](fiobank-transaction-report.svg?raw=true)


export fio transactions overview as json


Created using the library [fio-api-php](https://github.com/mhujer/fio-api-php)

MultiFlexi
----------

FioBank statement downloader is ready for run as [MultiFlexi](https://multiflexi.eu) application.
See the full list of ready-to-run applications within the MultiFlexi platform on the [application list page](https://www.multiflexi.eu/apps.php).

[![MultiFlexi App](https://github.com/VitexSoftware/MultiFlexi/blob/main/doc/multiflexi-app.svg)](https://www.multiflexi.eu/apps.php)
