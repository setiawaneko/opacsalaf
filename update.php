<?php
include_once 'config.php';
include_once 'Buku.php';

$database = new Database();
$db = $database->getConnection();
$buku = new Buku($db);

$buku->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID tidak ditemukan.');

if(!$buku->readOne()){
    die('Buku tidak ditemukan.');
}

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
        
        if($buku->update()){
            $message = '<div class="alert alert-success">Buku berhasil diupdate.</div>';
            // Refresh data
            $buku->readOne();
        } else{
            $message = '<div class="alert alert-danger">Gagal mengupdate buku.</div>';
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
    <title>Edit Buku - Living Library</title>
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
                    <div class="card-header bg-warning">
                        <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Buku: <?php echo htmlspecialchars($buku->judul); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php echo $message; ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 text-center">
                                <img src="uploads/covers/<?php echo $buku->cover_image; ?>" 
                                     class="img-thumbnail" 
                                     style="max-height: 200px;"
                                     onerror="this.src='uploads/covers/default_cover.jpg'">
                                <div class="mt-2">
                                    <small>Cover Saat Ini</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <strong>File PDF Saat Ini:</strong><br>
                                    <a href="uploads/pdf/<?php echo $buku->file_pdf; ?>" target="_blank">
                                        <?php echo $buku->file_pdf; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$buku->id}"); ?>" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Judul Buku *</label>
                                        <input type="text" name="judul" class="form-control" 
                                               value="<?php echo htmlspecialchars($buku->judul); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Penulis *</label>
                                        <input type="text" name="penulis" class="form-control" 
                                               value="<?php echo htmlspecialchars($buku->penulis); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Penerbit</label>
                                        <input type="text" name="penerbit" class="form-control" 
                                               value="<?php echo htmlspecialchars($buku->penerbit); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Tahun Terbit</label>
                                        <input type="number" name="tahun_terbit" class="form-control" 
                                               value="<?php echo $buku->tahun_terbit; ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">ISBN</label>
                                        <input type="text" name="isbn" class="form-control" 
                                               value="<?php echo $buku->isbn; ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kategori</label>
                                        <select name="kategori_id" class="form-control" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php 
                                            $kategori_stmt->execute();
                                            while ($kategori = $kategori_stmt->fetch(PDO::FETCH_ASSOC)): 
                                            ?>
                                            <option value="<?php echo $kategori['id']; ?>" 
                                                <?php echo $buku->kategori_id == $kategori['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Ganti Cover Buku</label>
                                        <input type="file" name="cover_image" class="form-control" accept="image/*">
                                        <div class="form-text">Kosongkan jika tidak ingin mengganti cover</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Ganti File PDF</label>
                                        <input type="file" name="file_pdf" class="form-control" accept=".pdf">
                                        <div class="form-text">Kosongkan jika tidak ingin mengganti PDF</div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Jumlah Halaman</label>
                                            <input type="number" name="jumlah_halaman" class="form-control" 
                                                   value="<?php echo $buku->jumlah_halaman; ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Bahasa</label>
                                            <select name="bahasa" class="form-control">
                                                <option value="Indonesia" <?php echo $buku->bahasa == 'Indonesia' ? 'selected' : ''; ?>>Indonesia</option>
                                                <option value="Arab" <?php echo $buku->bahasa == 'Arab' ? 'selected' : ''; ?>>Arab</option>
                                                <option value="Inggris" <?php echo $buku->bahasa == 'Inggris' ? 'selected' : ''; ?>>Inggris</option>
                                                <option value="Jawa" <?php echo $buku->bahasa == 'Jawa' ? 'selected' : ''; ?>>Jawa</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="Tersedia" <?php echo $buku->status == 'Tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                                            <option value="Dipinjam" <?php echo $buku->status == 'Dipinjam' ? 'selected' : ''; ?>>Dipinjam</option>
                                            <option value="Dalam Perbaikan" <?php echo $buku->status == 'Dalam Perbaikan' ? 'selected' : ''; ?>>Dalam Perbaikan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi Buku</label>
                                <textarea name="deskripsi" class="form-control" rows="4"><?php echo htmlspecialchars($buku->deskripsi); ?></textarea>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Buku
                                </button>
                                <a href="read.php?id=<?php echo $buku->id; ?>" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
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