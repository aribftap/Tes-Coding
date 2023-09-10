<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table = 'tbl_penjualan';
    protected $primaryKey = 'id_penjualan';
    protected $allowedFields = ['id_barang', 'jumlah_terjual', 'tanggal_transaksi'];

    public function getIdProduct($id_barang = false)
    {
        if ($id_barang === false) {
            return $this->findAll();
        } else {
            return $this->getWhere(['id_barang' => $id_barang])->getRowArray();
        }
    }

    public function updateProduct($penjualanDataUpdate, $id_barang)
    {
        return $this->db->table($this->table)->update($penjualanDataUpdate, ['id_barang' => $id_barang]);
    }
}
