<?php

header('Content-type: text/html; charset=utf8');
require 'sahibinden.class.php';

// ana kategoriler
print_r( Sahibinden::Kategori() );

// alt kategoriler
/*
Sahibinden::Kategori('emlak');
*/

// kategori içerikleri
/*
Sahibinden::Liste('emlak');
Sahibinden::Liste('emlak', 20); // 2. sayfa
*/

// Mağaza içerikleri
/*
Sahibinden::magListe('mağaza adi');
Sahibinden::magListe('mağaza adi', 20); // 2. sayfa
*/


// içerik detayı (henüz tamamlanmadı)
/*
Sahibinden::Detay('http://www.sahibinden.com/ilan/emlak-konut-satilik-dorlion-gayrimenkul-den-yildiztepe-de-sifir-bina-da-2-plus1-153319984/detay');
*/
