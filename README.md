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

## Deployment

### GitHub Deployment
1. Webhook.php dosyasını sunucuya yükleyin
2. GitHub repository'nızda webhook oluşturun:
   - Event: Push events
   - Content type: application/json
   - Secret: (opsiyonel)

### Sunucu Konfigürasyonu
1. .htaccess dosyasını sunucuya yükleyin
2. PHP ve Apache/LiteSpeed konfigürasyonlarını kontrol edin
3. Sunucu izinlerini kontrol edin

## API Endpoints

- GET /api/todos - Tüm todo listesini getir
- GET /api/todos/{id} - Belirli bir todoyu getir
- POST /api/todos - Yeni todo ekle
- PUT /api/todos/{id} - Todo güncelle
- DELETE /api/todos/{id} - Todo sil
- GET /api/todos/search - Todo ara

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylı bilgi için LICENSE dosyasını inceleyin.
