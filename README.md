# koruPHP Framework

Minimalist, yüksek performanslı ve modüler PHP framework. SCADA sistemleri ve endüstriyel uygulamalar için özel olarak tasarlanmıştır.

## 🚀 Özellikler

- **MVC Mimarisi**: Temiz ve ayrıştırılmış kod yapısı
- **Modüler Yapı**: Çoklu proje desteği
- **Özel Template Engine**: Blade benzeri, güvenli şablon motoru
- **Dinamik Routing**: Esnek rota yönetimi
- **Query Builder**: Basit ve güçlü veritabanı işlemleri
- **SCADA Bileşenleri**: Endüstriyel kontrol panelleri için hazır bileşenler
- **Middleware Desteği**: İstek/yanıt döngüsü kontrolü

## 📦 Kurulum

```bash
# Composer ile projeyi oluşturun
composer create-project koru/koru-php myproject

# Proje dizinine gidin
cd myproject

# Bağımlılıkları yükleyin
composer install

# Ortam dosyasını kopyalayın
cp .env.example .env

# Yerel sunucuyu başlatın
php -S localhost:8000 -t public