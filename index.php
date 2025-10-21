<?php
include_once 'config.php';
include_once 'Buku.php';

$database = new Database();
$db = $database->getConnection();
$buku = new Buku($db);

$search_keyword = '';
if(isset($_POST['search'])) {
    $search_keyword = $_POST['search_keyword'];
    $stmt = $buku->search($search_keyword);
} else {
    $stmt = $buku->read();
}
$num = $stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Living Library Assalafiyyah Mlangi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .book-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .book-card:hover {
            transform: translateY(-5px);
        }
        .book-cover {
            height: 250px;
            object-fit: cover;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-book"></i> Living Library Assalafiyyah Mlangi
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Search Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" class="row g-3">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" name="search_keyword" class="form-control" 
                                           placeholder="Cari buku berdasarkan judul, penulis, atau kategori..." 
                                           value="<?php echo htmlspecialchars($search_keyword); ?>">
                                    <button type="submit" name="search" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <a href="create.php" class="btn btn-success w-100">
                                    <i class="fas fa-plus"></i> Tambah Buku Baru
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Books Grid -->
        <?php if($num > 0): ?>
        <div class="row">
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                $status_class = '';
                switch($row['status']) {
                    case 'Tersedia': $status_class = 'bg-success'; break;
                    case 'Dipinjam': $status_class = 'bg-warning'; break;
                    case 'Dalam Perbaikan': $status_class = 'bg-danger'; break;
                }
            ?>
            <div class="col-md-3 mb-4">
                <div class="card book-card shadow-sm">
                    <div class="position-relative">
                        <img src="uploads/covers/<?php echo $row['cover_image']; ?>" 
                             class="card-img-top book-cover" 
                             alt="<?php echo htmlspecialchars($row['judul']); ?>"
                             onerror="this.src='uploads/covers/default_cover.jpg'">
                        <span class="badge <?php echo $status_class; ?> status-badge">
                            <?php echo $row['status']; ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title"><?php echo htmlspecialchars($row['judul']); ?></h6>
                        <p class="card-text text-muted small">
                            <strong>Penulis:</strong> <?php echo htmlspecialchars($row['penulis']); ?><br>
                            <strong>Kategori:</strong> <?php echo htmlspecialchars($row['nama_kategori']); ?><br>
                            <strong>Tahun:</strong> <?php echo $row['tahun_terbit']; ?>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100">
                            <a href="read.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="uploads/pdf/<?php echo $row['file_pdf']; ?>" 
                               class="btn btn-sm btn-primary" target="_blank">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Yakin ingin menghapus buku ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Tidak ada buku ditemukan</h4>
                        <p class="text-muted">Silakan tambah buku baru atau gunakan kata kunci pencarian lain.</p>
                        <a href="create.php" class="btn btn-success">
                            <i class="fas fa-plus"></i> Tambah Buku Pertama
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container text-center">
            <p>&copy; 2024 Living Library Assalafiyyah Mlangi. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>