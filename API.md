# TürkTicaret Todo API Dokümantasyonu

## Base URL

```plaintext
https://acdisoftware.com.tr/turkticaret
```

## Kategoriler (Categories)

### Tüm Kategorileri Listele

```http
GET /api/categories
```

**Yanıt:**

```json
{
    "data": [
        {
            "id": 1,
            "name": "İş",
            "description": "İş ile ilgili görevler",
            "color": "#FF5733",
            "todo_count": 5
        }
    ]
}
```

### Kategori Detayı

```http
GET /api/categories/{id}
```

**Yanıt:**

```json
{
    "data": {
        "id": 1,
        "name": "İş",
        "description": "İş ile ilgili görevler",
        "color": "#FF5733",
        "todos": [...]
    }
}
```

### Yeni Kategori Oluştur

```http
POST /api/categories
```

**Gövde:**

```json
{
    "name": "İş",
    "description": "İş ile ilgili görevler",
    "color": "#FF5733"
}
```

**Doğrulama Kuralları:**

- name: En az 3 karakter
- description: En az 10 karakter
- color: HEX renk kodu (#RRGGBB)

### Kategori Güncelle

```http
PUT /api/categories/{id}
```

**Gövde:**

```json
{
    "name": "İş",
    "description": "İş ile ilgili görevler",
    "color": "#FF5733"
}
```

### Kategori Sil

```http
DELETE /api/categories/{id}
```

## Görevler (Todos)

### Görevleri Listele

```http
GET /api/todos
```

**Query Parametreleri:**

- page: Sayfa numarası (varsayılan: 1)
- limit: Sayfa başına kayıt (varsayılan: 10, maksimum: 50)
- sort: Sıralama alanı (created_at, due_date, priority, status)
- order: Sıralama yönü (ASC, DESC)
- status: Durum filtresi (pending, in_progress, completed, cancelled)
- priority: Öncelik filtresi (low, medium, high)

### Görev Detayı

```http
GET /api/todos/{id}
```

### Yeni Görev Oluştur

```http
POST /api/todos
```

**Gövde:**

```json
{
    "title": "Rapor hazırla",
    "description": "Haftalık raporu hazırla ve yöneticiye gönder",
    "status": "pending",
    "priority": "high",
    "due_date": "2025-05-10",
    "category_ids": [1, 2]
}
```

**Doğrulama Kuralları:**

- title: En az 3 karakter
- description: En az 10 karakter
- status: pending, in_progress, completed, cancelled
- priority: low, medium, high
- due_date: Geçerli tarih formatı
- category_ids: Kategori ID'lerinin dizisi

### Görev Güncelle

```http
PUT /api/todos/{id}
```

**Gövde:** (Yeni görev oluşturma ile aynı format)

### Görev Sil

```http
DELETE /api/todos/{id}
```

### Görev Durumu Güncelle

```http
PATCH /api/todos/{id}/status
```

**Gövde:**

```json
{
    "status": "completed"
}
```

### Görev Ara

```http
GET /api/todos/search?q={arama_terimi}
```

### İstatistikler

```http
GET /api/todos/stats
```

**Yanıt:**

```json
{
    "data": {
        "total": 100,
        "by_status": {
            "pending": 30,
            "in_progress": 20,
            "completed": 45,
            "cancelled": 5
        },
        "by_priority": {
            "low": 20,
            "medium": 50,
            "high": 30
        }
    }
}
```

## Hata Kodları

- 200: Başarılı
- 201: Başarıyla oluşturuldu
- 404: Bulunamadı
- 422: Doğrulama hatası
- 500: Sunucu hatası

## Yanıt Formatı

Tüm API yanıtları aşağıdaki formatta döner:

```json
{
    "data": null,
    "status": "success|error",
    "message": "İşlem başarılı",
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 100,
        "total_pages": 10
    }
}
```
