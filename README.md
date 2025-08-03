# REST API: Организации, Здания и Деятельности

Приложение на Laravel с REST API для работы со справочниками организаций, зданий и видов деятельности.

---

## 🚀 Быстрый запуск

```bash
git clone https://github.com/your-username/your-repo.git
cd your-repo
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

> API доступно по адресу: `http://localhost:8000/api`

---

## 🔐 Аутентификация

Все запросы требуют заголовок:

```http
X-API-KEY: super-secret-key
```

Установи его в `.env` как `APP_API_KEY=super-secret-key`.

---

## 📚 Документация (Swagger)

Генерируется автоматически с помощью атрибутов PHP 8.

```bash
php artisan l5-swagger:generate
```

Открыть в браузере:

```
http://localhost:8000/api/documentation
```

---

## 🔗 Доступные маршруты

### 🏢 Здания

| Метод | Путь | Описание |
|-------|------|----------|
| `GET` | `/api/buildings` | Получить список всех зданий |
| `GET` | `/api/buildings/{id}/organizations` | Организации, находящиеся в здании |

### 🧭 Организации

| Метод | Путь | Описание |
|-------|------|----------|
| `GET` | `/api/organizations/{id}` | Подробная информация об организации |
| `GET` | `/api/organizations/search?name=` | Поиск по названию |
| `GET` | `/api/organizations/search?activity=` | Поиск по дереву деятельности |
| `GET` | `/api/organizations/nearby?lat=&lng=&radius=` | Поиск организаций рядом |
| `GET` | `/api/activities/{id}/organizations` | Организации по ID вида деятельности |
| `GET` | `/api/buildings/{id}/organizations` | Организации по ID здания |

### 📂 Виды деятельности

| Метод | Путь | Описание |
|-------|------|----------|
| `GET` | `/api/activities` | Все корневые виды деятельности |
| `GET` | `/api/activities/{id}` | Вложенные виды по ID |

---

## 🧪 Тестовые данные

- Генерируются автоматически через `php artisan migrate --seed`
- Используется SQLite по умолчанию (можно сменить в `.env`)
