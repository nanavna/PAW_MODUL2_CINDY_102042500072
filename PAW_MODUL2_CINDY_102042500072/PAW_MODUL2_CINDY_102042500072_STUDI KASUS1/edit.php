```php
<?php
require 'koneksi.php';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare(
    $conn,
    'SELECT * FROM brownies WHERE id=?'
);

mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$brownies = mysqli_fetch_assoc($hasil);

if (!$brownies) {
    http_response_code(404);
    exit('Brownies tidak ditemukan. <a href="index.php">Kembali ke katalog</a>.');
}

$kode = $brownies['kode_brownies'];
$nama = $brownies['nama_brownies'];
$brand = $brownies['merek_brownies'];
$kategori = $brownies['kategori_brownies'];
$jenis = $brownies['varian_brownies'];
$harga = $brownies['harga_brownies'];
$stok = $brownies['stok_brownies'];
$deskripsi = $brownies['deskripsi_brownies'];

$pesan = '';

if (isset($_POST['simpan'])) {

    $kode = trim($_POST['kode_brownies'] ?? '');
    $nama = trim($_POST['nama_brownies'] ?? '');
    $brand = trim($_POST['merek_brownies'] ?? '');
    $kategori = trim($_POST['kategori_brownies'] ?? '');
    $jenis = trim($_POST['varian_brownies'] ?? '');
    $harga = trim($_POST['harga_brownies'] ?? '');
    $stok = trim($_POST['stok_brownies'] ?? '');
    $deskripsi = trim($_POST['deskripsi_brownies'] ?? '');

    if (
        $kode === '' ||
        $nama === '' ||
        $brand === '' ||
        $kategori === '' ||
        $jenis === '' ||
        $deskripsi === ''
    ) {
        $pesan = 'Semua kolom wajib diisi.';
    } elseif (
        !is_numeric($harga) ||
        $harga <= 0 ||
        $harga > 9999999999
    ) {
        $pesan = 'Harga harus berupa angka positif tanpa Rp atau titik.';
    } elseif (
        filter_var($stok, FILTER_VALIDATE_INT) === false ||
        $stok < 0
    ) {
        $pesan = 'Stok harus berupa bilangan bulat nol atau lebih.';
    } else {

        $stmt = mysqli_prepare(
            $conn,
            'SELECT id FROM brownies WHERE kode_brownies=? AND id<>?'
        );

        mysqli_stmt_bind_param($stmt, 'si', $kode, $id);
        mysqli_stmt_execute($stmt);

        $cek = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($cek) > 0) {

            $pesan = 'Kode brownies sudah digunakan brownies lain.';

        } else {

            $stmt = mysqli_prepare(
                $conn,
                'UPDATE brownies
                SET kode_brownies=?,
                    nama_brownies=?,
                    merek_brownies=?,
                    kategori_brownies=?,
                    varian_brownies=?,
                    harga_brownies=?,
                    stok_brownies=?,
                    deskripsi_brownies=?
                WHERE id=?'
            );

            mysqli_stmt_bind_param(
                $stmt,
                'sssssdisi',
                $kode,
                $nama,
                $brand,
                $kategori,
                $jenis,
                $harga,
                $stok,
                $deskripsi,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                header('Location: detail.php?id=' . $id . '&pesan=1');
                exit;
            }

            $pesan = 'Brownies gagal diperbarui. Periksa kembali data.';
        }
    }
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

    <title>Edit Produk — Brownie House</title>

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

        <p class="eyebrow">Brownies management</p>

        <h1 class="page-title">Edit brownies</h1>

        <p class="meta">
            Perbarui informasi brownies di bawah ini.
        </p>

        <section class="panel">

            <?php if ($pesan !== ''): ?>
                <div class="error">
                    <?= h($pesan) ?>
                </div>
            <?php endif; ?>

            <form method="post">

                <div class="form-grid">

                    <div>
                        <label for="kode">Kode brownies</label>

                        <input
                            id="kode"
                            name="kode_brownies"
                            maxlength="20"
                            required
                            value="<?= h($kode) ?>"
                        >
                    </div>

                    <div>
                        <label for="nama">Nama brownies</label>

                        <input
                            id="nama"
                            name="nama_brownies"
                            maxlength="100"
                            required
                            value="<?= h($nama) ?>"
                        >
                    </div>

                    <div>
                        <label for="brand">Merek</label>

                        <input
                            id="brand"
                            name="merek_brownies"
                            maxlength="50"
                            required
                            value="<?= h($brand) ?>"
                        >
                    </div>

                    <div>
                        <label for="kategori">Kategori brownies</label>

                        <select
                            id="kategori"
                            name="kategori_brownies"
                            required
                        >
                            <option value="">
                                Pilih kategori brownies
                            </option>

                            <?php
                            foreach (
                                [
                                    'Brownies klasik',
                                    'Brownies premium',
                                    'Brownies bite-size',
                                    'Brownies hampers',
                                    'Brownies kukus',
                                    'Brownies panggang'
                                ] as $opt
                            ):
                            ?>

                                <option
                                    value="<?= h($opt) ?>"
                                    <?= $kategori === $opt ? 'selected' : '' ?>
                                >
                                    <?= h($opt) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div>
                        <label for="jenis">Model brownies</label>

                        <select
                            id="jenis"
                            name="varian_brownies"
                            required
                        >
                            <option value="">
                                Pilih model brownies
                            </option>

                            <?php
                            foreach (
                                [
                                    'Classic',
                                    'Almond',
                                    'Cheese',
                                    'Walnut',
                                    'Fudgy',
                                    'Double Chocolate'
                                ] as $opt
                            ):
                            ?>

                                <option
                                    value="<?= h($opt) ?>"
                                    <?= $jenis === $opt ? 'selected' : '' ?>
                                >
                                    <?= h($opt) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div>
                        <label for="harga">Harga (angka saja)</label>

                        <input
                            id="harga"
                            name="harga_brownies"
                            type="number"
                            min="1"
                            step="1"
                            required
                            value="<?= h($harga) ?>"
                        >
                    </div>

                    <div>
                        <label for="stok">Stok</label>

                        <input
                            id="stok"
                            name="stok_brownies"
                            type="number"
                            min="0"
                            step="1"
                            required
                            value="<?= h($stok) ?>"
                        >
                    </div>

                    <div class="full">
                        <label for="deskripsi">
                            Deskripsi brownies
                        </label>

                        <textarea
                            id="deskripsi"
                            name="deskripsi_brownies"
                            required
                        ><?= h($deskripsi) ?></textarea>
                    </div>

                </div>

                <div class="actions">

                    <button
                        class="btn"
                        name="simpan"
                        type="submit"
                    >
                        Simpan perubahan
                    </button>

                    <a
                        class="btn secondary"
                        href="detail.php?id=<?= $id ?>"
                    >
                        Batal
                    </a>

                </div>

            </form>

        </section>

    </main>

    <footer class="footer">
        Brownie House · Praktikum PAW Modul 2
    </footer>

</body>
</html>
```