```php
<?php
require 'koneksi.php';

$cari = trim($_GET['cari'] ?? '');
$kata = mysqli_real_escape_string($conn, $cari);

$sql = "
    SELECT *
    FROM brownies
    WHERE nama_brownies LIKE '%$kata%'
       OR merek_brownies LIKE '%$kata%'
       OR kategori_brownies LIKE '%$kata%'
    ORDER BY id DESC
";

$hasil = mysqli_query($conn, $sql);

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function rupiah($value) {
    return 'Rp ' . number_format((float)$value, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Brownie House — Katalog Brownies</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header class="topbar">

        <a class="brandmark" href="index.php">
            brownie<span> house.</span>
        </a>

        <nav class="navlinks">
            <a href="index.php">Katalog</a>
            <a href="tambah.php">Tambah Brownies</a>
        </nav>

    </header>

    <main class="wrap">

        <section class="hero">

            <p class="eyebrow">
                Freshly baked, always delightful
            </p>

            <h1>
                Temukan langkahmu,
                <br>
                mulai dari brownies yang tepat.
            </h1>

            <p>
                Kelola katalog brownies rumah tangga dalam satu tempat.
                Cari produk, cek stok, dan perbarui informasi dengan mudah.
            </p>

        </section>

        <?php if (isset($_GET['pesan'])): ?>

            <div class="notice">
                Perubahan data brownies berhasil disimpan.
            </div>

        <?php endif; ?>

        <div class="toolbar">

            <div>

                <p class="eyebrow">
                    Brownies collection
                </p>

                <h2>Katalog brownies</h2>

            </div>

            <form class="search" method="get">

                <input
                    type="search"
                    name="cari"
                    placeholder="Cari nama, merek, kategori..."
                    value="<?= h($cari) ?>"
                >

                <button class="btn" type="submit">
                    Cari
                </button>

                <?php if ($cari !== ''): ?>

                    <a
                        class="btn secondary"
                        href="index.php"
                    >
                        Reset
                    </a>

                <?php endif; ?>

            </form>

        </div>

        <p class="meta">
            Jumlah hasil:
            <?= mysqli_num_rows($hasil) ?>
            produk
        </p>

        <?php if (mysqli_num_rows($hasil) === 0): ?>

            <div class="empty">

                <h3>Brownies belum ditemukan</h3>

                <p>
                    Coba kata kunci lain atau tambahkan brownies baru.
                </p>

                <a class="btn" href="tambah.php">
                    + Tambah Brownies
                </a>

            </div>

        <?php else: ?>

            <div class="cards">

                <?php while ($brownies = mysqli_fetch_assoc($hasil)): ?>

                    <article class="product-card">

                        <div
                            class="product-art"
                            aria-hidden="true"
                        ></div>

                        <span class="pill">
                            <?= h($brownies['kategori_brownies']) ?>
                        </span>

                        <h3>
                            <?= h($brownies['nama_brownies']) ?>
                        </h3>

                        <p class="meta">
                            <?= h($brownies['merek_brownies']) ?>
                            ·
                            <?= h($brownies['varian_brownies']) ?>
                        </p>

                        <p class="price">
                            <?= rupiah($brownies['harga_brownies']) ?>
                        </p>

                        <p class="stock">
                            Stok tersedia:
                            <?= (int)$brownies['stok_brownies'] ?>
                        </p>

                        <div class="card-actions">

                            <a
                                href="detail.php?id=<?= (int)$brownies['id'] ?>"
                            >
                                Lihat detail
                            </a>

                            <a
                                href="edit.php?id=<?= (int)$brownies['id'] ?>"
                            >
                                Edit
                            </a>

                            <a
                                href="hapus.php?id=<?= (int)$brownies['id'] ?>"
                            >
                                Hapus
                            </a>

                        </div>

                    </article>

                <?php endwhile; ?>

            </div>

        <?php endif; ?>

    </main>

    <footer class="footer">
        Brownie House · Praktikum PAW Modul 2 · PHP & MySQL
    </footer>

</body>
</html>
```