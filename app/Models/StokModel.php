<?php

namespace App\Models;

use CodeIgniter\Model;

class StokModel extends Model
{
    protected $table = 'tbl_stok';
    protected $primaryKey = 'id_stok';
    protected $allowedFields = ['id_barang', 'stok'];

    public function getIdProduct($id_barang = false)
    {
        if ($id_barang === false) {
            return $this->findAll();
        } else {
            return $this->getWhere(['id_barang' => $id_barang])->getRowArray();
        }
    }

    public function updateProduct($stokDataUpdate, $id_barang)
    {
        return $this->db->table($this->table)->update($stokDataUpdate, ['id_barang' => $id_barang]);
    }
}
