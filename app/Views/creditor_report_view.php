<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alacaklı Raporu - <?= esc($creditor['creditor_name']) ?></title>
    
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
            --purple: #9333ea;
            --purple-dark: #7c3aed;
            --danger: #ef4444;
            --success: #22c55e;
            --warning: #f59e0b;
            --dark: #1e293b;
            --gray: #64748b;
            --card-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        * { font-family: 'Outfit', sans-serif; }
        
        body {
            background: linear-gradient(135deg, #9333ea 0%, #7c3aed 50%, #6366f1 100%);
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
        
        .creditor-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--purple) 0%, var(--purple-dark) 100%);
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
        
        .stat-card:hover { transform: translateY(-5px); }
        
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
        .stat-icon.purple { background: rgba(147, 51, 234, 0.1); color: var(--purple); }
        .stat-icon.warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
        }
        
        .stat-label { font-size: 0.9rem; color: var(--gray); }
        
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
        
        .table-custom { margin: 0; }
        
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
        
        .btn-back {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover { background: white; color: var(--purple); }
        
        .badge-odenmedi { background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%); color: #991b1b; }
        .badge-odendi { background: linear-gradient(135deg, #bbf7d0 0%, #86efac 100%); color: #166534; }
        
        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #e2e8f0;
            margin-bottom: 20px;
        }
        
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .report-header, .stat-card, .content-card { box-shadow: none !important; border: 1px solid #e2e8f0; }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Üst Navigasyon -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="<?= site_url('Munu#creditors') ?>" class="btn btn-back">
                <i class="fa-solid fa-arrow-left me-2"></i>Alacaklılara Dön
            </a>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-light">
                    <i class="fa-solid fa-print me-2"></i>Yazdır
                </button>
            </div>
        </div>
        
        <!-- Alacaklı Başlık Kartı -->
        <div class="report-header">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="creditor-avatar">
                        <?= strtoupper(mb_substr($creditor['creditor_name'], 0, 1)) ?>
                    </div>
                </div>
                <div class="col">
                    <h2 class="mb-1 fw-bold"><?= esc($creditor['creditor_name']) ?></h2>
                    <div class="d-flex gap-3 text-muted">
                        <?php if (!empty($creditor['phone'])): ?>
                        <span><i class="fa-solid fa-phone me-1"></i><?= esc($creditor['phone']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($creditor['email'])): ?>
                        <span><i class="fa-solid fa-envelope me-1"></i><?= esc($creditor['email']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-auto text-end">
                    <div class="text-muted mb-1">Toplam Borç</div>
                    <h2 class="mb-0 <?= $totalDebt > 0 ? 'text-danger' : 'text-success' ?>">
                        <?= number_format($totalDebt, 2, ',', '.') ?> ₺
                    </h2>
                </div>
            </div>
            
            <?php if (!empty($creditor['notes'])): ?>
            <div class="mt-3 pt-3 border-top">
                <small class="text-muted"><i class="fa-solid fa-note-sticky me-2"></i><?= esc($creditor['notes']) ?></small>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- İstatistik Kartları -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-value text-danger"><?= number_format($totalDebt, 2, ',', '.') ?> ₺</div>
                            <div class="stat-label">Ödenmemiş Borç</div>
                        </div>
                        <div class="stat-icon danger"><i class="fa-solid fa-clock"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-value text-success"><?= number_format($totalPaid, 2, ',', '.') ?> ₺</div>
                            <div class="stat-label">Ödenen Toplam</div>
                        </div>
                        <div class="stat-icon success"><i class="fa-solid fa-check"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-value"><?= $unpaidCount ?></div>
                            <div class="stat-label">Bekleyen İşlem</div>
                        </div>
                        <div class="stat-icon warning"><i class="fa-solid fa-hourglass-half"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-value"><?= $paidCount ?></div>
                            <div class="stat-label">Tamamlanan</div>
                        </div>
                        <div class="stat-icon purple"><i class="fa-solid fa-receipt"></i></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Hızlı İşlemler & Kategori Dağılımı -->
            <div class="col-lg-4">
                <!-- Hızlı İşlemler -->
                <div class="content-card mb-4 no-print">
                    <div class="card-header-custom">
                        <h5><i class="fa-solid fa-bolt me-2"></i>Hızlı İşlemler</h5>
                    </div>
                    <div class="p-4">
                        <button class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#addDebtModal">
                            <i class="fa-solid fa-plus me-2"></i>Bu Kişiye Borç Ekle
                        </button>
                        <a href="<?= site_url('Munu#creditors') ?>" class="btn btn-outline-secondary w-100">
                            <i class="fa-solid fa-edit me-2"></i>Bilgileri Düzenle
                        </a>
                    </div>
                </div>
                
                <!-- Kategori Dağılımı -->
                <div class="content-card">
                    <div class="card-header-custom">
                        <h5><i class="fa-solid fa-chart-pie me-2"></i>Kategori Dağılımı</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($categoryStats)): ?>
                        <p class="text-muted text-center py-4 mb-0">Henüz kayıt yok</p>
                        <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($categoryStats as $cat): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fa-solid <?= esc($cat['icon']) ?> me-2" style="color: <?= esc($cat['color']) ?>"></i>
                                    <?= esc($cat['name']) ?>
                                    <small class="text-muted">(<?= $cat['count'] ?>)</small>
                                </span>
                                <span class="fw-bold" style="color: var(--purple);"><?= number_format($cat['total'], 2, ',', '.') ?> ₺</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- İşlem Geçmişi -->
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h5><i class="fa-solid fa-history me-2"></i>Tüm İşlemler</h5>
                        <span class="badge" style="background: var(--purple);"><?= count($debts) ?> kayıt</span>
                    </div>
                    
                    <?php if (empty($debts)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-inbox"></i>
                        <h5 class="text-muted">Henüz işlem yok</h5>
                        <p class="text-muted">Bu alacaklıya ait borç kaydı bulunmuyor.</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Kategori</th>
                                    <th>Açıklama</th>
                                    <th>Tutar</th>
                                    <th>Durum</th>
                                    <th class="text-end no-print">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($debts as $debt): ?>
                                <?php $isOverdue = $debt['status'] === 'odenmedi' && $debt['due_date'] && strtotime($debt['due_date']) < strtotime('today'); ?>
                                <tr class="<?= $isOverdue ? 'table-danger' : '' ?>">
                                    <td>
                                        <div class="fw-bold"><?= date('d.m.Y', strtotime($debt['created_at'])) ?></div>
                                        <?php if ($debt['due_date']): ?>
                                        <small class="text-muted">Vade: <?= date('d.m.Y', strtotime($debt['due_date'])) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2" style="background-color: <?= esc($debt['category_color'] ?? '#6b7280') ?>;">
                                            <i class="fa-solid <?= esc($debt['category_icon'] ?? 'fa-tag') ?> me-1"></i>
                                            <?= esc($debt['category_name'] ?? 'Diğer') ?>
                                        </span>
                                    </td>
                                    <td><?= esc($debt['description'] ?: '-') ?></td>
                                    <td class="fw-bold" style="color: var(--purple);">
                                        <?= number_format($debt['amount'], 2, ',', '.') ?> ₺
                                    </td>
                                    <td>
                                        <?php if ($debt['status'] === 'odenmedi'): ?>
                                            <?php if ($isOverdue): ?>
                                            <span class="badge bg-danger"><i class="fa-solid fa-exclamation-triangle me-1"></i>Gecikmiş</span>
                                            <?php else: ?>
                                            <span class="badge badge-odenmedi px-3 py-2">Ödenmedi</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-odendi px-3 py-2">
                                                <i class="fa-solid fa-check me-1"></i>Ödendi
                                            </span>
                                            <?php if ($debt['paid_date']): ?>
                                            <br><small class="text-muted"><?= date('d.m.Y', strtotime($debt['paid_date'])) ?></small>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end no-print">
                                        <?php if ($debt['status'] === 'odenmedi'): ?>
                                        <a href="<?= site_url('Munu/pay_my_debt/' . $debt['id']) ?>?redirect=<?= urlencode(current_url()) ?>" 
                                           class="btn btn-sm btn-success"
                                           onclick="return confirm('Bu borcu ödenmiş olarak işaretlemek istiyor musunuz?')">
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                        <?php endif; ?>
                                        <a href="<?= site_url('Munu/delete_my_debt/' . $debt['id']) ?>?redirect=<?= urlencode(current_url()) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Bu kaydı silmek istediğinize emin misiniz?')">
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
            </div>
        </div>
        
        <!-- Rapor Footer -->
        <div class="text-center text-white mt-4 opacity-75">
            <small>
                <i class="fa-solid fa-clock me-1"></i>
                Rapor Tarihi: <?= date('d.m.Y H:i') ?>
            </small>
        </div>
    </div>
    
    <!-- Borç Ekleme Modal -->
    <div class="modal fade" id="addDebtModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i><?= esc($creditor['creditor_name']) ?> - Borç Ekle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= site_url('Munu/add_my_debt') ?>" method="post">
                    <input type="hidden" name="creditor_id" value="<?= $creditor['id'] ?>">
                    <input type="hidden" name="expense_type" value="borc">
                    <input type="hidden" name="redirect_to" value="<?= current_url() ?>">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Kategori Seçin...</option>
                                <?php foreach ($expense_categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= esc($cat['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tutar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control form-control-lg" step="0.01" min="0.01" required>
                                <span class="input-group-text">₺</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Son Ödeme Tarihi</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Açıklama</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-save me-2"></i>Borç Ekle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

