<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------
    public $validBarang = [
        'id_barang' => 'required',
        'nama_barang' => 'required',
        'jenis_barang' => 'required'
    ];

    public $validStok = [
        'stok' => 'required'
    ];

    public $validJual = [
        'jumlah_terjual' => 'required',
        'tanggal_transaksi' => 'required'
    ];

    public $barang_errors = [
        'id_barang' => [
            'required'     => 'ID Barang wajib diisi.'
        ],
        'nama_barang' => [
            'required'     => 'Nama Barang wajib diisi.'
        ],
        'jenis_barang' => [
            'required'     => 'Jenis Barang wajib diisi.'
        ]
    ];

    public $stok_errors = [
        'stok' => [
            'required'     => 'Stok Barang wajib diisi.'
        ]
    ];

    public $jual_errors = [
        'jumlah_terjual' => [
            'required'     => 'Jumlah Barang Terjual wajib diisi.'
        ],
        'tanggal_transaksi' => [
            'required'     => 'Tanggal Transaksi Barang wajib diisi.'
        ]
    ];
}
