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

Created using the library [php-rbczpremiumapi](https://github.com/VitexSoftware/php-vitexsoftware-rbczpremiumapi)
