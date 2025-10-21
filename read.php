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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $buku->judul; ?> - Living Library</title>
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
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <img src="uploads/covers/<?php echo $buku->cover_image; ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($buku->judul); ?>"
                         onerror="this.src='uploads/covers/default_cover.jpg'">
                </div>
                <div class="mt-3 text-center">
                    <a href="uploads/pdf/<?php echo $buku->file_pdf; ?>" 
                       class="btn btn-primary btn-lg w-100" target="_blank">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                    <div class="btn-group w-100 mt-2">
                        <a href="update.php?id=<?php echo $buku->id; ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="delete.php?id=<?php echo $buku->id; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Yakin ingin menghapus buku ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0"><?php echo htmlspecialchars($buku->judul); ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Penulis:</strong> <?php echo htmlspecialchars($buku->penulis); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Penerbit:</strong> <?php echo htmlspecialchars($buku->penerbit); ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Tahun Terbit:</strong> <?php echo $buku->tahun_terbit; ?>
                            </div>
                            <div class="col-md-6">
                                <strong>ISBN:</strong> <?php echo $buku->isbn ?: '-'; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Kategori:</strong> <?php echo htmlspecialchars($buku->nama_kategori); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <span class="badge 
                                    <?php 
                                    if($buku->status == 'Tersedia') echo 'bg-success';
                                    elseif($buku->status == 'Dipinjam') echo 'bg-warning';
                                    else echo 'bg-danger';
                                    ?>">
                                    <?php echo $buku->status; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Jumlah Halaman:</strong> <?php echo $buku->jumlah_halaman ?: '-'; ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Bahasa:</strong> <?php echo $buku->bahasa; ?>
                            </div>
                        </div>
                        
                        <?php if($buku->deskripsi): ?>
                        <div class="mb-3">
                            <strong>Deskripsi:</strong>
                            <p class="mt-2"><?php echo nl2br(htmlspecialchars($buku->deskripsi)); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <h5>Pratinjau PDF</h5>
                            <iframe src="uploads/pdf/<?php echo $buku->file_pdf; ?>#toolbar=0" 
                                    width="100%" height="500" style="border: 1px solid #ddd;">
                                Browser Anda tidak mendukung preview PDF.
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Buku
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>