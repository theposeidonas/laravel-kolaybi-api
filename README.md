<a name="readme-top"></a>
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]

<br />
<div align="center">
  <a href="https://webremium.com">
    <img src="https://istanbulwebtasarim.pro/images/istanbul-web-tasarim-logo.webp" alt="Webremium" style="width: 40%">
  </a>

<h3 align="center">KolayBi API Laravel Package</h3>

[![Laravel][Laravel.com]][Laravel-url]
![Packagist Downloads][downloads-url]
![Tests](https://img.shields.io/github/actions/workflow/status/theposeidonas/laravel-kolaybi/phpunit.yml?style=for-the-badge&logo=github)

  <p align="center">
    Laravel için geliştirilmiş güncel KolayBi API paketi.
    <br />
    <a href="https://github.com/theposeidonas/laravel-kolaybi-api"><strong>Dökümantasyon »</strong></a>
    <br />
    <br />
    <a href="https://github.com/theposeidonas/laravel-kolaybi-api/issues">Hata Bildir</a>
    ·
    <a href="https://github.com/theposeidonas/laravel-kolaybi-api/issues">Özellik İste</a>
  </p>
</div>

---

# Laravel KolayBi API

Bu paket, KolayBi API servisleri ile Laravel projeniz arasında hızlı ve güvenli bir köprü kurar. Otomatik bearer alarak arka planda otomatik yöneterek geliştirme sürecinizi hızlandırır.

### Neden ihtiyaç var?

KolayBi API entegrasyonu yaparken tekrar eden kimlik doğrulama işlemleri, dinamik token yönetimi ve karmaşık JSON yapılarıyla uğraşmak yerine; Laravel standartlarına uygun Facade yapısını kullanan, her Resource için hazır validasyonları olan sade bir çözüme ihtiyaç duyulmaktadır.

<p align="right">(<a href="#readme-top">Başa dön</a>)</p>

## Başlarken

Paketi kullanabilmek için KolayBi üzerinden API Key ve Channel ID bilgilerine sahip olmanız gerekmektedir.

### Kurulum

```shell
composer require theposeidonas/laravel-kolaybi-api
```

Config dosyasını yayınlamak için:

```shell
php artisan vendor:publish --tag=kolaybi-config --force
```

### Konfigürasyon

`.env` dosyanıza aşağıdaki bilgileri ekleyin:

```dotenv
KOLAYBI_API_KEY="your-api-key"
KOLAYBI_CHANNEL_ID="your-channel-id"
KOLAYBI_SANDBOX=true
KOLAYBI_BASE_URL="https://ofis-sandbox-api.kolaybi.com"
```

<p align="right">(<a href="#readme-top">Başa dön</a>)</p>

## Kullanım

Kullanacağınız Controller içerisine paketi dahil edin:

```php
use Theposeidonas\Kolaybi\Facades\Kolaybi;
```

#### Sınıflar (Resources)

Tüm modüllere Facade üzerinden erişebilirsiniz:

```php
Kolaybi::bank(); // Kasa ve Banka Hesapları
Kolaybi::company(); // Şirket Yönetimi
Kolaybi::customer(); // Cari (Müşteri ve Tedarikçi) Yönetimi
Kolaybi::invoice(); // Satış ve Alış Faturası İşlemleri
Kolaybi::product(); // Ürün, Hizmet ve Stok Yönetimi
Kolaybi::order(); // Sipariş Yönetimi
Kolaybi::proforma(); // Proforma Fatura İşlemleri
Kolaybi::tag(); // Etiket Yönetimi
Kolaybi::user(); // Kullanıcı ve Yetki Yönetimi
```

<p align="right">(<a href="#readme-top">Başa dön</a>)</p>


##### Yanıt Yapısı

Tüm istekler `KolaybiResponse` sınıfı döner. Başarı kontrolü ve veriye erişim şu şekildedir:

```php
$response = Kolaybi::bank()->list();

if ($response->isSuccess()) {
$data = $response->getData();
$status = $response->getStatus();
}
```

<p align="right">(<a href="#readme-top">Başa dön</a>)</p>

## Tüm Fonksiyonlar ve Parametreleri

Aşağıda paket içerisindeki kullanabileceğiniz tüm fonksiyonlar bulunuyor. Parametre detayları için döküman linklerini inceleyebilirsiniz. Eğer tek bir parametre bekleniyorsa, direk bu parametreyi, eğer birden fazla parametre bekleniyorsa da array şeklinde göndermelisiniz.

#### Kasa ve Banka (BankResource)
```php 
Kolaybi::bank()->list($query); // Kasa ve banka hesaplarını listeleme -> https://developer.kolaybi.com/docs/vaults/list/ 
Kolaybi::bank()->find($vaultId); // Belirli bir kasa/banka detayını görüntüleme -> https://developer.kolaybi.com/docs/vaults/detail 
Kolaybi::bank()->transactions($vaultId); // Kasa/banka hesap hareketlerini listeleme -> https://developer.kolaybi.com/docs/vaults/transactions] 
```

#### Şirket (CompanyResource)
```php 
Kolaybi::company()->list(); // Yetkili olunan şirketleri listeleme -> https://developer.kolaybi.com/docs/companies/list/
```

### Cari Hesap (CustomerResource)
```php 
Kolaybi::customer()->list(); // Cari hesapları (müşteri ve tedarikçi) listeleme -> https://developer.kolaybi.com/docs/associates/list/ 
Kolaybi::customer()->create($data); // Yeni cari hesap (müşteri/tedarikçi) oluşturma -> https://developer.kolaybi.com/docs/associates/create/ 
Kolaybi::customer()->addressCreate($data); // Mevcut bir cari hesaba yeni adres ekleme -> https://developer.kolaybi.com/docs/associates/addresses/ 
Kolaybi::customer()->transactions($associateId); // Cari hesaba ait tüm hareketleri listeleme -> https://developer.kolaybi.com/docs/associates/transactions/ 
Kolaybi::customer()->payment($id, $data); // Cari hesaptan tahsilat yapma (Ödeme Girişi) -> https://developer.kolaybi.com/docs/associates/payment/ 
Kolaybi::customer()->proceed($id, $data); // Cari hesaba ödeme yapma (Ödeme Çıkışı) -> https://developer.kolaybi.com/docs/associates/proceed/ 
```

### Fatura (InvoiceResource)
```php 
Kolaybi::invoice()->list($query); // Satış ve alış faturalarını listeleme -> https://developer.kolaybi.com/docs/invoices/list/ 
Kolaybi::invoice()->create($data); // Yeni fatura (satış/alış) oluşturma -> https://developer.kolaybi.com/docs/invoices/create/ 
Kolaybi::invoice()->find($documentId); // Belirli bir faturanın detaylarını görüntüleme -> https://developer.kolaybi.com/docs/invoices/detail/ 
Kolaybi::invoice()->formalize($documentId); // E-Fatura Gönderimi (Taslak Faturayı Resmileştirme) -> https://developer.kolaybi.com/docs/invoices/e-document/ 
Kolaybi::invoice()->collect($data); // Fatura Tahsilat -> https://developer.kolaybi.com/docs/invoices/proceed/ 
Kolaybi::invoice()->delete($documentId); // Mevcut bir faturayı silme -> https://developer.kolaybi.com/docs/invoices/delete/ 
Kolaybi::invoice()->deleteCollection($documentId); // Fatura üzerindeki tahsilat/ödeme kaydını silme -> https://developer.kolaybi.com/docs/invoices/proceed-delete/ 
Kolaybi::invoice()->cancelEDocument($documentId); // Resmileşmiş bir e-belgeyi iptal etme -> https://developer.kolaybi.com/docs/invoices/e-document-cancel/ 
Kolaybi::invoice()->viewEDocument($uuid); // E-Belgenin görüntüsünü (HTML/PDF) alma -> https://developer.kolaybi.com/docs/invoices/e-document-view/ 
Kolaybi::invoice()->resendEDocument($documentId); // E-Belgeyi alıcıya tekrar e-posta ile gönderme -> https://developer.kolaybi.com/docs/invoices/resend/ 
Kolaybi::invoice()->listEInvoices($query); // Gelen kutusuna düşen e-faturaları listeleme -> https://developer.kolaybi.com/docs/e-documents/list 
```

### Sipariş (OrderResource)
```php 
Kolaybi::order()->create($data); // Yeni sipariş oluşturma -> https://developer.kolaybi.com/docs/orders/create/ 
Kolaybi::order()->find($documentId); // Belirli bir siparişin detaylarını görüntüleme -> https://developer.kolaybi.com/docs/orders/detail/ 
```

### Ürün ve Stok (ProductResource)
```php 
Kolaybi::product()->list($query); // Ürün ve hizmetleri listeleme -> https://developer.kolaybi.com/docs/products/list/ 
Kolaybi::product()->create($data); // Yeni ürün veya hizmet oluşturma -> https://developer.kolaybi.com/docs/products/create/ 
Kolaybi::product()->find($productId); // Belirli bir ürünün detaylarını görüntüleme -> https://developer.kolaybi.com/docs/products/detail/ 
Kolaybi::product()->update($productId, $data); // Mevcut ürün bilgilerini güncelleme -> https://developer.kolaybi.com/docs/products/update/ 
Kolaybi::product()->stock($data); // Ürün için stok girişi veya çıkışı yapma (Stok Hareketleri) -> https://developer.kolaybi.com/docs/products/stock/ 
```

### Proforma Fatura (ProformaResource)
```php 
Kolaybi::proforma()->create($data); // Yeni proforma fatura oluşturma -> https://developer.kolaybi.com/docs/proformas/create/ 
Kolaybi::proforma()->find($documentId); // Belirli bir proforma faturanın detaylarını görüntüleme -> https://developer.kolaybi.com/docs/proformas/detail/ 
```

### Etiket (TagResource)
```php 
Kolaybi::tag()->list($query); // Etiketleri listeleme -> https://developer.kolaybi.com/docs/tags/list/ 
Kolaybi::tag()->find($tagId); // Belirli bir etiketin detaylarını görüntüleme -> https://developer.kolaybi.com/docs/tags/detail 
```

### Kullanıcı (UserResource)
```php 
Kolaybi::user()->list(); // Şirketteki kullanıcıları listeleme -> https://developer.kolaybi.com/docs/users/list/ 
```

## TODO

- [x] Temel Resource yapıları tamamlandı.
- [x] Unit testler eklendi.

## Lisanslama

MIT Lisansı ile dağıtılmaktadır. Detaylar için `LICENSE` dosyasına bakabilirsiniz.

<p align="right">(<a href="#readme-top">Başa dön</a>)</p>

## İletişim

Baran Arda - [@theposeidonas](https://twitter.com/theposeidonas) - baran@webremium.com

Proje Linki: [https://github.com/theposeidonas/laravel-kolaybi-api](https://github.com/theposeidonas/laravel-kolaybi-api)

<p align="right">(<a href="#readme-top">Başa dön</a>)</p>

[contributors-shield]: https://img.shields.io/github/contributors/theposeidonas/laravel-kolaybi.svg?style=for-the-badge
[contributors-url]: https://github.com/theposeidonas/laravel-kolaybi-api/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/theposeidonas/laravel-kolaybi.svg?style=for-the-badge
[forks-url]: https://github.com/theposeidonas/laravel-kolaybi-api/network/members
[stars-shield]: https://img.shields.io/github/stars/theposeidonas/laravel-kolaybi.svg?style=for-the-badge
[stars-url]: https://github.com/theposeidonas/laravel-kolaybi-api/stargazers
[issues-shield]: https://img.shields.io/github/issues/theposeidonas/laravel-kolaybi.svg?style=for-the-badge
[issues-url]: https://github.com/theposeidonas/laravel-kolaybi-api/issues
[license-shield]: https://img.shields.io/github/license/theposeidonas/laravel-kolaybi.svg?style=for-the-badge
[license-url]: https://github.com/theposeidonas/laravel-kolaybi-api/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://www.linkedin.com/in/theposeidonas/
[Laravel.com]: https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
[Laravel-url]: https://laravel.com
[downloads-url]: https://img.shields.io/packagist/dt/theposeidonas/laravel-kolaybi?style=for-the-badge&color=007ec6&cacheSeconds=3600