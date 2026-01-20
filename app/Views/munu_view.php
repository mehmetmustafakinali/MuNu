<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Munu | Akıllı Veresiye & Ön Muhasebe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-color: #6366f1;
            --purple-color: #9333ea;
            --sidebar-width: 260px;
            --bg-light: #f8fafc;
            --text-dark: #1e293b;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #0f172a;
            color: #94a3b8;
            padding-top: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #1e293b;
            overflow-y: auto;
        }

        .sidebar-brand-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            padding: 20px 10px;
            border-bottom: 1px solid #1e293b;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            padding: 14px 25px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #1e293b;
            color: #fff;
            border-right: 3px solid var(--primary-color);
        }



        .sidebar .nav-link i {
            width: 25px;
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: 100vh;
        }

        .section-view {
            display: none;
            animation: fadeIn 0.4s ease-in-out;
        }

        .section-view.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            background: #fff;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            color: #64748b;
            background-color: #f1f5f9;
            border-bottom: none;
        }

        .logout-area {
            margin-top: auto;
            padding: 20px;
            border-top: 1px solid #1e293b;
        }

        .brand-footer {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 900;
            color: rgba(255, 255, 255, 0.1);
            letter-spacing: 5px;
            margin-bottom: 10px;
        }

        .sidebar-logo-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e90ff 0%, #00bfff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
            overflow: hidden;
        }

        .sidebar-logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .typing-dots {
            display: flex;
            gap: 4px;
            padding: 5px 0;
        }

        .typing-dots .dot {
            width: 8px;
            height: 8px;
            background: #6366f1;
            border-radius: 50%;
            animation: typingBounce 1.4s infinite ease-in-out both;
        }

        .typing-dots .dot:nth-child(1) {
            animation-delay: -0.32s;
        }

        .typing-dots .dot:nth-child(2) {
            animation-delay: -0.16s;
        }

        .typing-dots .dot:nth-child(3) {
            animation-delay: 0;
        }

        @keyframes typingBounce {

            0%,
            80%,
            100% {
                transform: scale(0.6);
                opacity: 0.5;
            }

            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .text-purple {
            color: var(--purple-color) !important;
        }

        .alert-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: 0.3s;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <?php
    // Controller'dan gelen veriler
    $customers = $customers ?? [];
    $transactions = $transactions ?? [];
    $products = $products ?? [];
    $my_debts = $my_debts ?? [];
    $users = $users ?? [];

    $total_receivables = $total_receivables ?? 0;
    $total_my_debts = $total_my_debts ?? 0;
    $pending_notes = $pending_notes ?? 0;
    $customer_count = $customer_count ?? 0;
    $top_debtors = $top_debtors ?? [];
    $recent_transactions = $recent_transactions ?? [];
    $overdue_debts = $overdue_debts ?? 0;
    $monthly_data = $monthly_data ?? [];
    ?>

    <!-- Flash Mesajları -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-floating alert-dismissible fade show">
            <i class="fa-solid fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-floating alert-dismissible fade show">
            <i class="fa-solid fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand-container">
            <div class="text-center">
                <div class="sidebar-logo-wrapper mb-2">
                    <img src="<?= base_url('public/assets/img/sidebar_logo.png') ?>" alt="MUNU Logo"
                        class="sidebar-logo">
                </div>
                <h5 class="text-white m-0"><?= esc(session()->get('company_name')) ?: 'MUNU' ?></h5>
                <small class="text-muted">Ön Muhasebe</small>
            </div>
        </div>

        <a href="#" class="nav-link active" onclick="nav('dashboard', this)">
            <i class="fa-solid fa-house"></i> Panel
        </a>
        <a href="#" class="nav-link" onclick="nav('customers', this)">
            <i class="fa-solid fa-users"></i> Müşteriler
        </a>
        <a href="#" class="nav-link" onclick="nav('mydebts', this)">
            <i class="fa-solid fa-hand-holding-dollar"></i> Borçlarım
            <?php if ($overdue_debts > 0): ?>
                <span class="badge bg-danger ms-auto"><?= $overdue_debts ?></span>
            <?php endif; ?>
        </a>
        <a href="#" class="nav-link" onclick="nav('creditors', this)">
            <i class="fa-solid fa-address-book"></i> Alacaklılar
            </a>
        <a href="#" class="nav-link" onclick="nav('transactions', this)">
            <i class="fa-solid fa-money-bill-transfer"></i> İşlemler
        </a>
        <a href="#" class="nav-link" onclick="nav('notes', this)">
            <i class="fa-solid fa-note-sticky"></i> Karalama Defteri
            <?php if (($note_stats['overdue'] ?? 0) > 0): ?>
                    <span class="badge bg-danger ms-auto"><?= $note_stats['overdue'] ?></span>
            <?php elseif (($note_stats['today'] ?? 0) > 0): ?>
                    <span class="badge bg-warning ms-auto"><?= $note_stats['today'] ?></span>
            <?php endif; ?>
        </a>

        <div class="mt-4 mb-2 ps-4 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem;">Yönetim</div>
        <a href="#" class="nav-link" onclick="nav('settings', this)">
            <i class="fa-solid fa-gear"></i> Ayarlar
        </a>

        <div class="logout-area">
            <div class="brand-footer">MUNU</div>
            <a href="<?= site_url('Auth/logout') ?>" class="btn btn-outline-danger w-100">
                <i class="fa-solid fa-right-from-bracket me-2"></i>Çıkış Yap
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <button class="btn btn-light d-md-none me-3"
                    onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <h4 id="pageTitle" class="fw-bold m-0">Genel Bakış</h4>
                    <small class="text-muted"><?= date('d F Y, l') ?></small>
                </div>
            </div>
        </div>

        <!-- ============================================= -->
        <!-- DASHBOARD -->
        <!-- ============================================= -->
        <div id="view-dashboard" class="section-view active">
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card h-100 p-4 border-start border-4 border-primary">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase fw-bold mb-1">Toplam Alacak</p>
                                <h4 class="fw-bold text-primary mb-0">
                                    <?= number_format($total_receivables, 2, ',', '.') ?> ₺
                                </h4>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                                <i class="fa-solid fa-wallet fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 p-4 border-start border-4"
                        style="border-color: var(--purple-color) !important;">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase fw-bold mb-1">Toplam Borcum</p>
                                <h4 class="fw-bold text-purple mb-0"><?= number_format($total_my_debts, 2, ',', '.') ?>
                                    ₺</h4>
                            </div>
                            <div class="p-3 rounded-circle text-purple" style="background-color: #f3e8ff;">
                                <i class="fa-solid fa-hand-holding-dollar fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 p-4 border-start border-4 border-warning">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase fw-bold mb-1">Bekleyen Not</p>
                                <h4 class="fw-bold text-warning mb-0"><?= $pending_notes ?> Adet</h4>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                                <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 p-4 border-start border-4 border-success">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase fw-bold mb-1">Aktif Müşteri</p>
                                <h4 class="fw-bold text-success mb-0"><?= $customer_count ?></h4>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                                <i class="fa-solid fa-users fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card p-4">
                        <h6 class="fw-bold mb-4">Aylık İşlem Grafiği</h6>
                        <canvas id="mainChart" height="120"></canvas>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-4 h-100">
                        <h6 class="fw-bold mb-3">Son İşlemler</h6>
                        <div class="list-group list-group-flush">
                            <?php if (empty($recent_transactions)): ?>
                                    <div class="text-muted text-center py-3">Henüz işlem yok.</div>
                            <?php else: ?>
                                    <?php foreach ($recent_transactions as $t): ?>
                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center border-0 border-bottom px-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <?php if ($t['transaction_type'] == 'borc'): ?>
                                                                <i class="fa-solid fa-arrow-up text-danger"></i>
                                                        <?php else: ?>
                                                                <i class="fa-solid fa-arrow-down text-success"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= esc($t['customer_name']) ?></div>
                                                        <div class="small text-muted"><?= esc($t['description'] ?: '-') ?></div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="fw-bold <?= $t['transaction_type'] == 'borc' ? 'text-danger' : 'text-success' ?>">
                                                    <?= number_format($t['amount'], 2, ',', '.') ?>₺
                                                </div>
                                            </div>
                                    <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card p-4">
                        <h6 class="fw-bold mb-3 text-danger"><i class="fa-solid fa-fire me-2"></i>En Borçlu 5 Müşteri
                        </h6>
                        <ul class="list-group list-group-flush">
                            <?php if (empty($top_debtors)): ?>
                                    <li class="list-group-item text-muted">Borçlu müşteri yok.</li>
                            <?php else: ?>
                                    <?php foreach ($top_debtors as $index => $debtor): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-danger rounded-pill me-3"><?= $index + 1 ?></span>
                                                    <div>
                                                        <h6 class="m-0 fw-bold"><?= esc($debtor['customer_name']) ?></h6>
                                                        <small class="text-muted"><?= esc($debtor['phone'] ?: 'Telefon yok') ?></small>
                                                    </div>
                                                </div>
                                                <span
                                                    class="fs-5 fw-bold text-danger"><?= number_format($debtor['balance'], 2, ',', '.') ?>
                                                    ₺</span>
                                            </li>
                                    <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- AI Insights & Chat Panel -->
                <div class="col-md-6">
                    <div class="card h-100"
                        style="border-radius: 16px; overflow: hidden; background: linear-gradient(145deg, #1a1f2e 0%, #0f1318 100%); border: 1px solid rgba(99, 102, 241, 0.3);">
                        <!-- Header -->
                        <div class="card-header border-0 pb-0" style="background: transparent;">
                            <div class="d-flex align-items-center">
                                <div class="ai-icon-wrapper me-3"
                                    style="width: 45px; height: 45px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-brain text-white fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-white">MuNu AI Asistan</h6>
                                    <small class="text-muted">Akıllı finansal öneriler</small>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-success"><i
                                            class="fa-solid fa-circle fa-xs me-1"></i>Aktif</span>
                                </div>
                            </div>
                        </div>

                        <!-- AI Insights -->
                        <div class="card-body pt-3" style="max-height: 280px; overflow-y: auto;">
                            <!-- Vadesi Geçmiş Borç Uyarısı -->
                            <?php if ($overdue_debts > 0): ?>
                                    <div class="ai-insight-item d-flex align-items-start mb-3 p-3 rounded-3"
                                        style="background: rgba(239, 68, 68, 0.1); border-left: 3px solid #ef4444;">
                                        <div class="insight-icon me-3">
                                            <div
                                                style="width: 35px; height: 35px; background: rgba(239, 68, 68, 0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-white fw-bold small">Vadesi Geçmiş Borç!</h6>
                                            <p class="mb-0 text-muted small"><?= $overdue_debts ?> adet borcunuzun vadesi
                                                geçmiş. Hemen ödeme planı yapın.</p>
                                        </div>
                                    </div>
                            <?php endif; ?>

                            <!-- Tahsilat Önerisi -->
                            <?php if (!empty($top_debtors) && isset($top_debtors[0])): ?>
                                    <div class="ai-insight-item d-flex align-items-start mb-3 p-3 rounded-3"
                                        style="background: rgba(34, 197, 94, 0.1); border-left: 3px solid #22c55e;">
                                        <div class="insight-icon me-3">
                                            <div
                                                style="width: 35px; height: 35px; background: rgba(34, 197, 94, 0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-hand-holding-dollar text-success"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-white fw-bold small">Tahsilat Önerisi</h6>
                                            <p class="mb-0 text-muted small"><?= esc($top_debtors[0]['customer_name']) ?>'dan
                                                <?= number_format($top_debtors[0]['balance'], 0, ',', '.') ?>₺ alacağınız var.
                                                İletişime geçin.
                                            </p>
                                        </div>
                                    </div>
                            <?php endif; ?>

                            <!-- Genel Durum -->
                            <div class="ai-insight-item d-flex align-items-start mb-3 p-3 rounded-3"
                                style="background: rgba(99, 102, 241, 0.1); border-left: 3px solid #6366f1;">
                                <div class="insight-icon me-3">
                                    <div
                                        style="width: 35px; height: 35px; background: rgba(99, 102, 241, 0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-chart-line text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-white fw-bold small">Finansal Özet</h6>
                                    <p class="mb-0 text-muted small">Toplam
                                        <?= number_format($total_receivables, 0, ',', '.') ?>₺ alacak,
                                        <?= number_format($total_my_debts, 0, ',', '.') ?>₺ borç. Net:
                                        <?= number_format($total_receivables - $total_my_debts, 0, ',', '.') ?>₺
                                    </p>
                                </div>
                            </div>

                            <!-- Hatırlatma -->
                            <?php if ($pending_notes > 0): ?>
                                    <div class="ai-insight-item d-flex align-items-start mb-3 p-3 rounded-3"
                                        style="background: rgba(251, 191, 36, 0.1); border-left: 3px solid #fbbf24;">
                                        <div class="insight-icon me-3">
                                            <div
                                                style="width: 35px; height: 35px; background: rgba(251, 191, 36, 0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-bell text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-white fw-bold small">Bekleyen Hatırlatma</h6>
                                            <p class="mb-0 text-muted small"><?= $pending_notes ?> adet bekleyen notunuz var.
                                                Kontrol edin!</p>
                                        </div>
                                    </div>
                            <?php endif; ?>
                        </div>

                        <!-- Action Buttons & Chat -->
                        <div class="card-footer border-0 pt-0" style="background: transparent;">
                            <div class="d-flex gap-2 mb-3">
                                <button class="btn btn-sm flex-grow-1"
                                    style="background: rgba(99, 102, 241, 0.2); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.3);"
                                    onclick="openAiChat()">
                                    <i class="fa-solid fa-comments me-1"></i> Asistanla Konuş
                                </button>
                                <button class="btn btn-sm flex-grow-1"
                                    style="background: rgba(34, 197, 94, 0.2); color: #86efac; border: 1px solid rgba(34, 197, 94, 0.3);"
                                    onclick="requestDetailedAnalysis()">
                                    <i class="fa-solid fa-magnifying-glass-chart me-1"></i> Detaylı Analiz
                                </button>
                            </div>
                            <div class="input-group input-group-sm">
                                <input type="text" id="dashboardAiInput" class="form-control border-0"
                                    placeholder="Hızlı soru sorun..."
                                    style="background: rgba(255,255,255,0.1); color: white;"
                                    onkeypress="if(event.key==='Enter') sendDashboardAiMessage()">
                                <button class="btn px-3"
                                    style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white;"
                                    onclick="sendDashboardAiMessage()">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================= -->
        <!-- MÜŞTERİLER -->
        <!-- ============================================= -->
        <div id="view-customers" class="section-view">
            <div class="d-flex justify-content-between mb-4">
                <div class="input-group w-50">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="customerSearch"
                        placeholder="Müşteri adı veya telefon ile ara...">
                </div>
                <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#modalCustomer">
                    <i class="fa-solid fa-plus me-2"></i>Müşteri Ekle
                </button>
            </div>

            <div class="card overflow-hidden">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Müşteri</th>
                            <th>Telefon</th>
                            <th>Tür</th>
                            <th>Bakiye</th>
                            <th class="text-end pe-4">İşlem</th>
                        </tr>
                    </thead>
                    <tbody id="customerTableBody">
                        <?php if (empty($customers)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Henüz kayıtlı müşteri yok.</td>
                                </tr>
                        <?php else: ?>
                                <?php foreach ($customers as $cust): ?>
                                        <tr class="customer-row" data-name="<?= strtolower(esc($cust['customer_name'])) ?>"
                                            data-phone="<?= esc($cust['phone']) ?>">
                                            <td class="ps-4">
                                                <div class="fw-bold text-primary"><?= esc($cust['customer_name']) ?></div>
                                                <?php if (!empty($cust['city'])): ?>
                                                        <small class="text-muted"><?= esc($cust['city']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($cust['phone'] ?: '-') ?></td>
                                            <td>
                                                <span
                                                    class="badge bg-<?= $cust['customer_type'] == 'kurumsal' ? 'dark' : 'secondary' ?>">
                                                    <?= $cust['customer_type'] == 'kurumsal' ? 'Kurumsal' : 'Bireysel' ?>
                                                </span>
                                            </td>
                                            <td class="<?= $cust['balance'] > 0 ? 'text-danger fw-bold' : 'text-success' ?>">
                                                <?= number_format($cust['balance'], 2, ',', '.') ?> ₺
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="<?= site_url('Munu/customer_report/' . $cust['id']) ?>"
                                                    class="btn btn-sm btn-outline-primary me-1" title="Müşteri Raporu">
                                                    <i class="fa-solid fa-file-lines"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-danger me-1"
                                                    onclick="openTransactionModal(<?= $cust['id'] ?>, '<?= esc($cust['customer_name']) ?>', 'borc')"
                                                    title="Borç Ekle">
                                                    <i class="fa-solid fa-minus"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success me-1"
                                                    onclick="openTransactionModal(<?= $cust['id'] ?>, '<?= esc($cust['customer_name']) ?>', 'tahsilat')"
                                                    title="Tahsilat Al">
                                                    <i class="fa-solid fa-plus"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary me-1"
                                                    onclick="openEditCustomerModal(<?= $cust['id'] ?>, '<?= esc($cust['customer_name']) ?>', '<?= esc($cust['phone']) ?>', '<?= esc($cust['email']) ?>', '<?= esc($cust['address']) ?>', '<?= esc($cust['city']) ?>', '<?= esc($cust['customer_type']) ?>', '<?= esc($cust['notes']) ?>')"
                                                    title="Düzenle">
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>
                                                <a href="<?= site_url('Munu/delete_customer/' . $cust['id']) ?>"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    onclick="return confirm('Bu müşteriyi silmek istediğinize emin misiniz?')"
                                                    title="Sil">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============================================= -->
        <!-- BORÇLARIM -->
        <!-- ============================================= -->
        <div id="view-mydebts" class="section-view">
            <!-- Üst Bilgi Kartları -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 h-100"
                        style="background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 opacity-75">Toplam Borç</h6>
                                    <h3 class="mb-0 fw-bold"><?= number_format($total_my_debts ?? 0, 2, ',', '.') ?> ₺
                                    </h3>
                                </div>
                                <i class="fa-solid fa-wallet fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 h-100"
                        style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 opacity-75">Gecikmiş</h6>
                                    <h3 class="mb-0 fw-bold"><?= $overdue_debts ?? 0 ?> adet</h3>
                                </div>
                                <i class="fa-solid fa-clock fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 h-100"
                        style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 opacity-75">Alacaklı Sayısı</h6>
                                    <h3 class="mb-0 fw-bold"><?= count($creditors ?? []) ?></h3>
                                </div>
                                <i class="fa-solid fa-users fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aksiyon Butonları -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0" style="color: #9333ea;">
                    <i class="fa-solid fa-file-invoice-dollar me-2"></i>Borçlarım & Giderlerim
                </h5>
                <div class="btn-group">
                    <button class="btn" style="background-color: #9333ea; color: white;" data-bs-toggle="modal"
                        data-bs-target="#modalMyDebt">
                        <i class="fa-solid fa-plus me-2"></i>Borç/Gider Ekle
                    </button>
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalCreditor">
                        <i class="fa-solid fa-user-plus me-2"></i>Alacaklı Ekle
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Sol: Borç Listesi -->
                <div class="col-lg-8">
                    <div class="card overflow-hidden">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="fa-solid fa-list me-2"></i>Ödenmemiş Borçlar</h6>
                            <span class="badge bg-danger"><?= count($my_debts ?? []) ?> kayıt</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Kategori</th>
                                        <th>Alacaklı</th>
                                        <th>Açıklama</th>
                                        <th>Tutar</th>
                                        <th>Son Ödeme</th>
                                        <th class="text-end pe-4">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($my_debts)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="fa-solid fa-party-horn fa-2x mb-2 d-block"></i>
                                                    Harika! Hiç borcunuz yok. 🎉
                                                </td>
                                            </tr>
                                    <?php else: ?>
                                            <?php foreach ($my_debts as $debt): ?>
                                                    <?php $isOverdue = $debt['due_date'] && strtotime($debt['due_date']) < strtotime('today'); ?>
                                                    <tr class="<?= $isOverdue ? 'table-danger' : '' ?>">
                                                        <td class="ps-4">
                                                            <?php if (!empty($debt['category_icon'])): ?>
                                                                    <span class="badge rounded-pill px-3 py-2"
                                                                        style="background-color: <?= esc($debt['category_color'] ?? '#6b7280') ?>;">
                                                                        <i class="fa-solid <?= esc($debt['category_icon']) ?> me-1"></i>
                                                                        <?= esc($debt['category_name'] ?? 'Diğer') ?>
                                                                    </span>
                                                            <?php else: ?>
                                                                    <span class="badge bg-secondary">Diğer</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="fw-bold">
                                                            <?= esc($debt['creditor_display_name'] ?? $debt['creditor_name'] ?? '-') ?>
                                                        </td>
                                                        <td class="text-muted"><?= esc($debt['description'] ?: '-') ?></td>
                                                        <td class="fw-bold" style="color: #9333ea;">
                                                            <?= number_format($debt['amount'], 2, ',', '.') ?> ₺
                                                        </td>
                                                        <td>
                                                            <?php if ($debt['due_date']): ?>
                                                                    <?php if ($isOverdue): ?>
                                                                            <span class="badge bg-danger">
                                                                                <i class="fa-solid fa-exclamation-triangle me-1"></i>
                                                                                <?= date('d.m.Y', strtotime($debt['due_date'])) ?>
                                                                            </span>
                                                                    <?php else: ?>
                                                                            <span
                                                                                class="text-muted"><?= date('d.m.Y', strtotime($debt['due_date'])) ?></span>
                                                                    <?php endif; ?>
                                                            <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-end pe-4">
                                                            <a href="<?= site_url('Munu/pay_my_debt/' . $debt['id']) ?>"
                                                                class="btn btn-sm btn-success"
                                                                onclick="return confirm('Bu borcu ödenmiş olarak işaretlemek istiyor musunuz?')"
                                                                title="Ödedim">
                                                                <i class="fa-solid fa-check"></i>
                                                            </a>
                                                            <a href="<?= site_url('Munu/delete_my_debt/' . $debt['id']) ?>"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Bu kaydı silmek istediğinize emin misiniz?')"
                                                                title="Sil">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                            <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sağ: Özet Panelleri -->
                <div class="col-lg-4">
                    <!-- Kategori Özeti -->
                    <div class="card mb-3">
                        <div class="card-header bg-white">
                            <h6 class="mb-0 fw-bold"><i class="fa-solid fa-chart-pie me-2"></i>Kategori Bazlı</h6>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($category_summary)): ?>
                                    <p class="text-muted text-center py-3 mb-0">Henüz kayıt yok</p>
                            <?php else: ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($category_summary as $cat): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fa-solid <?= esc($cat['icon'] ?? 'fa-tag') ?> me-2"
                                                            style="color: <?= esc($cat['color'] ?? '#6b7280') ?>"></i>
                                                        <?= esc($cat['category_name'] ?? 'Diğer') ?>
                                                        <small class="text-muted">(<?= $cat['count'] ?>)</small>
                                                    </span>
                                                    <span class="fw-bold"
                                                        style="color: #9333ea;"><?= number_format($cat['total'] ?? 0, 2, ',', '.') ?>
                                                        ₺</span>
                                                </li>
                                        <?php endforeach; ?>
                                    </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Alacaklı Özeti -->
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="fa-solid fa-users me-2"></i>Alacaklı Bazlı</h6>
                            <a href="#creditors" class="btn btn-sm btn-outline-secondary"
                                onclick="showSection('creditors')">
                                Tümünü Gör
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($creditor_summary)): ?>
                                    <p class="text-muted text-center py-3 mb-0">Henüz kayıt yok</p>
                            <?php else: ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($creditor_summary as $cred): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fa-solid fa-user me-2 text-secondary"></i>
                                                        <?= esc($cred['creditor_name'] ?? 'Bilinmiyor') ?>
                                                        <small class="text-muted">(<?= $cred['count'] ?>)</small>
                                                    </span>
                                                    <span class="fw-bold"
                                                        style="color: #9333ea;"><?= number_format($cred['total'] ?? 0, 2, ',', '.') ?>
                                                        ₺</span>
                                                </li>
                                        <?php endforeach; ?>
                                    </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================= -->
        <!-- ALACAKLILAR YÖNETİMİ -->
        <!-- ============================================= -->
        <div id="view-creditors" class="section-view">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0" style="color: #9333ea;">
                    <i class="fa-solid fa-users me-2"></i>Alacaklılar / Kişiler
                </h5>
                <button class="btn" style="background-color: #9333ea; color: white;" data-bs-toggle="modal"
                    data-bs-target="#modalCreditor">
                    <i class="fa-solid fa-user-plus me-2"></i>Yeni Alacaklı Ekle
                </button>
            </div>

            <div class="row">
                <?php if (empty($creditors)): ?>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="fa-solid fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz alacaklı eklenmemiş</h5>
                                    <p class="text-muted">Borç takibi yapabilmek için önce alacaklı ekleyin.</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreditor">
                                        <i class="fa-solid fa-plus me-2"></i>Alacaklı Ekle
                                    </button>
                                </div>
                            </div>
                        </div>
                <?php else: ?>
                        <?php foreach ($creditors as $cred): ?>
                                <?php
                                // Bu alacaklının borç toplamını bul
                                $credDebt = 0;
                                $credCount = 0;
                                foreach ($creditor_summary ?? [] as $cs) {
                                    if (($cs['id'] ?? 0) == $cred['id']) {
                                        $credDebt = (float) ($cs['total'] ?? 0);
                                        $credCount = (int) ($cs['count'] ?? 0);
                                        break;
                                    }
                                }
                                ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                                    style="width: 50px; height: 50px; background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%); color: white; font-weight: 600; font-size: 1.2rem;">
                                                    <?= strtoupper(mb_substr($cred['creditor_name'], 0, 1)) ?>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0 fw-bold"><?= esc($cred['creditor_name']) ?></h6>
                                                    <?php if (!empty($cred['phone'])): ?>
                                                            <small class="text-muted"><i
                                                                    class="fa-solid fa-phone me-1"></i><?= esc($cred['phone']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="badge <?= $cred['status'] == 'aktif' ? 'bg-success' : 'bg-secondary' ?>">
                                                    <?= $cred['status'] == 'aktif' ? 'Aktif' : 'Pasif' ?>
                                                </span>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <div class="p-2 rounded" style="background: #f8f4ff;">
                                                        <small class="text-muted d-block">Toplam Borç</small>
                                                        <span class="fw-bold"
                                                            style="color: #9333ea;"><?= number_format($credDebt, 2, ',', '.') ?>
                                                            ₺</span>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="p-2 rounded" style="background: #f0fdf4;">
                                                        <small class="text-muted d-block">İşlem Sayısı</small>
                                                        <span class="fw-bold text-success"><?= $credCount ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (!empty($cred['notes'])): ?>
                                                    <p class="text-muted small mb-3">
                                                        <i class="fa-solid fa-note-sticky me-1"></i>
                                                        <?= esc(mb_substr($cred['notes'], 0, 50)) ?>
                                                        <?= mb_strlen($cred['notes']) > 50 ? '...' : '' ?>
                                                    </p>
                                            <?php endif; ?>

                                            <div class="d-flex gap-2">
                                                <a href="<?= site_url('Munu/creditor_report/' . $cred['id']) ?>"
                                                    class="btn btn-sm btn-outline-primary flex-grow-1">
                                                    <i class="fa-solid fa-file-lines me-1"></i>Detay
                                                </a>
                                                <button class="btn btn-sm btn-outline-secondary"
                                                    onclick="openCreditorEditModal(<?= $cred['id'] ?>)">
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>
                                                <?php if ($credDebt <= 0): ?>
                                                        <a href="<?= site_url('Munu/delete_creditor/' . $cred['id']) ?>"
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Bu alacaklıyı silmek istediğinize emin misiniz?')">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- ============================================= -->
        <!-- İŞLEMLER -->
        <!-- ============================================= -->
        <div id="view-transactions" class="section-view">
            <div class="d-flex justify-content-end mb-4 gap-2">
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTransactionGeneric"
                    onclick="setTransactionType('borc')">
                    <i class="fa-solid fa-minus me-2"></i>Borç Ekle
                </button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTransactionGeneric"
                    onclick="setTransactionType('tahsilat')">
                    <i class="fa-solid fa-plus me-2"></i>Tahsilat Al
                </button>
            </div>

            <div class="card overflow-hidden">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Tarih</th>
                            <th>Müşteri</th>
                            <th>Tür</th>
                            <th>Tutar</th>
                            <th>Açıklama</th>
                            <th class="text-end pe-4">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Henüz işlem yapılmadı.</td>
                                </tr>
                        <?php else: ?>
                                <?php foreach ($transactions as $t): ?>
                                        <tr>
                                            <td class="ps-4"><?= date('d.m.Y', strtotime($t['transaction_date'])) ?></td>
                                            <td class="fw-bold"><?= esc($t['customer_name']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $t['transaction_type'] == 'borc' ? 'danger' : 'success' ?>">
                                                    <?= $t['transaction_type'] == 'borc' ? 'Borç' : 'Tahsilat' ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold"><?= number_format($t['amount'], 2, ',', '.') ?> ₺</td>
                                            <td><?= esc($t['description'] ?: '-') ?></td>
                                            <td class="text-end pe-4">
                                                <a href="<?= site_url('Munu/delete_transaction/' . $t['id']) ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Bu işlemi silmek istediğinize emin misiniz?')">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============================================= -->
        <!-- KARALAMA DEFTERİ -->
        <!-- ============================================= -->
        <div id="view-notes" class="section-view">
            <!-- Üst İstatistik Kartları -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 h-100"
                        style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 opacity-75">Toplam Not</h6>
                                    <h3 class="mb-0 fw-bold"><?= $note_stats['total'] ?? 0 ?></h3>
                                </div>
                                <i class="fa-solid fa-note-sticky fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 h-100"
                        style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 opacity-75">Gecikmiş</h6>
                                    <h3 class="mb-0 fw-bold"><?= $note_stats['overdue'] ?? 0 ?></h3>
                                </div>
                                <i class="fa-solid fa-clock fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 h-100"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 opacity-75">Bugün</h6>
                                    <h3 class="mb-0 fw-bold"><?= $note_stats['today'] ?? 0 ?></h3>
                                </div>
                                <i class="fa-solid fa-calendar-day fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 h-100"
                        style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 opacity-75">Tamamlanan</h6>
                                    <h3 class="mb-0 fw-bold"><?= $note_stats['completed'] ?? 0 ?></h3>
                                </div>
                                <i class="fa-solid fa-check-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Sol: Notlar -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="m-0 fw-bold" style="color: #f59e0b;">
                            <i class="fa-solid fa-note-sticky me-2"></i>Notlarım
                        </h5>
                        <button class="btn text-white"
                            style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);"
                            data-bs-toggle="modal" data-bs-target="#modalNote">
                            <i class="fa-solid fa-plus me-2"></i>Yeni Not
                        </button>
                    </div>

                    <!-- Not Kartları -->
                    <?php $notes = $notes ?? []; ?>
                    <div class="row" id="notesContainer">
                        <?php if (empty($notes)): ?>
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm text-center py-5">
                                        <i class="fa-solid fa-note-sticky fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">Henüz not eklenmemiş</h5>
                                        <p class="text-muted">Yapılacaklar, hatırlatıcılar veya notlar ekleyin.</p>
                                    </div>
                                </div>
                        <?php else: ?>
                                <?php foreach ($notes as $note): ?>
                                        <?php
                                        $isOverdue = $note['due_date'] && strtotime($note['due_date']) < time();
                                        $isToday = $note['due_date'] && date('Y-m-d', strtotime($note['due_date'])) === date('Y-m-d');
                                        $priorityColors = [
                                            'dusuk' => 'secondary',
                                            'normal' => 'primary',
                                            'yuksek' => 'warning',
                                            'acil' => 'danger'
                                        ];
                                        $typeIcons = [
                                            'not' => 'fa-note-sticky',
                                            'hatirlatici' => 'fa-bell',
                                            'siparis' => 'fa-shopping-cart',
                                            'gorev' => 'fa-list-check'
                                        ];
                                        ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 border-0 shadow-sm"
                                                style="border-radius: 16px; border-left: 4px solid <?= esc($note['color']) ?> !important; background: <?= esc($note['color']) ?>20;">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <?php if ($note['is_pinned']): ?>
                                                                    <i class="fa-solid fa-thumbtack text-danger"></i>
                                                            <?php endif; ?>
                                                            <span class="badge bg-<?= $priorityColors[$note['priority']] ?>">
                                                                <?= ucfirst($note['priority']) ?>
                                                            </span>
                                                            <span class="badge bg-secondary">
                                                                <i
                                                                    class="fa-solid <?= $typeIcons[$note['note_type']] ?? 'fa-note-sticky' ?> me-1"></i>
                                                                <?= ucfirst($note['note_type']) ?>
                                                            </span>
                                                        </div>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-link text-muted p-0"
                                                                data-bs-toggle="dropdown">
                                                                <i class="fa-solid fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <a class="dropdown-item" href="#"
                                                                        onclick="openNoteEditModal(<?= $note['id'] ?>)">
                                                                        <i class="fa-solid fa-edit me-2"></i>Düzenle
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                        href="<?= site_url('Munu/toggle_pin_note/' . $note['id']) ?>">
                                                                        <i class="fa-solid fa-thumbtack me-2"></i>
                                                                        <?= $note['is_pinned'] ? 'Sabitlemeyi Kaldır' : 'Sabitle' ?>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item text-success"
                                                                        href="<?= site_url('Munu/complete_note/' . $note['id']) ?>"
                                                                        onclick="return confirm('Bu notu tamamlandı olarak işaretlemek istiyor musunuz?')">
                                                                        <i class="fa-solid fa-check me-2"></i>Tamamla
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item text-danger"
                                                                        href="<?= site_url('Munu/delete_note/' . $note['id']) ?>"
                                                                        onclick="return confirm('Bu notu silmek istediğinize emin misiniz?')">
                                                                        <i class="fa-solid fa-trash me-2"></i>Sil
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <h6 class="fw-bold mb-2"><?= esc($note['title']) ?></h6>

                                                    <?php if ($note['content']): ?>
                                                            <p class="text-muted small mb-2">
                                                                <?= nl2br(esc(mb_substr($note['content'], 0, 100))) ?>
                                                                <?= mb_strlen($note['content']) > 100 ? '...' : '' ?>
                                                            </p>
                                                    <?php endif; ?>

                                                    <?php if ($note['due_date']): ?>
                                                            <div class="mt-2">
                                                                <?php if ($isOverdue): ?>
                                                                        <span class="badge bg-danger">
                                                                            <i class="fa-solid fa-exclamation-triangle me-1"></i>
                                                                            Gecikmiş: <?= date('d.m.Y H:i', strtotime($note['due_date'])) ?>
                                                                        </span>
                                                                <?php elseif ($isToday): ?>
                                                                        <span class="badge bg-warning text-dark">
                                                                            <i class="fa-solid fa-clock me-1"></i>
                                                                            Bugün: <?= date('H:i', strtotime($note['due_date'])) ?>
                                                                        </span>
                                                                <?php else: ?>
                                                                        <span class="text-muted small">
                                                                            <i class="fa-regular fa-calendar me-1"></i>
                                                                            <?= date('d.m.Y H:i', strtotime($note['due_date'])) ?>
                                                                        </span>
                                                                <?php endif; ?>
                                                            </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sağ: Takvim & Yaklaşan -->
                <div class="col-lg-4">
                    <!-- Mini Takvim -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                        <div class="card-header bg-white border-0 pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-sm btn-outline-secondary" onclick="changeMonth(-1)">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                <h6 class="mb-0 fw-bold" id="calendarMonth"><?= strftime('%B %Y', time()) ?></h6>
                                <button class="btn btn-sm btn-outline-secondary" onclick="changeMonth(1)">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body pt-2">
                            <div id="miniCalendar" class="mini-calendar">
                                <!-- JavaScript ile doldurulacak -->
                            </div>
                        </div>
                    </div>

                    <!-- Bugünkü Notlar -->
                    <?php if (!empty($today_notes)): ?>
                            <div class="card border-0 shadow-sm mb-4"
                                style="border-radius: 16px; border-left: 4px solid #3b82f6 !important;">
                                <div class="card-header bg-white border-0">
                                    <h6 class="mb-0 fw-bold text-primary">
                                        <i class="fa-solid fa-calendar-day me-2"></i>Bugün
                                    </h6>
                                </div>
                                <div class="card-body pt-0">
                                    <?php foreach ($today_notes as $tn): ?>
                                            <div class="d-flex align-items-center mb-2 p-2 rounded" style="background: #eff6ff;">
                                                <i class="fa-solid fa-bell text-primary me-2"></i>
                                                <div class="flex-grow-1">
                                                    <div class="small fw-bold"><?= esc($tn['title']) ?></div>
                                                    <small class="text-muted"><?= date('H:i', strtotime($tn['due_date'])) ?></small>
                                                </div>
                                            </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                    <?php endif; ?>

                    <!-- Yaklaşan Notlar -->
                    <?php if (!empty($upcoming_notes)): ?>
                            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                                <div class="card-header bg-white border-0">
                                    <h6 class="mb-0 fw-bold" style="color: #f59e0b;">
                                        <i class="fa-solid fa-calendar-week me-2"></i>Yaklaşan (7 gün)
                                    </h6>
                                </div>
                                <div class="card-body pt-0">
                                    <?php foreach (array_slice($upcoming_notes, 0, 5) as $un): ?>
                                            <div class="d-flex align-items-center mb-2 p-2 rounded" style="background: #fef3c7;">
                                                <div class="text-center me-3" style="min-width: 40px;">
                                                    <div class="fw-bold" style="color: #f59e0b;">
                                                        <?= date('d', strtotime($un['due_date'])) ?>
                                                    </div>
                                                    <small class="text-muted"><?= date('M', strtotime($un['due_date'])) ?></small>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="small fw-bold"><?= esc($un['title']) ?></div>
                                                    <small class="text-muted"><?= date('H:i', strtotime($un['due_date'])) ?></small>
                                                </div>
                                            </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ============================================= -->
        <!-- PERSONEL YÖNETİMİ -->
        <!-- ============================================= -->
        <!-- ============================================= -->
        <!-- AYARLAR & PROFİL -->
        <!-- ============================================= -->
        <div id="view-settings" class="section-view">
            <div class="row">
                <!-- Sol: Profil Bilgileri -->
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center text-white rounded-circle"
                                style="width: 80px; height: 80px; font-size: 2rem; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                                <?= strtoupper(mb_substr(session()->get('full_name') ?? 'A', 0, 1)) ?>
                            </div>
                            <h5 class="fw-bold mb-1"><?= esc(session()->get('full_name')) ?></h5>
                            <span class="badge bg-dark px-3 py-2 rounded-pill">YÖNETİCİ</span>
                        </div>
                        <div class="card-body p-4">
                            <form>
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold"><i
                                            class="fa-solid fa-building me-2"></i>ŞİRKET ADI</label>
                                    <input type="text" class="form-control form-control-lg bg-light"
                                        value="<?= esc(session()->get('company_name')) ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold"><i
                                            class="fa-solid fa-user me-2"></i>KULLANICI ADI</label>
                                    <input type="text" class="form-control form-control-lg bg-light"
                                        value="<?= esc(session()->get('username')) ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold"><i
                                            class="fa-solid fa-id-card me-2"></i>AD SOYAD</label>
                                    <input type="text" class="form-control form-control-lg bg-light"
                                        value="<?= esc(session()->get('full_name')) ?>" readonly>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sağ: Şifre Değiştir -->
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-header bg-white border-0 pt-4 pb-0">
                            <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-lock me-2"></i>Güvenlik & Şifre
                            </h5>
                            <small class="text-muted">Hesap şifrenizi buradan güncelleyebilirsiniz.</small>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?= site_url('Munu/change_password') ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mevcut Şifre</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="fa-solid fa-key text-muted"></i></span>
                                        <input type="password" name="current_password"
                                            class="form-control border-start-0" placeholder="Mevcut şifrenizi girin"
                                            required>
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Yeni Şifre</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="fa-solid fa-lock text-muted"></i></span>
                                        <input type="password" name="new_password" class="form-control border-start-0"
                                            placeholder="En az 6 karakter" required minlength="6">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Yeni Şifre (Tekrar)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="fa-solid fa-lock text-muted"></i></span>
                                        <input type="password" name="confirm_password"
                                            class="form-control border-start-0" placeholder="Yeni şifreyi tekrar girin"
                                            required minlength="6">
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fa-solid fa-save me-2"></i>Şifreyi Güncelle
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ============================================= -->
    <!-- MODALLER -->
    <!-- ============================================= -->

    <!-- Müşteri Ekleme Modal -->
    <div class="modal fade" id="modalCustomer" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-user-plus me-2"></i>Yeni Müşteri</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= site_url('Munu/add_customer') ?>" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" placeholder="Müşteri adı girin"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" name="phone" class="form-control" placeholder="5XX XXX XX XX">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-posta</label>
                                <input type="email" name="email" class="form-control" placeholder="ornek@mail.com">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Müşteri Türü</label>
                            <select name="customer_type" class="form-select">
                                <option value="bireysel">Bireysel</option>
                                <option value="kurumsal">Kurumsal</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Adres</label>
                                <input type="text" name="address" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Şehir</label>
                                <input type="text" name="city" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- İşlem Modal - Müşteri Sayfasından -->
    <div class="modal fade" id="modalTransaction" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="transModalHeader">
                    <h5 class="modal-title" id="transModalTitle">İşlem Ekle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= site_url('Munu/add_transaction') ?>" method="post">
                    <input type="hidden" name="customer_id" id="transCustomerId">
                    <input type="hidden" name="transaction_type" id="transType">
                    <div class="modal-body">
                        <div class="alert alert-info py-2 mb-3">
                            <strong>Müşteri:</strong> <span id="transCustomerName"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tutar (₺) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control form-control-lg" step="0.01"
                                min="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Açıklama</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tarih</label>
                            <input type="date" name="transaction_date" class="form-control"
                                value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn" id="transSubmitBtn">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- İşlem Modal - Genel -->
    <div class="modal fade" id="modalTransactionGeneric" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="transGenericHeader">
                    <h5 class="modal-title" id="transGenericTitle">İşlem Ekle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= site_url('Munu/add_transaction') ?>" method="post">
                    <input type="hidden" name="transaction_type" id="transGenericType">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Müşteri Seçin <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">-- Müşteri Seçin --</option>
                                <?php foreach ($customers as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= esc($c['customer_name']) ?>
                                            (<?= number_format($c['balance'], 2, ',', '.') ?> ₺)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tutar (₺) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control form-control-lg" step="0.01"
                                min="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Açıklama</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tarih</label>
                            <input type="date" name="transaction_date" class="form-control"
                                value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn" id="transGenericSubmitBtn">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Borç Ekleme Modal -->
    <!-- Borç/Gider Ekleme Modal -->
    <div class="modal fade" id="modalMyDebt" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);">
                    <h5 class="modal-title"><i class="fa-solid fa-hand-holding-dollar me-2"></i>Borç / Gider Ekle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= site_url('Munu/add_my_debt') ?>" method="post">
                    <div class="modal-body">
                        <!-- Kayıt Türü Seçimi -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Kayıt Türü</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="expense_type" id="typeBorc" value="borc"
                                    checked>
                                <label class="btn btn-outline-danger" for="typeBorc">
                                    <i class="fa-solid fa-hand-holding-dollar me-2"></i>Borç (Vadeli)
                                </label>
                                <input type="radio" class="btn-check" name="expense_type" id="typeGider" value="gider">
                                <label class="btn btn-outline-warning" for="typeGider">
                                    <i class="fa-solid fa-receipt me-2"></i>Günlük Gider
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Kategori Seçimi -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fa-solid fa-tag me-1"></i>Kategori
                                </label>
                                <select name="category_id" class="form-select">
                                    <option value="">Kategori Seçin...</option>
                                    <?php if (!empty($expense_categories)): ?>
                                            <?php foreach ($expense_categories as $cat): ?>
                                                    <option value="<?= $cat['id'] ?>" data-icon="<?= esc($cat['icon']) ?>"
                                                        data-color="<?= esc($cat['color']) ?>">
                                                        <?= esc($cat['category_name']) ?>
                                                    </option>
                                            <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Alacaklı Seçimi -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fa-solid fa-user me-1"></i>Alacaklı / Kişi
                                </label>
                                <select name="creditor_id" class="form-select" id="creditorSelect">
                                    <option value="0">Kişi Seçin veya Yeni Ekleyin...</option>
                                    <?php if (!empty($creditors)): ?>
                                            <?php foreach ($creditors as $cred): ?>
                                                    <option value="<?= $cred['id'] ?>"><?= esc($cred['creditor_name']) ?></option>
                                            <?php endforeach; ?>
                                    <?php endif; ?>
                                    <option value="0" class="text-primary">➕ Yeni Alacaklı Ekle</option>
                                </select>
                                <input type="text" name="new_creditor" class="form-control mt-2" id="newCreditorInput"
                                    placeholder="Yeni alacaklı adı..." style="display: none;">
                            </div>
                        </div>

                        <div class="row">
                            <!-- Tutar -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tutar <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="amount" class="form-control form-control-lg" step="0.01"
                                        min="0.01" required placeholder="0,00">
                                    <span class="input-group-text">₺</span>
                                </div>
                            </div>

                            <!-- Son Ödeme Tarihi -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Son Ödeme Tarihi</label>
                                <input type="date" name="due_date" class="form-control form-control-lg">
                                <small class="text-muted">Günlük giderler için boş bırakabilirsiniz</small>
                            </div>
                        </div>

                        <!-- Açıklama -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Açıklama</label>
                            <textarea name="description" class="form-control" rows="2"
                                placeholder="Opsiyonel not..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn text-white"
                            style="background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);">
                            <i class="fa-solid fa-save me-2"></i>Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alacaklı Ekleme Modal -->
    <div class="modal fade" id="modalCreditor" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);">
                    <h5 class="modal-title"><i class="fa-solid fa-user-plus me-2"></i>Yeni Alacaklı Ekle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= site_url('Munu/add_creditor') ?>" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alacaklı Adı <span class="text-danger">*</span></label>
                            <input type="text" name="creditor_name" class="form-control" required
                                placeholder="Kişi veya firma adı...">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" name="phone" class="form-control" placeholder="0555 123 45 67">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-posta</label>
                                <input type="email" name="email" class="form-control" placeholder="ornek@email.com">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notlar</label>
                            <textarea name="notes" class="form-control" rows="2"
                                placeholder="Opsiyonel notlar..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn text-white"
                            style="background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);">
                            <i class="fa-solid fa-save me-2"></i>Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alacaklı Düzenleme Modal -->
    <div class="modal fade" id="modalCreditorEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                    <h5 class="modal-title"><i class="fa-solid fa-user-edit me-2"></i>Alacaklı Düzenle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="creditorEditForm" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alacaklı Adı <span class="text-danger">*</span></label>
                            <input type="text" name="creditor_name" id="edit_creditor_name" class="form-control"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" name="phone" id="edit_creditor_phone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-posta</label>
                                <input type="email" name="email" id="edit_creditor_email" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notlar</label>
                            <textarea name="notes" id="edit_creditor_notes" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Durum</label>
                            <select name="status" id="edit_creditor_status" class="form-select">
                                <option value="aktif">Aktif</option>
                                <option value="pasif">Pasif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn text-white"
                            style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                            <i class="fa-solid fa-save me-2"></i>Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ürün Ekleme Modal -->
    <!-- Not Ekleme Modal -->
    <div class="modal fade" id="modalNote" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                    <h5 class="modal-title"><i class="fa-solid fa-note-sticky me-2"></i>Yeni Not</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= site_url('Munu/add_note') ?>" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Başlık <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="Not başlığı...">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tür</label>
                                <select name="note_type" class="form-select">
                                    <option value="not">📝 Not</option>
                                    <option value="hatirlatici">🔔 Hatırlatıcı</option>
                                    <option value="siparis">🛒 Sipariş</option>
                                    <option value="gorev">✅ Görev</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Öncelik</label>
                                <select name="priority" class="form-select">
                                    <option value="dusuk">⬇️ Düşük</option>
                                    <option value="normal" selected>➡️ Normal</option>
                                    <option value="yuksek">⬆️ Yüksek</option>
                                    <option value="acil">🔴 Acil</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tarih</label>
                                <input type="date" name="due_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Saat</label>
                                <input type="time" name="due_time" class="form-control" value="09:00">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Renk</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="radio" name="color" value="#fef3c7" id="colorYellow" class="btn-check"
                                    checked>
                                <label for="colorYellow" class="btn rounded-circle p-3"
                                    style="background: #fef3c7; border: 2px solid #fbbf24;"></label>

                                <input type="radio" name="color" value="#dbeafe" id="colorBlue" class="btn-check">
                                <label for="colorBlue" class="btn rounded-circle p-3"
                                    style="background: #dbeafe; border: 2px solid transparent;"></label>

                                <input type="radio" name="color" value="#dcfce7" id="colorGreen" class="btn-check">
                                <label for="colorGreen" class="btn rounded-circle p-3"
                                    style="background: #dcfce7; border: 2px solid transparent;"></label>

                                <input type="radio" name="color" value="#fee2e2" id="colorRed" class="btn-check">
                                <label for="colorRed" class="btn rounded-circle p-3"
                                    style="background: #fee2e2; border: 2px solid transparent;"></label>

                                <input type="radio" name="color" value="#f3e8ff" id="colorPurple" class="btn-check">
                                <label for="colorPurple" class="btn rounded-circle p-3"
                                    style="background: #f3e8ff; border: 2px solid transparent;"></label>

                                <input type="radio" name="color" value="#f1f5f9" id="colorGray" class="btn-check">
                                <label for="colorGray" class="btn rounded-circle p-3"
                                    style="background: #f1f5f9; border: 2px solid transparent;"></label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">İçerik</label>
                            <textarea name="content" class="form-control" rows="3"
                                placeholder="Not detayları..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn text-white"
                            style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                            <i class="fa-solid fa-save me-2"></i>Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Not Düzenleme Modal -->
    <div class="modal fade" id="modalNoteEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                    <h5 class="modal-title"><i class="fa-solid fa-edit me-2"></i>Not Düzenle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="noteEditForm" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Başlık <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit_note_title" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tür</label>
                                <select name="note_type" id="edit_note_type" class="form-select">
                                    <option value="not">📝 Not</option>
                                    <option value="hatirlatici">🔔 Hatırlatıcı</option>
                                    <option value="siparis">🛒 Sipariş</option>
                                    <option value="gorev">✅ Görev</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Öncelik</label>
                                <select name="priority" id="edit_note_priority" class="form-select">
                                    <option value="dusuk">⬇️ Düşük</option>
                                    <option value="normal">➡️ Normal</option>
                                    <option value="yuksek">⬆️ Yüksek</option>
                                    <option value="acil">🔴 Acil</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tarih</label>
                                <input type="date" name="due_date" id="edit_note_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Saat</label>
                                <input type="time" name="due_time" id="edit_note_time" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Renk</label>
                            <select name="color" id="edit_note_color" class="form-select">
                                <option value="#fef3c7">🟡 Sarı</option>
                                <option value="#dbeafe">🔵 Mavi</option>
                                <option value="#dcfce7">🟢 Yeşil</option>
                                <option value="#fee2e2">🔴 Kırmızı</option>
                                <option value="#f3e8ff">🟣 Mor</option>
                                <option value="#f1f5f9">⚪ Gri</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">İçerik</label>
                            <textarea name="content" id="edit_note_content" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn text-white"
                            style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                            <i class="fa-solid fa-save me-2"></i>Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Personel Ekleme Modal -->
    <div class="modal fade" id="modalUser" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-user-plus me-2"></i>Yeni Personel</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= site_url('Munu/add_user') ?>" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kullanıcı Adı <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select name="role" class="form-select">
                                <option value="personel">Personel</option>
                                <option value="yonetici">Yönetici</option>
                            </select>
                        </div>
                        <div class="alert alert-info py-2">
                            <small>Varsayılan şifre: <strong>1234</strong></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Oluştur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Müşteri Düzenleme Modal -->
    <div class="modal fade" id="modalEditCustomer" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-user-edit me-2"></i>Müşteri Düzenle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCustomerForm" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" id="edit_customer_name" class="form-control"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" name="phone" id="edit_phone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-posta</label>
                                <input type="email" name="email" id="edit_email" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Müşteri Türü</label>
                            <select name="customer_type" id="edit_customer_type" class="form-select">
                                <option value="bireysel">Bireysel</option>
                                <option value="kurumsal">Kurumsal</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Adres</label>
                                <input type="text" name="address" id="edit_address" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Şehir</label>
                                <input type="text" name="city" id="edit_city" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notlar</label>
                            <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sayfa Navigasyonu
        function nav(pageId, element) {
            event.preventDefault();

            document.querySelectorAll('.sidebar .nav-link').forEach(el => {
                el.classList.remove('active');

            });

            element.classList.add('active');

            document.querySelectorAll('.section-view').forEach(el => el.classList.remove('active'));
            document.getElementById('view-' + pageId).classList.add('active');

            const titles = {
                dashboard: "Genel Bakış",
                customers: "Müşteri Listesi",
                transactions: "İşlem Geçmişi",
                notes: "Karalama Defteri",
                settings: "Firma & Profil Ayarları",
                mydebts: "Borçlarım",
                creditors: "Alacaklılar"
            };
            document.getElementById('pageTitle').innerText = titles[pageId];
        }

        // İşlem Modal (Borç/Tahsilat)
        function openTransactionModal(customerId, customerName, type) {
            document.getElementById('transCustomerId').value = customerId;
            document.getElementById('transCustomerName').innerText = customerName;
            document.getElementById('transType').value = type;

            const header = document.getElementById('transModalHeader');
            const title = document.getElementById('transModalTitle');
            const btn = document.getElementById('transSubmitBtn');

            if (type === 'borc') {
                header.className = 'modal-header bg-danger text-white';
                title.innerHTML = '<i class="fa-solid fa-minus me-2"></i>Borç Ekle';
                btn.className = 'btn btn-danger';
                btn.innerText = 'Borç Kaydet';
            } else {
                header.className = 'modal-header bg-success text-white';
                title.innerHTML = '<i class="fa-solid fa-plus me-2"></i>Tahsilat Al';
                btn.className = 'btn btn-success';
                btn.innerText = 'Tahsilat Kaydet';
            }

            new bootstrap.Modal(document.getElementById('modalTransaction')).show();
        }

        // Müşteri Düzenleme Modal
        function openEditCustomerModal(id, name, phone, email, address, city, type, notes) {
            document.getElementById('editCustomerForm').action = '<?= site_url('Munu/update_customer/') ?>' + id;
            document.getElementById('edit_customer_name').value = name || '';
            document.getElementById('edit_phone').value = phone || '';
            document.getElementById('edit_email').value = email || '';
            document.getElementById('edit_address').value = address || '';
            document.getElementById('edit_city').value = city || '';
            document.getElementById('edit_customer_type').value = type || 'bireysel';
            document.getElementById('edit_notes').value = notes || '';

            new bootstrap.Modal(document.getElementById('modalEditCustomer')).show();
        }

        function setTransactionType(type) {
            document.getElementById('transGenericType').value = type;

            const header = document.getElementById('transGenericHeader');
            const title = document.getElementById('transGenericTitle');
            const btn = document.getElementById('transGenericSubmitBtn');

            if (type === 'borc') {
                header.className = 'modal-header bg-danger text-white';
                title.innerHTML = '<i class="fa-solid fa-minus me-2"></i>Borç Ekle';
                btn.className = 'btn btn-danger';
            } else {
                header.className = 'modal-header bg-success text-white';
                title.innerHTML = '<i class="fa-solid fa-plus me-2"></i>Tahsilat Al';
                btn.className = 'btn btn-success';
            }
        }

        function quickStockAdd(productId, productName) {
            document.getElementById('stockProductName').innerText = productName;
            document.getElementById('stockUpdateForm').action = '<?= site_url('Munu/update_stock/') ?>' + productId;
            new bootstrap.Modal(document.getElementById('modalStockUpdate')).show();
        }

        // Müşteri Arama
        document.getElementById('customerSearch')?.addEventListener('input', function (e) {
            const keyword = e.target.value.toLowerCase();
            document.querySelectorAll('.customer-row').forEach(row => {
                const name = row.dataset.name;
                const phone = row.dataset.phone || '';
                if (name.includes(keyword) || phone.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Flash mesajları otomatik kapat
        setTimeout(() => {
            document.querySelectorAll('.alert-floating').forEach(alert => {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);

        // Sayfa yüklendiğinde URL hash'i kontrol et ve doğru sayfayı göster
        document.addEventListener('DOMContentLoaded', function () {
            const hash = window.location.hash.replace('#', '');
            if (hash && ['dashboard', 'customers', 'transactions', 'stock', 'mydebts', 'users'].includes(hash)) {
                // Tüm section'ları gizle
                document.querySelectorAll('.section-view').forEach(el => el.classList.remove('active'));
                // Hedef section'ı göster
                const targetView = document.getElementById('view-' + hash);
                if (targetView) {
                    targetView.classList.add('active');
                }

                // Sidebar aktif linkini güncelle
                document.querySelectorAll('.sidebar .nav-link').forEach(el => {
                    el.classList.remove('active');

                });

                // Doğru menüyü aktif yap
                const menuLinks = document.querySelectorAll('.sidebar .nav-link');
                menuLinks.forEach(link => {
                    if (link.getAttribute('onclick') && link.getAttribute('onclick').includes("'" + hash + "'")) {
                        link.classList.add('active');
                    }
                });

                // Sayfa başlığını güncelle
                const titles = {
                    dashboard: "Genel Bakış",
                    customers: "Müşteri Listesi",
                    transactions: "İşlem Geçmişi",
                    notes: "Karalama Defteri",
                    users: "Personel Yönetimi",
                    mydebts: "Borçlarım",
                    creditors: "Alacaklılar"
                };
                document.getElementById('pageTitle').innerText = titles[hash] || "Genel Bakış";
            }
        });

        // Grafik
        const monthlyData = <?= json_encode($monthly_data) ?>;

        if (document.getElementById('mainChart')) {
            const ctx = document.getElementById('mainChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthlyData.length > 0 ? monthlyData.map(d => d.month) : ['Veri Yok'],
                    datasets: [
                        {
                            label: 'Borç',
                            data: monthlyData.length > 0 ? monthlyData.map(d => d.borc) : [0],
                            backgroundColor: 'rgba(239, 68, 68, 0.7)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 1
                        },
                        {
                            label: 'Tahsilat',
                            data: monthlyData.length > 0 ? monthlyData.map(d => d.tahsilat) : [0],
                            backgroundColor: 'rgba(34, 197, 94, 0.7)',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return value.toLocaleString('tr-TR') + ' ₺';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Alacaklı seçimi - yeni ekleme input'u göster/gizle
        document.getElementById('creditorSelect')?.addEventListener('change', function () {
            const newInput = document.getElementById('newCreditorInput');
            if (this.value === '0' && this.options[this.selectedIndex].text.includes('Yeni')) {
                newInput.style.display = 'block';
                newInput.focus();
            } else {
                newInput.style.display = 'none';
                newInput.value = '';
            }
        });

        // Alacaklı düzenleme modalını aç
        function openCreditorEditModal(creditorId) {
            fetch('<?= site_url('Munu/get_creditor/') ?>' + creditorId)
                .then(response => response.json())
                .then(data => {
                    if (data.creditor) {
                        document.getElementById('edit_creditor_name').value = data.creditor.creditor_name || '';
                        document.getElementById('edit_creditor_phone').value = data.creditor.phone || '';
                        document.getElementById('edit_creditor_email').value = data.creditor.email || '';
                        document.getElementById('edit_creditor_notes').value = data.creditor.notes || '';
                        document.getElementById('edit_creditor_status').value = data.creditor.status || 'aktif';
                        document.getElementById('creditorEditForm').action = '<?= site_url('Munu/update_creditor/') ?>' + creditorId;

                        const modal = new bootstrap.Modal(document.getElementById('modalCreditorEdit'));
                        modal.show();
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    alert('Alacaklı bilgileri yüklenirken hata oluştu.');
                });
        }

        // Section gösterme fonksiyonu (hash link için)
        function showSection(sectionId) {
            // Tüm section'ları gizle
            document.querySelectorAll('.section-view').forEach(function (el) {
                el.style.display = 'none';
            });
            // Hedef section'ı göster
            const target = document.getElementById('view-' + sectionId);
            if (target) {
                target.style.display = 'block';
            }
            // URL hash'i güncelle
            window.location.hash = sectionId;
            // Nav link'i aktif yap
            document.querySelectorAll('.sidebar .nav-link').forEach(function (link) {
                link.classList.remove('active');
                if (link.getAttribute('onclick')?.includes("'" + sectionId + "'")) {
                    link.classList.add('active');
                }
            });
        }

        // ========================================
        // KARALAMA DEFTERİ FONKSİYONLARI
        // ========================================

        // Mini takvim değişkenleri
        let currentCalendarDate = new Date();

        // Takvim oluştur
        function renderMiniCalendar() {
            const container = document.getElementById('miniCalendar');
            if (!container) return;

            const year = currentCalendarDate.getFullYear();
            const month = currentCalendarDate.getMonth();

            // Ay adını güncelle
            const monthNames = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
                'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
            document.getElementById('calendarMonth').textContent = monthNames[month] + ' ' + year;

            // Ayın ilk günü ve toplam gün sayısı
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();

            // Hafta günleri
            let html = '<div class="calendar-grid">';
            html += '<div class="calendar-header">';
            ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'].forEach(d => {
                html += '<div class="calendar-day-name">' + d + '</div>';
            });
            html += '</div><div class="calendar-days">';

            // Boş günler (ayın ilk gününden önce)
            const startDay = firstDay === 0 ? 6 : firstDay - 1;
            for (let i = 0; i < startDay; i++) {
                html += '<div class="calendar-day empty"></div>';
            }

            // Günler
            for (let day = 1; day <= daysInMonth; day++) {
                const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;
                html += '<div class="calendar-day' + (isToday ? ' today' : '') + '" data-date="' + year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0') + '">';
                html += day;
                html += '</div>';
            }

            html += '</div></div>';
            container.innerHTML = html;
        }

        // Ay değiştir
        function changeMonth(delta) {
            currentCalendarDate.setMonth(currentCalendarDate.getMonth() + delta);
            renderMiniCalendar();
        }

        // Not düzenleme modalını aç
        function openNoteEditModal(noteId) {
            fetch('<?= site_url('Munu/get_note/') ?>' + noteId)
                .then(response => response.json())
                .then(data => {
                    if (data.note) {
                        const note = data.note;
                        document.getElementById('edit_note_title').value = note.title || '';
                        document.getElementById('edit_note_type').value = note.note_type || 'not';
                        document.getElementById('edit_note_priority').value = note.priority || 'normal';
                        document.getElementById('edit_note_content').value = note.content || '';
                        document.getElementById('edit_note_color').value = note.color || '#fef3c7';

                        // Tarih ve saat
                        if (note.due_date) {
                            const dt = new Date(note.due_date);
                            document.getElementById('edit_note_date').value = dt.toISOString().split('T')[0];
                            document.getElementById('edit_note_time').value = dt.toTimeString().slice(0, 5);
                        } else {
                            document.getElementById('edit_note_date').value = '';
                            document.getElementById('edit_note_time').value = '09:00';
                        }

                        document.getElementById('noteEditForm').action = '<?= site_url('Munu/update_note/') ?>' + noteId;

                        const modal = new bootstrap.Modal(document.getElementById('modalNoteEdit'));
                        modal.show();
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    alert('Not bilgileri yüklenirken hata oluştu.');
                });
        }

        // Sayfa yüklendiğinde takvimi oluştur
        document.addEventListener('DOMContentLoaded', function () {
            renderMiniCalendar();
        });
    </script>

    <style>
        /* Mini Takvim Stilleri */
        .calendar-grid {
            font-size: 0.85rem;
        }

        .calendar-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            text-align: center;
            margin-bottom: 5px;
        }

        .calendar-day-name {
            font-weight: 600;
            color: #64748b;
            font-size: 0.75rem;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
        }

        .calendar-day:hover:not(.empty) {
            background: #fef3c7;
        }

        .calendar-day.today {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            font-weight: 600;
        }

        .calendar-day.empty {
            cursor: default;
        }

        .calendar-day.has-notes {
            position: relative;
        }

        .calendar-day.has-notes::after {
            content: '';
            position: absolute;
            bottom: 2px;
            width: 4px;
            height: 4px;
            background: #ef4444;
            border-radius: 50%;
        }
    </style>

    <!-- ============================================= -->
    <!-- AI ASISTAN CHAT WIDGET -->
    <!-- ============================================= -->

    <!-- Chat Toggle Button -->
    <button id="aiChatToggle" class="ai-chat-toggle" title="AI Asistan">
        <div class="ai-icon-container">
            <div class="ai-ring ai-ring-outer"></div>
            <div class="ai-ring ai-ring-inner"></div>
            <div class="ai-core">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ai-brain-svg">
                    <path
                        d="M12 2C9.243 2 7 4.243 7 7v1H6c-1.654 0-3 1.346-3 3v1c0 .552.448 1 1 1s1-.448 1-1v-1c0-.551.449-1 1-1h1v3c0 2.757 2.243 5 5 5s5-2.243 5-5v-3h1c.551 0 1 .449 1 1v1c0 .552.448 1 1 1s1-.448 1-1v-1c0-1.654-1.346-3-3-3h-1V7c0-2.757-2.243-5-5-5z"
                        fill="currentColor" opacity="0.9" />
                    <circle cx="10" cy="9" r="1" fill="currentColor" />
                    <circle cx="14" cy="9" r="1" fill="currentColor" />
                    <path d="M9 15h6M12 15v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="0.5" stroke-dasharray="2 3"
                        class="ai-orbit" />
                </svg>
            </div>
            <div class="ai-particles">
                <span></span><span></span><span></span><span></span>
            </div>
        </div>
    </button>

    <!-- Chat Window -->
    <div id="aiChatWindow" class="ai-chat-window">
        <div class="ai-chat-header">
            <div class="d-flex align-items-center">
                <div class="ai-avatar me-2">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="22" height="22">
                        <path
                            d="M12 2C9.243 2 7 4.243 7 7v1H6c-1.654 0-3 1.346-3 3v1c0 .552.448 1 1 1s1-.448 1-1v-1c0-.551.449-1 1-1h1v3c0 2.757 2.243 5 5 5s5-2.243 5-5v-3h1c.551 0 1 .449 1 1v1c0 .552.448 1 1 1s1-.448 1-1v-1c0-1.654-1.346-3-3-3h-1V7c0-2.757-2.243-5-5-5z"
                            fill="currentColor" opacity="0.9" />
                        <circle cx="10" cy="9" r="1.2" fill="currentColor" />
                        <circle cx="14" cy="9" r="1.2" fill="currentColor" />
                        <path d="M9 14.5h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                </div>
                <div>
                    <h6 class="m-0 fw-bold">MUNU AI</h6>
                    <small class="text-light opacity-75">Akıllı işletme asistanınız</small>
                </div>
            </div>
            <button id="aiChatClose" class="btn btn-sm btn-link text-white">
                <i class="fa-solid fa-times fa-lg"></i>
            </button>
        </div>

        <div id="aiChatMessages" class="ai-chat-messages">
            <!-- Welcome Message -->
            <div class="ai-message ai-message-bot">
                <div class="message-content">
                    Merhaba! 👋 Ben MUNU AI Asistanı. İşletmenizle ilgili sorularınızı yanıtlayabilirim.
                    <br><br>
                    Örnek sorular:
                    <ul class="mb-0 mt-2">
                        <li>Toplam alacağım ne kadar?</li>
                        <li>En borçlu müşterilerim kimler?</li>
                        <li>Bu ay kaç tahsilat yaptım?</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="ai-chat-footer">
            <div class="input-group">
                <input type="text" id="aiChatInput" class="form-control" placeholder="Mesajınızı yazın..."
                    autocomplete="off">
                <button id="aiChatSend" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <style>
        /* AI Chat Widget Styles */
        .ai-chat-toggle {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: 3px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.5), 0 0 20px rgba(139, 92, 246, 0.3);
            z-index: 9999;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ai-chat-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }

        .ai-chat-toggle.active {
            transform: rotate(360deg);
        }

        /* Advanced AI Icon Styles */
        .ai-icon-container {
            position: relative;
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ai-ring {
            position: absolute;
            border-radius: 50%;
            border: 2px solid transparent;
        }

        .ai-ring-outer {
            width: 100%;
            height: 100%;
            border-top-color: rgba(255, 255, 255, 0.8);
            border-right-color: rgba(255, 255, 255, 0.4);
            animation: aiRingSpin 2s linear infinite;
        }

        .ai-ring-inner {
            width: 70%;
            height: 70%;
            border-bottom-color: rgba(255, 255, 255, 0.8);
            border-left-color: rgba(255, 255, 255, 0.4);
            animation: aiRingSpin 1.5s linear infinite reverse;
        }

        @keyframes aiRingSpin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .ai-core {
            position: relative;
            z-index: 2;
            animation: aiPulse 2s ease-in-out infinite;
        }

        .ai-brain-svg {
            width: 32px;
            height: 32px;
            color: white;
            filter: drop-shadow(0 0 6px rgba(255, 255, 255, 0.6));
        }

        .ai-orbit {
            animation: aiOrbitSpin 4s linear infinite;
            transform-origin: center;
        }

        @keyframes aiOrbitSpin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes aiPulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }
        }

        .ai-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .ai-particles span {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            animation: aiParticle 3s ease-in-out infinite;
        }

        .ai-particles span:nth-child(1) {
            top: 0;
            left: 50%;
            animation-delay: 0s;
        }

        .ai-particles span:nth-child(2) {
            top: 50%;
            right: 0;
            animation-delay: 0.75s;
        }

        .ai-particles span:nth-child(3) {
            bottom: 0;
            left: 50%;
            animation-delay: 1.5s;
        }

        .ai-particles span:nth-child(4) {
            top: 50%;
            left: 0;
            animation-delay: 2.25s;
        }

        @keyframes aiParticle {

            0%,
            100% {
                opacity: 0;
                transform: scale(0.5) translateY(0);
            }

            50% {
                opacity: 1;
                transform: scale(1) translateY(-3px);
            }
        }

        .ai-chat-toggle:hover .ai-ring-outer {
            animation-duration: 1s;
        }

        .ai-chat-toggle:hover .ai-ring-inner {
            animation-duration: 0.75s;
        }

        .ai-chat-toggle:hover .ai-brain-svg {
            filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.8));
        }

        .ai-chat-window {
            position: fixed;
            bottom: 100px;
            right: 24px;
            width: 380px;
            height: 520px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 9998;
            animation: slideUp 0.3s ease;
        }

        .ai-chat-window.show {
            display: flex;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .ai-chat-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ai-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .ai-chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ai-message {
            max-width: 85%;
            animation: fadeIn 0.3s ease;
        }

        .ai-message-bot {
            align-self: flex-start;
        }

        .ai-message-user {
            align-self: flex-end;
        }

        .ai-message .message-content {
            padding: 12px 16px;
            border-radius: 16px;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .ai-message-bot .message-content {
            background: #f1f5f9;
            color: #1e293b;
            border-bottom-left-radius: 4px;
        }

        .ai-message-user .message-content {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .ai-chat-footer {
            padding: 16px;
            border-top: 1px solid #e2e8f0;
            background: white;
        }

        .ai-chat-footer .form-control {
            border-radius: 25px 0 0 25px;
            border: 2px solid #e2e8f0;
            padding: 12px 20px;
            font-size: 0.9rem;
        }

        .ai-chat-footer .form-control:focus {
            border-color: #6366f1;
            box-shadow: none;
        }

        .ai-chat-footer .btn-primary {
            border-radius: 0 25px 25px 0;
            padding: 12px 20px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
        }

        /* Typing Indicator */
        .typing-indicator {
            display: flex;
            gap: 4px;
            padding: 12px 16px;
            background: #f1f5f9;
            border-radius: 16px;
            border-bottom-left-radius: 4px;
            width: fit-content;
        }

        .typing-indicator span {
            width: 8px;
            height: 8px;
            background: #94a3b8;
            border-radius: 50%;
            animation: typingBounce 1.4s infinite ease-in-out;
        }

        .typing-indicator span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: -0.16s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0s;
        }

        @keyframes typingBounce {

            0%,
            80%,
            100% {
                transform: scale(0.8);
                opacity: 0.5;
            }

            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Mobile Responsive */
        @media (max-width: 576px) {
            .ai-chat-window {
                width: calc(100% - 40px);
                right: 20px;
                bottom: 90px;
                height: 450px;
            }

            .ai-chat-toggle {
                bottom: 20px;
                right: 20px;
                width: 55px;
                height: 55px;
            }
        }

        /* Message List Styling */
        .ai-message ul {
            padding-left: 20px;
            font-size: 0.85rem;
        }

        .ai-message ul li {
            margin-bottom: 4px;
        }
    </style>

    <script>
        // AI Chat Widget JavaScript
        document.addEventListener('DOMContentLoaded', function () {
            const chatToggle = document.getElementById('aiChatToggle');
            const chatWindow = document.getElementById('aiChatWindow');
            const chatClose = document.getElementById('aiChatClose');
            const chatInput = document.getElementById('aiChatInput');
            const chatSend = document.getElementById('aiChatSend');
            const chatMessages = document.getElementById('aiChatMessages');

            // Toggle chat window
            chatToggle.addEventListener('click', function () {
                chatWindow.classList.toggle('show');
                chatToggle.classList.toggle('active');
                if (chatWindow.classList.contains('show')) {
                    chatInput.focus();
                }
            });

            // Close chat window
            chatClose.addEventListener('click', function () {
                chatWindow.classList.remove('show');
                chatToggle.classList.remove('active');
            });

            // Send message on button click
            chatSend.addEventListener('click', sendMessage);

            // Send message on Enter key
            chatInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });

            function sendMessage() {
                const message = chatInput.value.trim();
                if (!message) return;

                // Add user message to chat
                addMessage(message, 'user');
                chatInput.value = '';

                // Show typing indicator
                showTypingIndicator();

                // Send to AI endpoint
                fetch('<?= site_url('Munu/ai_chat') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'message=' + encodeURIComponent(message)
                })
                    .then(response => response.json())
                    .then(data => {
                        hideTypingIndicator();
                        if (data.success) {
                            addMessage(data.message, 'bot');
                        } else {
                            addMessage('❌ ' + (data.message || 'Bir hata oluştu'), 'bot');
                        }
                    })
                    .catch(error => {
                        hideTypingIndicator();
                        console.error('AI Chat Error:', error);
                        addMessage('❌ Bağlantı hatası. Lütfen tekrar deneyin.', 'bot');
                    });
            }

            function addMessage(content, type) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `ai-message ai-message-${type}`;

                // Convert markdown-like formatting
                let formattedContent = content
                    .replace(/\n/g, '<br>')
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>');

                messageDiv.innerHTML = `<div class="message-content">${formattedContent}</div>`;
                chatMessages.appendChild(messageDiv);

                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function showTypingIndicator() {
                const typingDiv = document.createElement('div');
                typingDiv.id = 'typingIndicator';
                typingDiv.className = 'ai-message ai-message-bot';
                typingDiv.innerHTML = `
            <div class="typing-indicator">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
                chatMessages.appendChild(typingDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function hideTypingIndicator() {
                const typingDiv = document.getElementById('typingIndicator');
                if (typingDiv) {
                    typingDiv.remove();
                }
            }
        });

        // Dashboard AI Chat Functions
        function sendDashboardAiMessage() {
            const input = document.getElementById('dashboardAiInput');
            const message = input.value.trim();

            if (!message) return;

            input.value = '';
            input.placeholder = 'Yanıt bekleniyor...';
            input.disabled = true;

            // Send to AI endpoint
            fetch('<?= site_url('Munu/ai_chat') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'message=' + encodeURIComponent(message)
            })
                .then(response => response.json())
                .then(data => {
                    input.disabled = false;
                    input.placeholder = 'Hızlı soru sorun...';

                    if (data.success) {
                        // Show response in a modal or alert
                        showAiResponse(message, data.message);
                    } else {
                        alert('❌ Hata: ' + (data.message || 'Bir hata oluştu'));
                    }
                })
                .catch(error => {
                    input.disabled = false;
                    input.placeholder = 'Hızlı soru sorun...';
                    alert('❌ Bağlantı hatası. Lütfen tekrar deneyin.');
                });
        }

        // Show AI Response in a nice modal
        function showAiResponse(question, answer) {
            // Format the answer
            let formattedAnswer = answer.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            // Create modal HTML
            const modalHtml = `
                <div class="modal fade" id="aiResponseModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
                            <div class="modal-header text-white" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-brain fa-lg me-3"></i>
                                    <h5 class="modal-title mb-0">MuNu AI Asistan</h5>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="mb-3 p-3 rounded-3" style="background: #f1f5f9;">
                                    <small class="text-muted d-block mb-1"><i class="fa-solid fa-user me-1"></i> Siz</small>
                                    <p class="mb-0">${escapeHtml(question)}</p>
                                </div>
                                <div class="p-3 rounded-3" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%); border-left: 3px solid #6366f1;">
                                    <small class="text-primary d-block mb-1"><i class="fa-solid fa-robot me-1"></i> AI Asistan</small>
                                    <div>${formattedAnswer}</div>
                                </div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('aiResponseModal').remove(); openAiChat();">
                                    <i class="fa-solid fa-comments me-1"></i> Konuşmaya Devam Et
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if any
            const existingModal = document.getElementById('aiResponseModal');
            if (existingModal) existingModal.remove();

            // Add modal to body and show
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('aiResponseModal'));
            modal.show();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Open floating AI chat
        function openAiChat() {
            const aiWidget = document.getElementById('ai-chat-widget');
            if (aiWidget) {
                aiWidget.style.display = 'block';
            }
        }

        // Request detailed analysis
        function requestDetailedAnalysis() {
            const input = document.getElementById('dashboardAiInput');
            input.value = 'İşletmemin detaylı finansal analizini yap. Alacaklar, borçlar, nakit akışı ve öneriler ver.';
            sendDashboardAiMessage();
        }
    </script>

</body>

</html>