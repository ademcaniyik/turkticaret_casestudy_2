# Frontend Documentation

Bu klasör, Türk Ticaret Case Study projesinin React.js frontend uygulamasını içerir.

## Teknolojiler

- React.js (v18.2.0)
- Redux Toolkit
- Material-UI (MUI)
- React Router DOM
- Formik ve Yup (Form yönetimi)
- Axios (API iletişim)

## Kurulum

1. Gerekli paketleri yükleyin:
   ```bash
   npm install
   ```

2. Geliştirme sunucusunu başlatın:
   ```bash
   npm start
   ```

3. Production build oluşturun:
   ```bash
   npm run build
   ```

## Projeyi Çalıştırma

1. Geliştirme ortamında:
   ```bash
   npm start
   ```
   - Uygulama http://localhost:3000 adresinde çalışacaktır

2. Production ortamında:
   ```bash
   npm run build
   ```
   - Build edilmiş dosyalar `build` klasörüne kaydedilecektir

## Özellikler

- Todo listesi yönetimi
- Kategori yönetimi
- Arama ve filtreleme
- Durum yönetimi
- Öncelik yönetimi
- Tema değiştirme (Light/Dark mode)

## API Entegrasyonu

Uygulama, backend API ile entegre çalışır. API endpoint'leri:

- GET /api/todos - Tüm todo listesini getir
- GET /api/todos/{id} - Belirli bir todoyu getir
- POST /api/todos - Yeni todo ekle
- PUT /api/todos/{id} - Todo güncelle
- DELETE /api/todos/{id} - Todo sil
- GET /api/todos/search - Todo ara

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylı bilgi için ana dizindeki LICENSE dosyasını inceleyin.
