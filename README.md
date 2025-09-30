Fiobank Statement Tools
=======================

A comprehensive suite of PHP tools for working with FioBank statements and transactions. This toolset provides automated downloading, emailing, and reporting capabilities for FioBank account data.

[![wakatime](https://wakatime.com/badge/user/5abba9ca-813e-43ac-9b5f-b1cfdf3dc1c7/project/636c0922-84fd-4eec-aaa1-356b712caae3.svg)](https://wakatime.com/badge/user/5abba9ca-813e-43ac-9b5f-b1cfdf3dc1c7/project/636c0922-84fd-4eec-aaa1-356b712caae3)

## Tools Overview

This package includes three main tools:

1. **fiobank-statement-downloader** - Downloads bank statements in various formats
2. **fiobank-statement-mailer** - Downloads and emails statements to recipients
3. **fiobank-transaction-report** - Generates JSON transaction reports

## Fiobank Statement Downloader


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

### Configuration

All tools use environment variables for configuration. You can either set them in your environment or create a `.env` file.

**Required Configuration:**
* `FIO_TOKEN` - Your FioBank API token with read permissions (e.g., 'KitMuWyajissajPishtuwolth8ojyukMaldryavAcsOotuhuaksaf')
* `FIO_TOKEN_NAME` - Descriptive name for your token (e.g., 'Fio Main')
* `ACCOUNT_NUMBER` - Your FioBank account number (e.g., 666666666)

**Optional Configuration:**
* `STATEMENTS_DIR` - Directory to save statements (default: current directory)
* `STATEMENTS_FORMAT` - Statement format: pdf, csv, gpc, html, json, ofx, xml (default: pdf)
* `APP_DEBUG` - Enable debug mode (True/False)
* `EASE_LOGGER` - Logging method: syslog, eventlog, or console
* `LANG` - Application locale: cs_CZ or en_US (default: cs_CZ)

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

## Fiobank Statement Mailer

![Mailer](fiobank-statement-mailer.svg?raw=true)

Downloads FioBank statements and sends them via email to specified recipients.

### Usage

```shell
fiobank-statement-mailer [path/to/.env]
```

### Configuration

Shares basic configuration with the downloader and uses additional email-specific environment variables:

**Email Configuration:**
* `STATEMENTS_TO` - Recipient's email address (required)
* `STATEMENTS_FROM` - Sender's email address
* `STATEMENTS_REPLYTO` - Reply-To email address
* `STATEMENTS_CC` - CC email address
* `STATEMENTS_DIR` - Temporary folder for downloaded statements (default: /tmp/)
* `EASE_SMTP` - Optional SMTP configuration as JSON string:
  ```json
  {
    "port": "587",
    "starttls": true,
    "auth": true,
    "host": "smtp.office365.com",
    "username": "your@email.com",
    "password": "your_password"
  }
  ```

## Fiobank Transaction Report

![Report](fiobank-transaction-report.svg?raw=true)

Generates a comprehensive JSON report of FioBank transactions, providing detailed insights into account activity.

### Usage

```shell
fiobank-transaction-report [path/to/.env]
```

### Output Example

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

## Features

- **Multiple Statement Formats**: Support for PDF, CSV, GPC, HTML, JSON, OFX, and XML formats
- **Automated Email Delivery**: Send statements directly to recipients via email
- **Transaction Analysis**: Generate detailed JSON reports of account activity
- **Flexible Scheduling**: Import data for specific date ranges or predefined periods
- **MultiFlexi Integration**: Ready for deployment in MultiFlexi application platform
- **Comprehensive Logging**: Built-in logging with multiple output options
- **Environment-based Configuration**: Easy setup using environment variables or .env files

## Requirements

- PHP 7.4 or higher
- FioBank API token with read permissions
- Valid FioBank account

## Library Dependencies

Created using the library [fio-api-php](https://github.com/mhujer/fio-api-php)

## MultiFlexi Integration

These FioBank statement tools are fully compatible with [MultiFlexi](https://multiflexi.eu) - a powerful application management platform. MultiFlexi allows you to:

- **Easy Deployment**: Install and configure applications through a web interface
- **Environment Management**: Manage configuration variables securely
- **Scheduled Execution**: Set up automated statement downloads and reports
- **Multi-Environment Support**: Run applications in different environments
- **Container Support**: Deploy using Docker/OCI images

All three tools (`fiobank-statement-downloader`, `fiobank-statement-mailer`, `fiobank-transaction-report`) are available as ready-to-run MultiFlexi applications.

[![MultiFlexi App](https://github.com/VitexSoftware/MultiFlexi/blob/main/doc/multiflexi-app.svg)](https://www.multiflexi.eu/apps.php)

See the full list of ready-to-run applications within the MultiFlexi platform on the [application list page](https://www.multiflexi.eu/apps.php).

## Installation

### Debian/Ubuntu

For Linux distributions based on Debian/Ubuntu, .deb packages are available through the VitexSoftware repository:

```shell
# Add the repository
echo "deb http://repo.vitexsoftware.com $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/vitexsoftware.list

# Add the GPG key
sudo wget -O /etc/apt/trusted.gpg.d/vitexsoftware.gpg http://repo.vitexsoftware.cz/keyring.gpg

# Update package list and install
sudo apt update
sudo apt install fiobank-statement-tools
```

### Composer Installation

For other systems or development purposes, you can install via Composer:

```shell
composer require spojenet/fiobank-statement-tools
```

### Docker/Container Usage

Docker images are available:

```shell
# Download statements
docker run spojenet/fiobank-statement-downloader

# Send statements via email
docker run spojenet/fiobank-statement-mailer

# Generate transaction reports
docker run spojenet/fiobank-transaction-report
```

### Available Commands

After installation, the following commands are available:

* **fiobank-statement-downloader** - Downloads statements from FioBank in various formats
* **fiobank-statement-mailer** - Downloads statements from FioBank and sends them by email
* **fiobank-transaction-report** - Exports transactions overview as JSON

## Troubleshooting

### Common Issues

1. **Invalid API Token**: Ensure your FIO_TOKEN has read permissions for the specified account
2. **Account Access**: Verify that ACCOUNT_NUMBER matches your FioBank account
3. **Date Range Issues**: Check that your requested date range is valid and within API limits
4. **Email Delivery**: For the mailer tool, ensure SMTP settings are correctly configured

### Getting Help

- **Documentation**: Full documentation is available in the repository
- **Issues**: Report bugs and request features at [GitHub Issues](https://github.com/Spoje-NET/fiobank-statement-tools/issues)
- **Support**: Contact info@vitexsoftware.cz for commercial support
