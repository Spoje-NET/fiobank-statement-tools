Fiobank Statement Downloader
============================

![fiobank-statement-downloader](fiobank-statement-downloader.svg?raw=true)

Usage
-----

```shell
fiobank-statement-downloader [save/to/directory] [format] [path/to/.env]
```

Example output when EASE_LOGGER=console

```console
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
* `2024-08-05>2024-08-11` - custom scope
* `2024-10-11` - only specific day

fiobank-statement-mailer
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

```json
{
  "source": "SpojeNet\\FioApi\\Downloader",
  "account": "4678357887",
  "in": {
    "26824841497": 605,
    "26824888581": 400,
    "26824925635": 400,
    "26824931682": 400,
    "26824932722": 639,
    "26824933226": 500,
    "26824934478": 500,
    "26824936675": 700,
    "26824937278": 400,
    "26824937528": 600,
    "26824961116": 400,
    "26824973216": 400,
    "26824973399": 400,
    "26825089436": 784,
    "26825089826": 190,
    "26825096119": 600,
    "26825100170": 500,
    "26825108688": 400,
    "26825326207": 530,
    "26825342736": 590,
    "26825354034": 400,
    "26825391533": 400,
    "26825421760": 500,
    "26825440250": 400,
    "26825445534": 500,
    "26825451244": 400,
    "26825485629": 400,
    "26825534661": 400,
    "26825535110": 400,
    "26825536273": 400,
    "26825711691": 2600,
    "26825737686": 400,
    "26825840759": 1742,
    "26825906082": 500,
    "26825921632": 6050,
    "26825965281": 1876,
    "26826064789": 300,
    "26826480759": 500,
    "26826623438": 400,
    "26826651980": 700
  },
  "out": {
    "26825356334": -22581
  },
  "in_total": 40,
  "out_total": 1,
  "in_sum_total": 29206,
  "out_sum_total": -22581,
  "from": "2024-12-11",
  "to": "2024-12-11",
  "iban": "CZ8545635664567300043347"
}
```

Created using the library [fio-api-php](https://github.com/mhujer/fio-api-php)

MultiFlexi
----------

FioBank statement tools is ready for run as [MultiFlexi](https://multiflexi.eu) application.
See the full list of ready-to-run applications within the MultiFlexi platform on the [application list page](https://www.multiflexi.eu/apps.php).

[![MultiFlexi App](https://github.com/VitexSoftware/MultiFlexi/blob/main/doc/multiflexi-app.svg)](https://www.multiflexi.eu/apps.php)

Debian/Ubuntu
-------------

For Linux, .deb packages are available. Please use the repo:

```shell
    echo "deb http://repo.vitexsoftware.com $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/vitexsoftware.list
    sudo wget -O /etc/apt/trusted.gpg.d/vitexsoftware.gpg http://repo.vitexsoftware.cz/keyring.gpg
    sudo apt update
    sudo apt install fiobank-statement-tools
```

After installing the package, the following new commands are available in the system:

* **fiobank-statement-downloader**      - downloads statements from FioBank
* **fiobank-statement-mailer**          - downloads statements from FioBank and sends them by email
* **fiobank-transaction-report**        - exports transactions overview as json
