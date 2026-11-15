<?php

namespace App\Services\Payments;

use RuntimeException;

/**
 * Dilempar driver gateway bila kredensial belum diisi via admin settings.
 *
 * Pesan exception selalu menyebut setting key yang harus diisi,
 * agar admin tahu persis apa yang kurang — tanpa hardcode/dummy key.
 */
class MissingCredentials extends RuntimeException {}
