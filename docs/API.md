# API v1 — Kontrak untuk Developer Flutter

Basis: `routes/api.php` aktual (prefix `/api/v1`). Semua respons JSON
(`bootstrap/app.php` me-render JSON untuk `api/*`).

## Base URL & Header

- Lokal: `http://localhost:8000/api/v1` (sesuaikan `APP_URL` / port serve).
- Header wajib untuk endpoint auth: `Authorization: Bearer <token>`,
  `Accept: application/json`, `Content-Type: application/json`.

## Auth (Sanctum token)

| Method & Path              | Auth | Body                                   | Respons |
|----------------------------|------|----------------------------------------|---------|
| `POST /auth/register`      | —    | `name`, `email`, `password`, `password_confirmation` (min 8) | `201 { user: {id,name,email,role}, token }` |
| `POST /auth/login`         | —    | `email`, `password`                    | `200 { user: {...}, token }` |
| `GET /me`                  | Ya   | —                                      | `200 { id, name, email, role, agency, phone }` |
| `POST /auth/logout`        | Ya   | —                                      | `200 { message }` (token saat ini dihapus) |

Contoh register:

```json
// POST /api/v1/auth/register
{ "name": "Budi", "email": "budi@mail.com", "password": "password123", "password_confirmation": "password123" }

// 201
{ "user": { "id": 7, "name": "Budi", "email": "budi@mail.com", "role": "user" }, "token": "1|abcdef..." }
```

Token dibuat via `createToken('flutter', ['properties:read', 'leads:write'])`.
Simpan token di secure storage, kirim sebagai `Bearer` di tiap request auth.
Kredensial salah → `422 { message, errors: { email: [...] } }`.

## Katalog & Konten (publik)

### `GET /properties` — list + filter + paginasi

Query: `q`, `type` (`rumah|apartemen|tanah|ruko|villa`),
`status` (`jual|sewa`), `city` (LIKE), `min_price`, `max_price`,
`bedrooms` (>=), `sort` (`latest|price_asc|price_desc`, default `latest`).
Hanya yang `published_at` terisi. 15 item/halaman (Laravel paginator:
`data` + `meta` + `links`).

```json
// GET /api/v1/properties?type=rumah&status=jual&city=Depok
{
  "data": [
    {
      "id": 12, "slug": "rumah-dijual-di-beji-depok-123456",
      "title": "Rumah Dijual di Beji, Depok",
      "type": "rumah", "listing_status": "jual", "price": 1500000000,
      "city": "Depok", "district": "Beji",
      "bedrooms": 3, "bathrooms": 2, "land_area": 120, "building_area": 90,
      "thumbnail": "/storage/properties/abc.jpg", "featured": false,
      "agent": { "name": "Agen Demo", "agency": "R123 Depok", "photo": null }
    }
  ],
  "meta": { "current_page": 1, "last_page": 4, "per_page": 15, "total": 50 }
}
```

### `GET /properties/{slug}` — detail

Menambah `views` +1 tiap hit. Field seperti card plus `certificate`,
`province`, `address`, `lat`, `lng`, `description`, `views`,
`published_at` (ISO8601), `images: [{id,url,sort}]`,
`agent: {id,name,agency,phone,wa,photo,verified_at}`,
`related_ids: [int]` (maks 6, sekota + setipe).

### `GET /projects` / `GET /projects/{slug}`

Query list: `city` (LIKE), `q` (nama, LIKE). Resource:
`{ id, name, slug, city, price_from, description }`.

### `GET /articles` / `GET /articles/{slug}`

Query list: `city` (=), `type` (=), `q` (judul, LIKE). Resource:
`{ id, title, slug, body, city, type }`.

### `POST /leads` — kirim lead (publik)

```json
// POST /api/v1/leads
{ "property_id": 12, "name": "Budi", "phone": "08123456789", "message": "Masih tersedia?" }

// 201
{ "message": "Lead berhasil dikirim.", "id": 31 }
```

`message` opsional (maks 2000). `source` otomatis `"api"`.

## Butuh Token

### Favorit

| Method & Path         | Body              | Respons |
|-----------------------|-------------------|---------|
| `GET /favorites`      | —                 | paginasi `PropertyCardResource` (seperti list properties) |
| `POST /favorites/toggle` | `{ "property_id": 12 }` | `{ "favorited": true }` `201` (buat) / `{ "favorited": false }` `200` (hapus) |

### Order paket unggulan

| Method & Path      | Body                                    | Respons |
|--------------------|-----------------------------------------|---------|
| `GET /orders`      | —                                       | paginasi order milik user (`property: {id,title,slug}` eager) |
| `POST /orders`     | `{ "property_id": 12, "package_slug": "pro" }` | `201 { message, id, amount, status: "pending" }` |

`property_id` harus properti milik user sendiri, kalau tidak `403
{ message: "Properti bukan milik Anda." }`.
`package_slug` harus ada di tabel `packages` (lihat halaman web `/harga`:
Gratis/Basic/Plus/Pro). Pembayaran web: `GET /pay/{order}` (auth, browser)
→ instruksi transfer (driver `manual`) atau redirect gateway;
callback server-ke-server `POST /pay/callback/{driver}` (tanpa auth/CSRF).

## Kode Status & Format Error

| Status | Arti / kapan |
|--------|--------------|
| `200`  | OK (list, detail, toggle-off, callback) |
| `201`  | Created (register, lead, favorit baru, order) |
| `401`  | Tanpa/expired token: `{ "message": "Unauthenticated." }` |
| `403`  | Order properti orang lain |
| `404`  | Slug/id tidak ada |
| `422`  | Validasi gagal: `{ "message": "...", "errors": { "field": ["..."] } }` |

Contoh `422`:

```json
{ "message": "The email field is required. (and 1 more error)", "errors": { "email": ["The email field is required."] } }
```

## Rate Limit

Tidak ada `throttle` kustom di `routes/api.php`; berlaku default grup
middleware `api` Laravel. Kalau Flutter kena `429`, terapkan backoff +
tampilkan pesan coba-lagi (jangan retry agresif; pola yang sama dipakai
callback payment agar anti retry-storm).

## Rute Web pSEO (sekilas, bukan API)

Aplikasi web memakai URL cantik untuk SEO: `/{listing}/{type}/{city}`
(`listing`: `jual|sewa`) via `SeoController@landing`, plus
`/properti`, `/properti/{slug}`, `/proyek`, `/agen`, `/artikel`,
`/harga`, `/bandingkan`, `/favorit`, `/lelang`, `/jasa`, `/sitemap.xml`.
Flutter cukup konsumsi API v1 di atas; deep-link bisa memetakan
`properties/{slug}` → layar detail.
