# ERD — Marketplace Properti (Phase 1, implemented)

## Tables

- **users**: id, name, email, role (`user` default; `admin|agent|developer|user`), password, email_verified_at, remember_token, timestamps
- **agent_profiles**: id, user_id FK→users cascade (unique), agency nullable, phone, wa, photo nullable, verified_at nullable, timestamps
- **properties**: id, user_id FK→users cascade, title, slug unique, type enum (`rumah|apartemen|tanah|ruko|villa`), listing_status enum (`jual|sewa`), price unsignedBigInteger, bedrooms/smallint nullable, bathrooms nullable, land_area float nullable (LT m²), building_area float nullable (LB m²), certificate nullable (SHM/HGB/AJB/Strata Title), province, city, district nullable, address text, lat/lng decimal(10,7) nullable, description longtext, featured bool default false, verified_at nullable, views unsigned default 0, published_at nullable, timestamps
  - index: slug (unique), (city, type, listing_status), price
- **property_images**: id, property_id FK→properties cascade, path, sort int default 0, timestamps — index (property_id, sort)
- **favorites**: id, user_id FK→users cascade, property_id FK→properties cascade, timestamps — unique (user_id, property_id)
- **leads**: id, property_id FK→properties cascade, agent_user_id nullable FK→users nullOnDelete, name, phone, message nullable, source default `web`, timestamps
- **projects**: id, developer_user_id nullable FK→users nullOnDelete, name, slug unique, city, price_from unsignedBigInteger, description nullable, timestamps
- **articles**: id, title, slug unique, body longtext, city nullable, type nullable (pSEO), timestamps

## Relations

- User `hasOne` AgentProfile ↔ AgentProfile `belongsTo` User
- User `hasMany` Property/Favorite; `hasMany` Lead via `agent_user_id` (`agentLeads`); `hasMany` Project via `developer_user_id` (`developerProjects`)
- Property `belongsTo` User; `hasMany` PropertyImage (`images`, ordered by `sort`), Favorite (`favorites`), Lead (`leads`)
- PropertyImage/Favorite `belongsTo` Property (+ Favorite `belongsTo` User)
- Lead `belongsTo` Property; `belongsTo` User as `agent` via `agent_user_id`
- Project `belongsTo` User as `developer` via `developer_user_id`

## Migrations

- `0001_01_01_000000_create_users_table` (bawaan, tidak diubah)
- `2026_11_14_170957_create_agent_profiles_table`
- `2026_11_14_170958_create_properties_table`
- `2026_11_14_170959_create_property_images_table`
- `2026_11_14_171000_create_favorites_table`
- `2026_11_14_171001_create_leads_table`
- `2026_11_14_171002_create_projects_table`
- `2026_11_14_171003_create_articles_table`
- `2026_11_14_171006_add_role_to_users`
- `php artisan migrate:fresh --force` hijau (MySQL `rumah123`).
