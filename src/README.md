# Backend Documentation

Bu klasör, Türk Ticaret Case Study projesinin PHP backend uygulamasını içerir.

## Teknolojiler

- PHP
- Composer
- Laravel
- MySQL/MariaDB

## Kurulum

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

## API Endpoints

- GET /api/todos - Tüm todo listesini getir
- GET /api/todos/{id} - Belirli bir todoyu getir
- POST /api/todos - Yeni todo ekle
- PUT /api/todos/{id} - Todo güncelle
- DELETE /api/todos/{id} - Todo sil
- GET /api/todos/search - Todo ara

## Veritabanı Şeması

### todos tablosu
- id (int, primary key)
- title (string)
- description (text)
- status (string)
- priority (string)
- category_id (int)
- created_at (timestamp)
- updated_at (timestamp)

### categories tablosu
- id (int, primary key)
- name (string)
- description (text)
- created_at (timestamp)
- updated_at (timestamp)

## Güvenlik

- API istekleri için JWT authentication
- Rate limiting
- Input validation
- SQL injection koruması

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylı bilgi için ana dizindeki LICENSE dosyasını inceleyin.
