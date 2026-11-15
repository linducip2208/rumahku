# BACKUP_RESTORE

Backup: `php artisan backup:database --keep=7` → storage/app/backups/rumah123-*.sql.gz (mysqldump otomatis, prune). Restore: gunzip lalu `mysql -u USER -p rumah123 < file.sql`, lalu `php artisan scout:import "App\Models\Property"`. Media: backup folder storage/app/public terpisah (rsync). Uji restore tiap bulan.
