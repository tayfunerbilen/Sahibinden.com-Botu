Hakkında
====================

Sahibinden.com için php ile bot hazırladım. Henüz tamamlanmadı, dileyenler alıp kendileri de geliştirmeye devam edebilirler.

Proxy sorununu çözemedim henüz, pek işe yaradığını söyleyemem mevcut proxy'nin :)

Kullanımı
====================

```
// ana kategoriler
Sahibinden::Kategori();

// alt kategoriler
Sahibinden::Kategori('emlak');

// kategori içerikleri
Sahibinden::Liste('emlak');
Sahibinden::Liste('emlak', 20); // 2. sayfa

// içerik detayı (henüz tamamlanmadı)
Sahibinden::Detay('http://www.sahibinden.com/ilan/emlak-konut-satilik-dorlion-gayrimenkul-den-yildiztepe-de-sifir-bina-da-2-plus1-153319984/detay');
```
