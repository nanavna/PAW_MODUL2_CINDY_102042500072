```php
<?php

// Koneksi database MySQL dengan port 3307
$conn = mysqli_connect(
    '127.0.0.1',
    'root',
    '',
    'paw_modul2_brownies',
    3307
);

// Periksa koneksi database
if (!$conn) {
    die(
        'Koneksi database gagal: ' .
        mysqli_connect_error()
    );
}

// Mengatur karakter database
mysqli_set_charset($conn, 'utf8mb4');

?>
```