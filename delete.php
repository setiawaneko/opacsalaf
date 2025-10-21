<?php
include_once 'config.php';
include_once 'Buku.php';

$database = new Database();
$db = $database->getConnection();
$buku = new Buku($db);

$buku->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID tidak ditemukan.');

if($buku->delete()){
    header("Location: index.php?message=Buku berhasil dihapus");
} else{
    header("Location: index.php?message=Gagal menghapus buku");
}
?>