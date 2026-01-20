<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'company_name',
        'company_code',
        'phone',
        'email',
        'address',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'company_name' => 'required|min_length[3]|max_length[150]',
    ];

    protected $validationMessages = [
        'company_name' => [
            'required' => 'Şirket adı zorunludur.',
            'min_length' => 'Şirket adı en az 3 karakter olmalıdır.'
        ]
    ];
}
