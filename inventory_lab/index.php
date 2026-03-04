<?php
function readData() {
    $file = "inventory.json";

    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }

    $data = file_get_contents($file);
    return json_decode($data, true);
}

function saveData($data) {
   file_put_contents("inventory.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

$data = readData();
$errors = [];
$editData = null;

/* ========================
   MODE EDIT (AMBIL DATA)
======================== */
if (isset($_GET['edit'])) {
    foreach ($data as $item) {
        if ($item['id'] == $_GET['edit']) {
            $editData = $item;
        }
    }
}

/* ========================
   TAMBAH DATA
======================== */
if (isset($_POST['tambah'])) {

    $nama = $_POST["nama"];
    $foto = $_POST["foto"];
    $kategori = $_POST["kategori"];
    $kondisi = $_POST["kondisi"];

    if ($nama == "") {
        $errors[] = "Nama perangkat wajib diisi.";
    }

    if ($foto == "") {
        $errors[] = "Link gambar wajib diisi.";
    }

    if (empty($errors)) {

        $data[] = [
            "id" => time(),
            "nama" => $nama,
            "foto" => $foto,
            "kategori" => $kategori,
            "kondisi" => $kondisi
        ];

        saveData($data);
        header("Location: index.php");
        exit();
    }
}

/* ========================
   UPDATE DATA
======================== */
if (isset($_POST['update'])) {

    foreach ($data as &$item) {
        if ($item['id'] == $_POST['id']) {
            $item['nama'] = $_POST['nama'];
            $item['foto'] = $_POST['foto'];
            $item['kategori'] = $_POST['kategori'];
            $item['kondisi'] = $_POST['kondisi'];
        }
    }

    saveData($data);
    header("Location: index.php");
    exit();
}

/* ========================
   HAPUS DATA
======================== */
if (isset($_GET["hapus"])) {

    $data = array_filter($data, function($item) {
        return $item["id"] != $_GET["hapus"];
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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

<form method="post">

<?php if ($editData) : ?>
<input type="hidden" name="id" value="<?= $editData['id'] ?>">
<?php endif; ?>

<div class="mb-3">
<input type="text" name="nama" class="form-control"
placeholder="Nama perangkat"
value="<?= $editData['nama'] ?? '' ?>">
</div>

<div class="mb-3">
<input type="text" name="foto" class="form-control"
placeholder="Link gambar .jpg"
value="<?= $editData['foto'] ?? '' ?>">
</div>

<div class="mb-3">
<select name="kategori" class="form-select">
<option value="PC" <?= (isset($editData) && $editData['kategori']=="PC") ? "selected":"" ?>>PC</option>
<option value="komputer" <?= (isset($editData) && $editData['kategori']=="komputer") ? "selected":"" ?>>Komputer</option>
<option value="networking" <?= (isset($editData) && $editData['kategori']=="networking") ? "selected":"" ?>>Networking</option>
</select>
</div>

<!-- TAMBAHAN KONDISI -->
<div class="mb-3">
<select name="kondisi" class="form-select">
<option value="Baik" <?= (isset($editData) && $editData['kondisi']=="Baik") ? "selected":"" ?>>Baik</option>
<option value="Rusak" <?= (isset($editData) && $editData['kondisi']=="Rusak") ? "selected":"" ?>>Rusak</option>
</select>
</div>

<?php if ($editData) : ?>
<button type="submit" name="update" class="btn btn-warning">Update</button>
<a href="index.php" class="btn btn-secondary">Batal</a>
<?php else : ?>
<button type="submit" name="tambah" class="btn btn-primary">simpan</button>
<?php endif; ?>

</form>
</div>
</div>

<div class="row">
<?php foreach ($data as $item) : ?>
<div class="col-md-4 card-item" data-kategori="<?= $item['kategori'] ?>">
<div class="card h-100 shadow-md">
<img src="<?= $item["foto"] ?>" style="height: 200px; object-fit: cover;" />

<div class="card-body text-center">
<h5 class="card-title"><?= $item["nama"] ?></h5>
<p class="mb-1"><?= $item["kategori"] ?></p>
<p class="mb-1"><?= $item["kondisi"] ?? '' ?></p>

<a href="?edit=<?= $item['id'] ?>" class="btn btn-sm btn-warning">Edit</a>

<a href="?hapus=<?= $item['id'] ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Yakin ingin menghapus')">Hapus</a>

</div>
</div>
</div>
<?php endforeach; ?>
</div>

</div>
</body>
</html>