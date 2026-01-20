<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;

class Migration extends Controller
{
    public function index()
    {
        $forge = Database::forge();
        $db = Database::connect();

        echo "<h1>Veritabanı Güncellemesi Başlatılıyor...</h1>";

        // 1. Companies Tablosunu Oluştur
        if (!$db->tableExists('companies')) {
            $fields = [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'company_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => '150',
                ],
                'logo_url' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => true,
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['aktif', 'pasif'],
                    'default' => 'aktif',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ];
            $forge->addField($fields);
            $forge->addKey('id', true);
            $forge->createTable('companies');
            echo "<p style='color:green'>Companies tablosu oluşturuldu.</p>";

            // Varsayılan şirket oluştur
            $db->table('companies')->insert([
                'company_name' => 'Demo Şirketi',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $defaultCompanyId = $db->insertID();
            echo "<p style='color:green'>Varsayılan şirket oluşturuldu. ID: $defaultCompanyId</p>";
        } else {
            echo "<p style='color:orange'>Companies tablosu zaten var.</p>";
            $defaultCompanyId = 1;
        }

        // 2. Diğer tablolara company_id ekle
        $tables = [
            'users',
            'customers',
            'transactions',
            'notes',
            'my_debts',
            'creditors',
            'expense_categories',
            'products',
            'ai_chat_history'
        ];

        foreach ($tables as $table) {
            if ($db->tableExists($table)) {
                $fields = $db->getFieldNames($table);
                if (!in_array('company_id', $fields)) {
                    $column = [
                        'company_id' => [
                            'type' => 'INT',
                            'constraint' => 11,
                            'unsigned' => true,
                            'default' => $defaultCompanyId, // Mevcut veriler varsayılan şirkete atansın
                            'after' => 'id'
                        ]
                    ];
                    $forge->addColumn($table, $column);
                    echo "<p style='color:green'>$table tablosuna company_id eklendi.</p>";
                } else {
                    echo "<p style='color:gray'>$table tablosunda zaten company_id var.</p>";
                }
            } else {
                echo "<p style='color:red'>Hata: $table tablosu bulunamadı.</p>";
            }
        }

        echo "<h3>GÜNCELLEME TAMAMLANDI. Lütfen '/Auth/login' sayfasına gidiniz.</h3>";
        echo "<a href='" . site_url('Auth/login') . "'>Giriş Yap</a>";
    }
}
