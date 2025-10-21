<?php
include_once 'config.php';
include_once 'Buku.php';

$database = new Database();
$db = $database->getConnection();
$buku = new Buku($db);

// Get categories for dropdown
$kategori_query = "SELECT * FROM kategori ORDER BY nama_kategori";
$kategori_stmt = $db->prepare($kategori_query);
$kategori_stmt->execute();

$message = '';
if($_POST){
    try {
        $buku->judul = $_POST['judul'];
        $buku->penulis = $_POST['penulis'];
        $buku->penerbit = $_POST['penerbit'];
        $buku->tahun_terbit = $_POST['tahun_terbit'];
        $buku->isbn = $_POST['isbn'];
        $buku->kategori_id = $_POST['kategori_id'];
        $buku->deskripsi = $_POST['deskripsi'];
        $buku->jumlah_halaman = $_POST['jumlah_halaman'];
        $buku->bahasa = $_POST['bahasa'];
        $buku->status = $_POST['status'];
        
        if($buku->create()){
            $message = '<div class="alert alert-success">Buku berhasil ditambahkan.</div>';
        } else{
            $message = '<div class="alert alert-danger">Gagal menambahkan buku.</div>';
        }
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku Baru - Living Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book"></i> Living Library Assalafiyyah Mlangi
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-plus"></i> Tambah Buku Baru</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Judul Buku *</label>
                                        <input type="text" name="judul" class="form-control" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Penulis *</label>
                                        <input type="text" name="penulis" class="form-control" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Penerbit</label>
                                        <input type="text" name="penerbit" class="form-control">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Tahun Terbit</label>
                                        <input type="number" name="tahun_terbit" class="form-control" 
                                               min="1000" max="<?php echo date('Y'); ?>" 
                                               value="<?php echo date('Y'); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">ISBN</label>
                                        <input type="text" name="isbn" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kategori</label>
                                        <select name="kategori_id" class="form-control" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php while ($kategori = $kategori_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $kategori['id']; ?>">
                                                <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Cover Buku</label>
                                        <input type="file" name="cover_image" class="form-control" accept="image/*">
                                        <div class="form-text">Format: JPG, JPEG, PNG, GIF (Maks. 10MB)</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">File PDF *</label>
                                        <input type="file" name="file_pdf" class="form-control" accept=".pdf" required>
                                        <div class="form-text">Hanya file PDF (Maks. 10MB)</div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Jumlah Halaman</label>
                                            <input type="number" name="jumlah_halaman" class="form-control" min="1">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Bahasa</label>
                                            <select name="bahasa" class="form-control">
                                                <option value="Indonesia">Indonesia</option>
                                                <option value="Arab">Arab</option>
                                                <option value="Inggris">Inggris</option>
                                                <option value="Jawa">Jawa</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="Tersedia">Tersedia</option>
                                            <option value="Dipinjam">Dipinjam</option>
                                            <option value="Dalam Perbaikan">Dalam Perbaikan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi Buku</label>
                                <textarea name="deskripsi" class="form-control" rows="4" 
                                          placeholder="Deskripsi singkat tentang buku..."></textarea>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Simpan Buku
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>