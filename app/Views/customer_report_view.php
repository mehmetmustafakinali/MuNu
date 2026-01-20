<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Raporu - <?= esc($customer['customer_name']) ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --danger: #ef4444;
            --success: #22c55e;
            --warning: #f59e0b;
            --dark: #1e293b;
            --gray: #64748b;
            --light: #f8fafc;
            --card-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        * {
            font-family: 'Outfit', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 0;
        }
        
        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .report-header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
        }
        
        .customer-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 600;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            height: 100%;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        
        .stat-icon.danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .stat-icon.success { background: rgba(34, 197, 94, 0.1); color: var(--success); }
        .stat-icon.primary { background: rgba(14, 165, 233, 0.1); color: var(--primary); }
        .stat-icon.warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .content-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 20px 25px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .card-header-custom h5 {
            margin: 0;
            font-weight: 600;
            color: var(--dark);
        }
        
        .table-custom {
            margin: 0;
        }
        
        .table-custom th {
            background: #f8fafc;
            color: var(--gray);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border: none;
            padding: 15px 20px;
        }
        
        .table-custom td {
            padding: 15px 20px;
            vertical-align: middle;
            border-color: #f1f5f9;
        }
        
        .badge-borc {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            color: #991b1b;
            font-weight: 500;
        }
        
        .badge-tahsilat {
            background: linear-gradient(135deg, #bbf7d0 0%, #86efac 100%);
            color: #166534;
            font-weight: 500;
        }
        
        .btn-back {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: white;
            color: #764ba2;
        }
        
        .btn-action {
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .chart-container {
            padding: 25px;
            height: 300px;
        }
        
        .customer-info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
        }
        
        .customer-info-item i {
            width: 20px;
            color: var(--gray);
        }
        
        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #e2e8f0;
            margin-bottom: 20px;
        }
        
        .print-btn {
            background: white;
            color: var(--dark);
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 500;
        }
        
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .report-header, .stat-card, .content-card {
                box-shadow: none !important;
                border: 1px solid #e2e8f0;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Üst Navigasyon -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="<?= site_url('Munu#customers') ?>" class="btn btn-back">
                <i class="fa-solid fa-arrow-left me-2"></i>Müşterilere Dön
            </a>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="print-btn">
                    <i class="fa-solid fa-print me-2"></i>Yazdır
                </button>
            </div>
        </div>
        
        <!-- Müşteri Başlık Kartı -->
        <div class="report-header">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="customer-avatar">
                        <?= strtoupper(substr($customer['customer_name'], 0, 1)) ?>
                    </div>
                </div>
                <div class="col">
                    <h2 class="mb-1 fw-bold"><?= esc($customer['customer_name']) ?></h2>
                    <span class="badge bg-<?= $customer['customer_type'] == 'kurumsal' ? 'dark' : 'secondary' ?> me-2">
                        <?= $customer['customer_type'] == 'kurumsal' ? 'Kurumsal' : 'Bireysel' ?>
                    </span>
                    <span class="badge bg-<?= $customer['status'] == 'aktif' ? 'success' : 'warning' ?>">
                        <?= $customer['status'] == 'aktif' ? 'Aktif' : 'Pasif' ?>
                    </span>
                </div>
                <div class="col-auto text-end">
                    <div class="text-muted mb-1">Güncel Bakiye</div>
                    <h2 class="mb-0 <?= $customer['balance'] > 0 ? 'text-danger' : 'text-success' ?>">
                        <?= number_format($customer['balance'], 2, ',', '.') ?> ₺
                    </h2>
                </div>
            </div>
            
            <!-- Müşteri Bilgileri -->
            <div class="row mt-4 pt-4 border-top">
                <div class="col-md-4">
                    <div class="customer-info-item">
                        <i class="fa-solid fa-phone"></i>
                        <span><?= esc($customer['phone'] ?: 'Telefon yok') ?></span>
                    </div>
                    <div class="customer-info-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span><?= esc($customer['email'] ?: 'E-posta yok') ?></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="customer-info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span><?= esc($customer['city'] ?: 'Şehir belirtilmemiş') ?></span>
                    </div>
                    <div class="customer-info-item">
                        <i class="fa-solid fa-map"></i>
                        <span><?= esc($customer['address'] ?: 'Adres yok') ?></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <?php if ($customer['customer_type'] == 'kurumsal'): ?>
                    <div class="customer-info-item">
                        <i class="fa-solid fa-building"></i>
                        <span>VD: <?= esc($customer['tax_office'] ?: '-') ?></span>
                    </div>
                    <div class="customer-info-item">
                        <i class="fa-solid fa-hashtag"></i>
                        <span>VN: <?= esc($customer['tax_number'] ?: '-') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- İstatistik Kartları -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-value text-danger"><?= number_format($totalBorc, 2, ',', '.') ?> ₺</div>
                            <div class="stat-label">Toplam Borç</div>
                        </div>
                        <div class="stat-icon danger">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-value text-success"><?= number_format($totalTahsilat, 2, ',', '.') ?> ₺</div>
                            <div class="stat-label">Toplam Tahsilat</div>
                        </div>
                        <div class="stat-icon success">
                            <i class="fa-solid fa-arrow-trend-down"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-value"><?= $transactionCount ?></div>
                            <div class="stat-label">Toplam İşlem</div>
                        </div>
                        <div class="stat-icon primary">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-value <?= $customer['balance'] > 0 ? 'text-danger' : 'text-success' ?>">
                                <?= number_format($customer['balance'], 2, ',', '.') ?> ₺
                            </div>
                            <div class="stat-label">Kalan Bakiye</div>
                        </div>
                        <div class="stat-icon <?= $customer['balance'] > 0 ? 'danger' : 'success' ?>">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Grafik -->
            <div class="col-lg-6">
                <div class="content-card h-100">
                    <div class="card-header-custom">
                        <h5><i class="fa-solid fa-chart-bar me-2"></i>Aylık İşlem Grafiği</h5>
                    </div>
                    <div class="chart-container">
                        <?php if (!empty($monthlyStats)): ?>
                        <canvas id="monthlyChart"></canvas>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-chart-pie"></i>
                            <p class="text-muted">Grafik için yeterli veri yok</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Hızlı İşlemler -->
            <div class="col-lg-6">
                <div class="content-card h-100">
                    <div class="card-header-custom">
                        <h5><i class="fa-solid fa-bolt me-2"></i>Hızlı İşlemler</h5>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <button class="btn btn-danger btn-lg w-100 btn-action" 
                                        onclick="openQuickTransaction('borc')">
                                    <i class="fa-solid fa-minus me-2"></i>Borç Ekle
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-success btn-lg w-100 btn-action" 
                                        onclick="openQuickTransaction('tahsilat')"
                                        <?= $customer['balance'] <= 0 ? 'disabled' : '' ?>>
                                    <i class="fa-solid fa-plus me-2"></i>Tahsilat Al
                                </button>
                            </div>
                            <div class="col-6">
                                <a href="<?= site_url('Munu#customers') ?>" class="btn btn-outline-primary btn-lg w-100 btn-action">
                                    <i class="fa-solid fa-edit me-2"></i>Düzenle
                                </a>
                            </div>
                            <div class="col-6">
                                <button onclick="window.print()" class="btn btn-outline-secondary btn-lg w-100 btn-action">
                                    <i class="fa-solid fa-file-pdf me-2"></i>PDF Al
                                </button>
                            </div>
                        </div>
                        
                        <!-- Notlar -->
                        <?php if (!empty($customer['notes'])): ?>
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="fw-bold mb-2"><i class="fa-solid fa-sticky-note me-2"></i>Notlar</h6>
                            <p class="mb-0 text-muted"><?= nl2br(esc($customer['notes'])) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- İşlem Geçmişi Tablosu -->
        <div class="content-card mt-4">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h5><i class="fa-solid fa-history me-2"></i>İşlem Geçmişi</h5>
                <span class="badge bg-primary"><?= $transactionCount ?> işlem</span>
            </div>
            
            <?php if (empty($transactions)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-inbox"></i>
                <h5 class="text-muted">Henüz işlem yapılmamış</h5>
                <p class="text-muted">Bu müşteri için borç veya tahsilat kaydı bulunmuyor.</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>İşlem Türü</th>
                            <th>Tutar</th>
                            <th>Açıklama</th>
                            <th class="text-end no-print">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $runningBalance = 0;
                        foreach ($transactions as $t): 
                            if ($t['transaction_type'] === 'borc') {
                                $runningBalance += (float) $t['amount'];
                            } else {
                                $runningBalance -= (float) $t['amount'];
                            }
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= date('d.m.Y', strtotime($t['transaction_date'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($t['created_at'])) ?></small>
                            </td>
                            <td>
                                <span class="badge badge-<?= $t['transaction_type'] ?> px-3 py-2">
                                    <i class="fa-solid fa-<?= $t['transaction_type'] == 'borc' ? 'arrow-up' : 'arrow-down' ?> me-1"></i>
                                    <?= $t['transaction_type'] == 'borc' ? 'Borç' : 'Tahsilat' ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold <?= $t['transaction_type'] == 'borc' ? 'text-danger' : 'text-success' ?>">
                                    <?= $t['transaction_type'] == 'borc' ? '+' : '-' ?>
                                    <?= number_format($t['amount'], 2, ',', '.') ?> ₺
                                </span>
                            </td>
                            <td>
                                <?= esc($t['description'] ?: '-') ?>
                            </td>
                            <td class="text-end no-print">
                                <a href="<?= site_url('Munu/delete_transaction/' . $t['id']) ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Bu işlemi silmek istediğinize emin misiniz?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Rapor Footer -->
        <div class="text-center text-white mt-4 opacity-75">
            <small>
                <i class="fa-solid fa-clock me-1"></i>
                Rapor Tarihi: <?= date('d.m.Y H:i') ?> | 
                Kayıt Tarihi: <?= date('d.m.Y', strtotime($customer['created_at'])) ?>
            </small>
        </div>
    </div>
    
    <!-- Hızlı İşlem Modal -->
    <div class="modal fade" id="quickTransactionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header" id="quickModalHeader">
                    <h5 class="modal-title" id="quickModalTitle">İşlem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= site_url('Munu/add_transaction') ?>" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
                        <input type="hidden" name="transaction_type" id="quick_transaction_type">
                        <input type="hidden" name="redirect_to" value="<?= site_url('Munu/customer_report/' . $customer['id']) ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Müşteri</label>
                            <input type="text" class="form-control" value="<?= esc($customer['customer_name']) ?>" readonly style="background: #f8fafc;">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tutar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control form-control-lg" 
                                       step="0.01" min="0.01" required placeholder="0,00">
                                <span class="input-group-text">₺</span>
                            </div>
                            <div id="maxAmountHint" class="form-text text-success" style="display: none;">
                                Maks. tahsilat: <?= number_format($customer['balance'], 2, ',', '.') ?> ₺
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tarih</label>
                            <input type="date" name="transaction_date" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Açıklama</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Opsiyonel not..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn" id="quickSubmitBtn">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Hızlı işlem modal
        function openQuickTransaction(type) {
            const modal = new bootstrap.Modal(document.getElementById('quickTransactionModal'));
            const header = document.getElementById('quickModalHeader');
            const title = document.getElementById('quickModalTitle');
            const submitBtn = document.getElementById('quickSubmitBtn');
            const typeInput = document.getElementById('quick_transaction_type');
            const maxHint = document.getElementById('maxAmountHint');
            
            typeInput.value = type;
            
            if (type === 'borc') {
                header.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
                header.style.color = 'white';
                title.innerHTML = '<i class="fa-solid fa-minus me-2"></i>Borç Ekle';
                submitBtn.className = 'btn btn-danger';
                submitBtn.innerHTML = '<i class="fa-solid fa-plus me-2"></i>Borç Ekle';
                maxHint.style.display = 'none';
            } else {
                header.style.background = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
                header.style.color = 'white';
                title.innerHTML = '<i class="fa-solid fa-plus me-2"></i>Tahsilat Al';
                submitBtn.className = 'btn btn-success';
                submitBtn.innerHTML = '<i class="fa-solid fa-check me-2"></i>Tahsilat Al';
                maxHint.style.display = 'block';
            }
            
            modal.show();
        }
        
        // Aylık grafik
        <?php if (!empty($monthlyStats)): ?>
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($monthlyStats, 'month')) ?>,
                datasets: [
                    {
                        label: 'Borç',
                        data: <?= json_encode(array_column($monthlyStats, 'borc')) ?>,
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderRadius: 6
                    },
                    {
                        label: 'Tahsilat',
                        data: <?= json_encode(array_column($monthlyStats, 'tahsilat')) ?>,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('tr-TR') + ' ₺';
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>

