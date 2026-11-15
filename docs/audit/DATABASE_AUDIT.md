# DATABASE_AUDIT

24 migrasi, semua Ran. FK cascade konsisten (properties→images/favorites/leads/orders; auctions→bids). Index: listings (city,type,status)+price, images(property,sort), favorites unique pair, orders(user,status)/(property,status), bids(auction,created), services(category,city), hot tables batch 2026_11_15_174221. payment_reference unique nullable. Tanpa SoftDeletes (hard-delete + hapus file, disengaja). Tanpa index: package_slug (join logis), kpr nominal. InnoDB TABLE_ROWS estimasi — gunakan COUNT(*). Testing DB terpisah rumah123_testing; .env.testing mencegah wipe dev.
