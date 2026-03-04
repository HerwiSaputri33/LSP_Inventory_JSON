<?php
// function untuk membaca data
function readData() {
    $file = "inventory.json";

    // cek apakah file sudah ada atau belum
    if (!file_exists($file)) {
        // jika file belum ada, buat file baru dengan array kosong
        file_put_contents($file, json_encode([]));
    }
    // membaca data dari ineventory.json disimpan ke variable $data
    $data = file_get_contents($file);
    // mengembalikan nilai berbentuk array dari object
    return json_decode($data, true);
}
// function untuk menyimpan data
function saveData($data) {
   file_put_contents("inventory.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

$errors = [];
if (isset($_POST['tambah'])) {
    // ambil semua nilai di dalam input
    $nama = $_POST["nama"];
    $foto = $_POST["foto"];
    $kategori = $_POST["kategori"];

    // validasi input nama perangkat
    if ($nama == "") {
        $errors[] = "Nama perangkat wajib diisi.";
    }
    // validasi input untuk url foto
    if ($foto == "") {
        $errors[] = "Link gambar wajib diisi.";
    }elseif (!filter_var($foto, FILTER_VALIDATE_URL)) {
        $errors[] = "format url tidak valid.";
    }else {
        // validasi ekstensi file gambar
        $allowedExtensions = ['jpg','jpeg'];
        $ext = strtolower(pathinfo(parse_url($foto, PHP_URL_PATH), PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            $errors[] = "URL harus berupa file gambar .jpg.";
        }
    }
    // jika tidak ada error, maka data akan disimpan
    if (empty($errors)) {
        // panggil function readData
        $data = readData();

        $data[] = [
            "id" => time(),
            "nama" => $nama,
            "foto" => $foto,
            "kategori" => $kategori
        ];
        // simpan data
        saveData($data);
        header("Location: index.php");
        exit();
    }
}

IF (isset($_GET["hapus"])) {
    $id = $_GET["hapus"];
    $data = readData();

    $data = array_filter($data, function($item) use ($id) {
        return $item["id"] != $id;
    });
    saveData(array_values($data));
    header("Location: index.php");
    exit;
}

$data = readData();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Lab Inventory Manger</h1>

        <div class="card mb-4">
            <div class="card-body">

                <?php if (!empty($errors)) : ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="" method="post">
                <div class="mb-3">
                    <input type="text" name="nama" class="form-control" placeholder="Nama perangkat">
                </div>
                <div class="mb-3">
                    <input type="text" name="foto" class="form-control" placeholder="Link gambar .jpg">
                </div>

                <div class="mb-3">
                    <select name="kategori" class="form-select">
                        <option value="PC">PC</option>
                        <option value="komputer">Komputer</option>
                        <option value="networking">Networking</option>
                    </select>
                </div>

                <button type="submit" name="tambah" class="btn btn-md btn-primary">simpan</button>

                </form>
            </div>

        </div>

        <div class="text-center mb-4">
            <button class="btn btn-primary filter-btn" data-filters="Semua">Semua</button>
            <button class="btn btn-primary filter-btn" data-filters="PC">PC</button>
            <button class="btn btn-primary filter-btn" data-filters="Komputer">Komputer</button>
            <button class="btn btn-primary filter-btn" data-filters="Networking">Networking</button>
        </div>

        <div class="row">
            <?php foreach ($data as $item) : ?>
                <div class="col-md-4 card-item" data-kategori="<?= $item['kategori'] ?>">
                    <div class="card h-100 shadow-md">
                        <img src="<?= $item["foto"] ?>"
                        style="height: 200px; object-fit: cover;" />

                        <div class="card-body text-center">
                        <h5 class="card-title"><?= $item["nama"] ?></h5>
                        <p class="mb-1"><?= $item["kategori"] ?></p>


                        <a href="?hapus=<?= $item['id'] ?>"
                            class="btn btn=sm btn-danger" onclick="return confirm('Yakin ingin mwnghapus')">Hapus
                        </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
        <script>
        document.querySelectorAll(".filter-btn").forEach((button) => {
            button.addEventListener("click", function() {

                let kategori = this.dataset.filters;

                document.querySelectorAll(".card-item").forEach((card) => {

                    if (kategori === "Semua" || card.dataset.kategori === kategori) {
                        card.style.display = "block";
                    } else {
                        card.style.display = "none";
                    }

                });

            });
        });
        </script>
</body>

</html>