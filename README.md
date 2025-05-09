# Türk Ticaret Case Study

Bu proje, Türk Ticaret için geliştirilmiş bir todo uygulamasıdır. Frontend React.js ile, backend PHP ile geliştirilmiştir.

## Proje Yapısı

```
turkticaret_case_2/
├── frontend/          # React.js frontend uygulaması
├── public/            # Build edilmiş dosyalar
├── src/              # PHP backend kaynak kodları
└── tests/            # Test dosyaları
```

## Kurulum

### Frontend Kurulumu

1. Frontend klasörüne gidin:

   ```bash
   cd frontend
   ```

2. Gerekli paketleri yükleyin:

   ```bash
   npm install
   ```

3. Geliştirme sunucusunu başlatın:

   ```bash
   npm start
   ```

### Backend Kurulumu

1. Composer ile gerekli paketleri yükleyin:

   ```bash
   composer install
   ```

2. .env dosyasını oluşturun ve gerekli ayarları yapın:

   ```
   DB_HOST=your_host
   DB_NAME=your_database
   DB_USER=your_username
   DB_PASS=your_password
   ```

3. Veritabanı tablolarını oluşturun:

   ```bash
   php artisan migrate
   ```

### Sunucu Konfigürasyonu

1. .htaccess dosyasını sunucuya yükleyin

2. PHP ve Apache/LiteSpeed konfigürasyonlarını kontrol edin

3. Sunucu izinlerini kontrol edin

## API Endpoints

### Todo API

- GET /api/todos - Tüm todo listesini getir
- GET /api/todos/{id} - Belirli bir todoyu getir
- POST /api/todos - Yeni todo ekle
- PUT /api/todos/{id} - Todo güncelle
- DELETE /api/todos/{id} - Todo sil
- GET /api/todos/search - Todo ara
- PATCH /api/todos/{id}/status - Todo durumunu güncelle
- PATCH /api/todos/{id}/priority - Todo önceliğini güncelle

### Category API

- GET /api/categories - Tüm kategorileri getir
- GET /api/categories/{id} - Belirli bir kategoriyi getir
- POST /api/categories - Yeni kategori ekle
- PUT /api/categories/{id} - Kategori güncelle
- DELETE /api/categories/{id} - Kategori sil
- GET /api/categories/{id}/todos - Belirli kategoriye ait todoları getir

### Authentication API

- POST /api/auth/login - Kullanıcı girişi
- POST /api/auth/register - Yeni kullanıcı kaydı
- POST /api/auth/logout - Kullanıcı çıkış
- GET /api/auth/me - Mevcut kullanıcı bilgisi

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylı bilgi için ana dizindeki LICENSE dosyasını inceleyin.