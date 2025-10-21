<?php
class Buku {
    private $conn;
    private $table_name = "buku";

    public $id;
    public $judul;
    public $penulis;
    public $penerbit;
    public $tahun_terbit;
    public $isbn;
    public $kategori_id;
    public $cover_image;
    public $file_pdf;
    public $deskripsi;
    public $jumlah_halaman;
    public $bahasa;
    public $status;
    public $nama_kategori;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Upload file handler
    private function uploadFile($file, $type) {
        $file_name = basename($file["name"]);
        $file_tmp = $file["tmp_name"];
        $file_size = $file["size"];
        $file_error = $file["error"];

        // Check for errors
        if ($file_error !== UPLOAD_ERR_OK) {
            throw new Exception("Error dalam upload file: " . $file_error);
        }

        // Check file size
        if ($file_size > MAX_FILE_SIZE) {
            throw new Exception("File terlalu besar. Maksimal 10MB.");
        }

        // Get file extension
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validate file type
        if ($type === 'cover') {
            if (!in_array($file_ext, ALLOWED_COVER_TYPES)) {
                throw new Exception("Hanya file JPG, JPEG, PNG, GIF yang diizinkan untuk cover.");
            }
            $upload_dir = UPLOAD_COVER_DIR;
        } else {
            if (!in_array($file_ext, ALLOWED_PDF_TYPES)) {
                throw new Exception("Hanya file PDF yang diizinkan.");
            }
            $upload_dir = UPLOAD_PDF_DIR;
        }

        // Generate unique filename
        $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_filename;

        // Move uploaded file
        if (!move_uploaded_file($file_tmp, $upload_path)) {
            throw new Exception("Gagal menyimpan file.");
        }

        return $new_filename;
    }

    // Delete file
    private function deleteFile($filename, $type) {
        if ($type === 'cover') {
            $file_path = UPLOAD_COVER_DIR . $filename;
        } else {
            $file_path = UPLOAD_PDF_DIR . $filename;
        }

        if (file_exists($file_path) && $filename != 'default_cover.jpg') {
            unlink($file_path);
        }
    }

    // Create buku
    public function create() {
        try {
            // Handle file uploads
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $this->cover_image = $this->uploadFile($_FILES['cover_image'], 'cover');
            } else {
                $this->cover_image = 'default_cover.jpg';
            }

            if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK) {
                $this->file_pdf = $this->uploadFile($_FILES['file_pdf'], 'pdf');
            } else {
                throw new Exception("File PDF wajib diupload.");
            }

            $query = "INSERT INTO " . $this->table_name . "
                    SET judul=:judul, penulis=:penulis, penerbit=:penerbit, 
                    tahun_terbit=:tahun_terbit, isbn=:isbn, kategori_id=:kategori_id,
                    cover_image=:cover_image, file_pdf=:file_pdf, deskripsi=:deskripsi,
                    jumlah_halaman=:jumlah_halaman, bahasa=:bahasa, status=:status";

            $stmt = $this->conn->prepare($query);

            // Bind parameters
            $stmt->bindParam(":judul", $this->judul);
            $stmt->bindParam(":penulis", $this->penulis);
            $stmt->bindParam(":penerbit", $this->penerbit);
            $stmt->bindParam(":tahun_terbit", $this->tahun_terbit);
            $stmt->bindParam(":isbn", $this->isbn);
            $stmt->bindParam(":kategori_id", $this->kategori_id);
            $stmt->bindParam(":cover_image", $this->cover_image);
            $stmt->bindParam(":file_pdf", $this->file_pdf);
            $stmt->bindParam(":deskripsi", $this->deskripsi);
            $stmt->bindParam(":jumlah_halaman", $this->jumlah_halaman);
            $stmt->bindParam(":bahasa", $this->bahasa);
            $stmt->bindParam(":status", $this->status);

            if ($stmt->execute()) {
                return true;
            }
            return false;

        } catch (Exception $e) {
            // Clean up uploaded files if error occurs
            if (isset($this->cover_image) && $this->cover_image != 'default_cover.jpg') {
                $this->deleteFile($this->cover_image, 'cover');
            }
            if (isset($this->file_pdf)) {
                $this->deleteFile($this->file_pdf, 'pdf');
            }
            throw $e;
        }
    }

    // Read all books with category name
    public function read() {
        $query = "SELECT 
                    b.*, 
                    k.nama_kategori 
                  FROM " . $this->table_name . " b
                  LEFT JOIN kategori k ON b.kategori_id = k.id
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single book
    public function readOne() {
        $query = "SELECT 
                    b.*, 
                    k.nama_kategori 
                  FROM " . $this->table_name . " b
                  LEFT JOIN kategori k ON b.kategori_id = k.id
                  WHERE b.id = ? 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->judul = $row['judul'];
            $this->penulis = $row['penulis'];
            $this->penerbit = $row['penerbit'];
            $this->tahun_terbit = $row['tahun_terbit'];
            $this->isbn = $row['isbn'];
            $this->kategori_id = $row['kategori_id'];
            $this->cover_image = $row['cover_image'];
            $this->file_pdf = $row['file_pdf'];
            $this->deskripsi = $row['deskripsi'];
            $this->jumlah_halaman = $row['jumlah_halaman'];
            $this->bahasa = $row['bahasa'];
            $this->status = $row['status'];
            $this->nama_kategori = $row['nama_kategori'];
            return true;
        }
        return false;
    }

    // Update book
    public function update() {
        try {
            // Get current data
            $current_data = $this->readOne();

            // Handle cover image upload
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                // Delete old cover if exists and not default
                if ($this->cover_image && $this->cover_image != 'default_cover.jpg') {
                    $this->deleteFile($this->cover_image, 'cover');
                }
                $this->cover_image = $this->uploadFile($_FILES['cover_image'], 'cover');
            } else {
                // Keep existing cover
                $this->cover_image = $this->cover_image;
            }

            // Handle PDF upload
            if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK) {
                // Delete old PDF
                if ($this->file_pdf) {
                    $this->deleteFile($this->file_pdf, 'pdf');
                }
                $this->file_pdf = $this->uploadFile($_FILES['file_pdf'], 'pdf');
            } else {
                // Keep existing PDF
                $this->file_pdf = $this->file_pdf;
            }

            $query = "UPDATE " . $this->table_name . "
                    SET judul=:judul, penulis=:penulis, penerbit=:penerbit, 
                    tahun_terbit=:tahun_terbit, isbn=:isbn, kategori_id=:kategori_id,
                    cover_image=:cover_image, file_pdf=:file_pdf, deskripsi=:deskripsi,
                    jumlah_halaman=:jumlah_halaman, bahasa=:bahasa, status=:status
                    WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":judul", $this->judul);
            $stmt->bindParam(":penulis", $this->penulis);
            $stmt->bindParam(":penerbit", $this->penerbit);
            $stmt->bindParam(":tahun_terbit", $this->tahun_terbit);
            $stmt->bindParam(":isbn", $this->isbn);
            $stmt->bindParam(":kategori_id", $this->kategori_id);
            $stmt->bindParam(":cover_image", $this->cover_image);
            $stmt->bindParam(":file_pdf", $this->file_pdf);
            $stmt->bindParam(":deskripsi", $this->deskripsi);
            $stmt->bindParam(":jumlah_halaman", $this->jumlah_halaman);
            $stmt->bindParam(":bahasa", $this->bahasa);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":id", $this->id);

            if ($stmt->execute()) {
                return true;
            }
            return false;

        } catch (Exception $e) {
            throw $e;
        }
    }

    // Delete book
    public function delete() {
        // Get book data first to delete files
        if ($this->readOne()) {
            // Delete cover image
            if ($this->cover_image && $this->cover_image != 'default_cover.jpg') {
                $this->deleteFile($this->cover_image, 'cover');
            }
            
            // Delete PDF file
            if ($this->file_pdf) {
                $this->deleteFile($this->file_pdf, 'pdf');
            }

            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);

            if ($stmt->execute()) {
                return true;
            }
        }
        return false;
    }

    // Search books
    public function search($keywords) {
        $query = "SELECT 
                    b.*, 
                    k.nama_kategori 
                  FROM " . $this->table_name . " b
                  LEFT JOIN kategori k ON b.kategori_id = k.id
                  WHERE b.judul LIKE ? OR b.penulis LIKE ? OR k.nama_kategori LIKE ?
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $keywords = "%{$keywords}%";
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->execute();
        
        return $stmt;
    }

    // Get books by category
    public function readByCategory($category_id) {
        $query = "SELECT 
                    b.*, 
                    k.nama_kategori 
                  FROM " . $this->table_name . " b
                  LEFT JOIN kategori k ON b.kategori_id = k.id
                  WHERE b.kategori_id = ?
                  ORDER BY b.judul";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $category_id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>