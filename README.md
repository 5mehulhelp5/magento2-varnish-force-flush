# Nx6_VarnishPurge

[![Magento 2](https://img.shields.io/badge/Magento-2.4-f46f25.svg?style=flat-square)](https://magento.com/)
[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777bb4.svg?style=flat-square)](https://www.php.net/)
[![Rector](https://img.shields.io/badge/Rector-enabled-8a2be2.svg?style=flat-square)](https://github.com/rectorphp/rector)
[![PHP-CS-Fixer](https://img.shields.io/badge/code%20style-PHP--CS--Fixer-46a2f1.svg?style=flat-square)](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](./LICENSE)

Magento 2 module that adds a manual **"force flush"** action for the Varnish full
page cache to the admin panel — for the times when Magento's own cache
invalidation isn't enough and you need to blow away the entire Varnish cache
right now.

| | |
|---|---|
| Package | `nx6/varnish-force-flush` |
| Module | `Nx6_VarnishPurge` |
| Version | `0.1.0` |

## Installation

```bash
composer require nx6/varnish-force-flush
bin/magento module:enable Nx6_VarnishPurge
bin/magento setup:upgrade
bin/magento setup:di:compile        # production mode only
bin/magento cache:flush
```

Configure the Varnish backend host/port under **Stores › Configuration ›
Advanced › System › Full Page Cache** (the standard Magento Varnish settings)
before using either button.

## Where the buttons appear

- **Stores › Configuration › Advanced › System › Full Page Cache** — a
  **Force Purge** button next to the Varnish settings. Runs over AJAX and shows a
  JSON `{success, message}` result inline.
- **System › Tools › Cache Management** — a **Force Varnish Flush** button right
  after *Flush JavaScript/CSS Cache*. It only shows when Varnish is the configured
  caching application (`system/full_page_cache/caching_application` = Varnish) and
  the admin user holds the ACL. Behaves like Magento's own flush buttons: performs
  the purge, sets a success/error notice, redirects back.

## How it works

Both buttons call the same operation: an HTTP `PURGE` request to the configured
Varnish backend (`system/full_page_cache/varnish/backend_host` /
`backend_port`) carrying `X-Magento-Tags-Pattern: .*`, which tells Varnish to
discard its **entire** cache. The request and response are logged for
troubleshooting.

| File | Role |
|---|---|
| `Model/VarnishPurger.php` | The purge itself (the cURL `PURGE` request); shared by both entry points. |
| `Model/VarnishPurgeResult.php` | Immutable `{success, message}` value object. |
| `Controller/Adminhtml/Varnish/Purge.php` | Config-page button — AJAX, returns JSON. |
| `Controller/Adminhtml/Cache/Purge.php` | Cache Management button — GET action, admin notice + redirect. |
| `Block/Adminhtml/Cache/Additional.php` | Extends core `Magento\Backend\Block\Cache\Additional` to add the Varnish visibility / ACL checks. No core files are modified — the template is swapped via layout XML. |
| `etc/acl.xml` | `Nx6_VarnishPurge::varnish_purge` gates both buttons. |

## Compatibility

Magento 2.4.x, PHP 8.3+. Requires Varnish configured as the full page cache
application.

## Structure

```
.
├── Block/Adminhtml/
│   ├── Cache/Additional.php
│   └── System/Config/PurgeButton.php
├── Controller/Adminhtml/
│   ├── Cache/Purge.php
│   └── Varnish/Purge.php
├── Model/
│   ├── VarnishPurger.php
│   └── VarnishPurgeResult.php
├── etc/
│   ├── acl.xml
│   ├── adminhtml/{routes.xml,system.xml}
│   └── module.xml
├── view/adminhtml/
│   ├── layout/adminhtml_cache_index.xml
│   └── templates/system/{cache/additional.phtml,config/purge_button.phtml}
├── registration.php
├── composer.json
├── LICENSE
└── README.md
```

## License

MIT — see [LICENSE](./LICENSE).
