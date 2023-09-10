<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use \App\Models\BarangModel;
use \App\Models\StokModel;
use \App\Models\PenjualanModel;


class Product extends ResourceController
{
    /*model*/
    protected $BarangModel;
    protected $StokModel;
    protected $PenjualanModel;

    /*db*/
    protected $db;
    use ResponseTrait;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->BarangModel = new BarangModel();
        $this->StokModel = new StokModel();
        $this->PenjualanModel = new PenjualanModel();
    }

    public function index()
    {
        // Ambil semua data dari tiga model
        $barangData = $this->BarangModel->findAll();
        $stokData = $this->StokModel->findAll();
        $penjualanData = $this->PenjualanModel->findAll();

        // Inisialisasi array untuk hasil gabungan
        $responseData = [];

        // Loop melalui data barang
        foreach ($barangData as $barang) {
            // Temukan data stok berdasarkan id_barang
            $stok = $this->findStokData($stokData, $barang['id_barang']);

            // Temukan data penjualan berdasarkan id_barang
            $penjualan = $this->findPenjualanData($penjualanData, $barang['id_barang']);

            // Gabungkan data yang dibutuhkan
            $mergedData = [
                'Nama Barang' => $barang['nama_barang'],
                'Stok' => $stok ? $stok['stok'] : 0, // Jika stok tidak ada, anggap 0
                'Jumlah Terjual' => $penjualan ? $penjualan['jumlah_terjual'] : 0, // Jika penjualan tidak ada, anggap 0
                'Tanggal Transaksi' => $penjualan ? $penjualan['tanggal_transaksi'] : null, // Tanggal transaksi jika ada, null jika tidak ada
                'Jenis Barang' => $barang['jenis_barang']
            ];

            // Tambahkan data ke array respons
            $responseData[] = $mergedData;
        }

        return $this->respond($responseData, 200);
    }

    // Fungsi untuk mencari data stok berdasarkan id_barang
    private function findStokData($stokData, $id_barang)
    {
        foreach ($stokData as $stok) {
            if ($stok['id_barang'] === $id_barang) {
                return $stok;
            }
        }
        return null; // Stok tidak ditemukan
    }

    // Fungsi untuk mencari data penjualan berdasarkan id_barang
    private function findPenjualanData($penjualanData, $id_barang)
    {
        foreach ($penjualanData as $penjualan) {
            if ($penjualan['id_barang'] === $id_barang) {
                return $penjualan;
            }
        }
        return null; // Data penjualan tidak ditemukan
    }

    public function show($id_barang = null)
    {
        $id_barang = $this->request->getVar('id_barang');

        // Cari data barang berdasarkan $id_barang
        $barangData = $this->BarangModel->where('id_barang', $id_barang)->first();
        $stokData = $this->StokModel->where('id_barang', $id_barang)->first();
        $penjualanData = $this->PenjualanModel->where('id_barang', $id_barang)->first();

        if (!$barangData || !$stokData || !$penjualanData) {
            return $this->failNotFound('Data Barang tidak ditemukan.');
        }

        // Gabungkan data yang diperlukan
        $responseData = [
            'Nama Barang' => $barangData['nama_barang'],
            'Stok' => $stokData['stok'] ?? 0,
            'Jumlah Terjual' => $penjualanData['jumlah_terjual'] ?? 0,
            'Tanggal Transaksi' => $penjualanData['tanggal_transaksi'] ?? null,
            'Jenis Barang' => $barangData['jenis_barang']
        ];

        return $this->respond($responseData, 200);
    }

    public function new()
    {
        // Ambil data yang dikirimkan oleh klien
        $postDataBarang = $this->request->getPost();
        $postDataStok = $this->request->getPost();
        $postDataPenjualan = $this->request->getPost();

        // Validasi data pada tbl_barang
        if (empty($postDataBarang['id_barang'] || empty($postDataBarang['nama_barang']) || empty($postDataBarang['jenis_barang']))) {
            return $this->fail('ID Barang, Nama Barang dan Jenis Barang harus diisi.', 500);
        }

        // Inisialisasi data untuk model tbl_barang
        $barangData = [
            'id_barang' => $postDataBarang['id_barang'],
            'nama_barang' => $postDataBarang['nama_barang'],
            'jenis_barang' => $postDataBarang['jenis_barang']
        ];

        // Validasi data pada tbl_stok
        if (empty($postDataStok['id_barang'] || empty($postDataStok['stok']))) {
            return $this->fail('Stok Barang harus diisi.', 500);
        }

        // Inisialisasi data untuk model tbl_stok
        $stokData = [
            'id_barang' => $postDataStok['id_barang'],
            'stok' => $postDataStok['stok']
        ];

        // Validasi data pada tbl_penjualan
        if (empty($postDataPenjualan['id_barang'] || empty($postDataPenjualan['jumlah_terjual']) || empty($postDataPenjualan['tanggal_transaksi']))) {
            return $this->fail('Jumlah Terjual dan Tanggal Transaksi Barang harus diisi.', 400);
        }

        // Inisialisasi data untuk model tbl_penjualan
        $penjualanData = [
            'id_barang' => $postDataPenjualan['id_barang'],
            'jumlah_terjual' => $postDataPenjualan['jumlah_terjual'],
            'tanggal_transaksi' => $postDataPenjualan['tanggal_transaksi']
        ];

        // Masukan data ke dalam database
        $barangInserted = $this->BarangModel->insert($barangData);
        $stokInserted = $this->StokModel->insert($stokData);
        $penjualanInserted = $this->PenjualanModel->insert($penjualanData);

        if (!$barangInserted && !$stokInserted && !$penjualanInserted) {
            return $this->fail('Gagal menambahkan barang baru.', 400);
        } else {
            return $this->respondCreated(['message' => 'Data Barang berhasil ditambahkan.']);
        }
    }

    public function update($id_barang = null)
    {
        $id_barang = $this->request->getVar('id_barang');

        // Cari data barang berdasarkan $id_barang
        $barangData = $this->BarangModel->where('id_barang', $id_barang)->first();
        $stokData = $this->StokModel->where('id_barang', $id_barang)->first();
        $penjualanData = $this->PenjualanModel->where('id_barang', $id_barang)->first();

        if (!$barangData || !$stokData || !$penjualanData) {
            return $this->failNotFound('Data Barang tidak ditemukan.');
        }


        // Validasi data pada tbl_barang
        if (!isset($postDataBarang['id_barang']) || !isset($postDataBarang['nama_barang']) || !isset($postDataBarang['jenis_barang'])) {
            return $this->fail('Nama Barang dan Jenis Barang harus diisi.', 400);
        } else {
            // Inisialisasi data untuk model tbl_barang
            $barangDataUpdate = [
                'id_barang' => $postDataBarang['id_barang'],
                'nama_barang' => $postDataBarang['nama_barang'],
                'jenis_barang' => $postDataBarang['jenis_barang']
            ];

            // Update data pada model tbl_barang jika ada
            $this->BarangModel->update($barangDataUpdate);
        }


        // Validasi data pada tbl_stok
        if (!isset($postDataStok['id_barang']) || !isset($postDataStok['stok'])) {
            return $this->fail('Stok Barang harus diisi.', 400);
        } else {
            // Inisialisasi data untuk model tbl_barang
            $stokDataUpdate = [
                'id_barang' => $postDataStok['id_barang'],
                'stok' => $postDataStok['stok']
            ];

            // Update data pada model tbl_barang jika ada
            $this->StokModel->update($stokDataUpdate);
        }


        // Validasi data pada tbl_penjualan
        if (!isset($postDataPenjualan['id_barang']) || !isset($postDataPenjualan['jumlah_terjual']) || !isset($postDataPenjualan['tanggal_transaksi'])) {
            return $this->fail('Stok Barang harus diisi.', 400);
        } else {
            // Inisialisasi data untuk model tbl_barang
            $penjualanDataUpdate = [
                'id_barang' => $postDataPenjualan['id_barang'],
                'jumlah_terjual' => $postDataPenjualan['jumlah_terjual'],
                'tanggal_transaksi' => $postDataPenjualan['tanggal_transaksi']
            ];

            // Update data pada model tbl_barang jika ada
            $this->PenjualanModel->update($penjualanDataUpdate);
        }

        return $this->respond(['message' => 'Data Barang berhasil diedit.']);
    }


    public function delete($id_barang = null)
    {
        $id_barang = $this->request->getVar('id_barang');

        // Cari data penjualan berdasarkan $id_barang
        $penjualanData = $this->PenjualanModel->where('id_barang', $id_barang)->first();
        $stokData = $this->StokModel->where('id_barang', $id_barang)->first();
        $barangData = $this->BarangModel->where('id_barang', $id_barang)->first();

        if (!$penjualanData && !$stokData && !$barangData) {
            return $this->failNotFound('Data Barang tidak ditemukan.');
        }

        // Hapus data dari model tbl_penjualan
        $this->PenjualanModel->delete($id_barang);

        // Hapus data dari model tbl_barang
        $this->StokModel->delete($id_barang);
        $this->BarangModel->delete($id_barang);

        // Jika Anda memiliki model StokModel, lakukan hal yang sama untuk menghapus data dari model tersebut.

        return $this->respond(['message' => 'Data Barang berhasil dihapus.']);
    }
}
