<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table = 'tbl_barang';
    protected $primaryKey = 'id_barang';
    protected $allowedFields = ['id_barang', 'nama_barang', 'jenis_barang'];

    public function getIdProduct($id_barang = false)
    {
        if ($id_barang === false) {
            return $this->findAll();
        } else {
            return $this->getWhere(['id_barang' => $id_barang])->getRowArray();
        }
    }

    public function updateProduct($barangDataUpdate, $id_barang)
    {
        return $this->db->table($this->table)->update($barangDataUpdate, ['id_barang' => $id_barang]);
    }

    public function deleteCategory($id)
    {
        return $this->db->table($this->table)->delete(['category_id' => $id]);
    }
}
