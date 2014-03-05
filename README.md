Hakkında
====================

Sahibinden.com için php ile bot hazırladım. Dileyenler alıp kendileri de geliştirmeye devam edebilirler.

Şuan güzel bir şekilde, kategorileri alt kategorileri vs. kategori listelerini ve detaylarını çekmektedir.

Kullanımı da oldukça basit, aşağıdan bakabilirsiniz.

Kullanımı
====================

```php
<?php

header('Content-type: text/html; charset=utf8');
require 'sahibinden.class.php';

// ana kategoriler
print_r( Sahibinden::Kategori() );

// alt kategoriler
print_r( Sahibinden::Kategori('emlak') );

// kategori içerikleri
print_r( Sahibinden::Liste('emlak') );
// Sahibinden::Liste('emlak', 20); // 2. sayfa

// içerik detayı
print_r( Sahibinden::Detay('http://www.sahibinden.com/ilan/emlak-konut-satilik-dorlion-gayrimenkul-den-yildiztepe-de-sifir-bina-da-2-plus1-153319984/detay') );
```
