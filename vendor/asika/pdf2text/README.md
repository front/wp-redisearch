# Simple PDF to text class

[![Latest Stable Version](https://poser.pugx.org/asika/pdf2text/v/stable.svg)](https://packagist.org/packages/asika/pdf2text) [![Total Downloads](https://poser.pugx.org/asika/pdf2text/downloads.svg)](https://packagist.org/packages/asika/pdf2text) [![Latest Unstable Version](https://poser.pugx.org/asika/pdf2text/v/unstable.svg)](https://packagist.org/packages/asika/pdf2text) [![License](https://poser.pugx.org/asika/pdf2text/license.svg)](https://packagist.org/packages/asika/pdf2text)

This is a simple PDF to text class based on [SilverStripe framework](https://code.google.com/p/lucene-silverstripe-plugin/source/browse/trunk/thirdparty/class.pdf2text.php?r=19)

This version support composer and PSR-4 autoloading. Origin code is maintained by [Darren Inwood](https://code.google.com/u/102046044091186134429).

# Installation Via Composer

``` json
{
    "require": {
        "asika/pdf2text": "1.*"
    }
}
```

# Usage

``` php
$reader = new \Asika\Pdf2text;
$output = $reader->decode($fileName);
```

# Lincense

GNU General Public License version 2 or later.

