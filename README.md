# DocuSign Driver for the ElectronicSignature Library (not public yet)


This is using the https://github.com/spatie/package-skeleton-laravel foundation.

The goal is by using the included https://github.com/docusign/docusign-esign-php-client 

Have the main class `src/DocusignDriver.php` 100% working

There are notes above every function there to help

I will share a video shortly of DocuSeals code since the goal is 100% the same.


## Install

```bash
composer require alnutile/docusigndriver
```

You need to get a lot of info from their UI to setup the .env area, the .env should look like this after:

```env
ELECTRONIC_SIGNATURES_DRIVER=docusign
DOCUSIGN_USERNAME=your@email.com
DOCUSIGN_PASSWORD="foobar"
DOCUSIGN_USER_ID="ef860922-ecb2-...."
DOCUSIGN_ACCOUNT_ID="7ed99888-4cf3-...."
## DEV IK
DOCUSIGN_INTEGRATOR_KEY="95256525-7ca7-......"
# RSA?
DOCUSIGN_PRIVATE_KEY="uj03....."

DOCUSIGN_RSA_PRIVATE_KEY="-----BEGIN RSA PRIVATE KEY-----
All the rest here
-----END RSA PRIVATE KEY-----"

DOCUSIGN_RSA_PUBLIC_KEY="-----BEGIN PUBLIC KEY-----
All the rest here
-----END PUBLIC KEY-----"

```

## Upload the Template to Docusign

This video shows how I did it with DocuSeal and I am hoping you can find a way to do this in DocuSign

>NOTE: Download the video so it is not sideways 🤦

[https://nextcloud.sundancesolutions.io/index.php/s/EPGGqKM2xEefpoP](https://nextcloud.sundancesolutions.io/index.php/s/EPGGqKM2xEefpoP)
