@extends('web.layouts.app')

@section('content')
<div class="test-dashboard">
    <div class="page-header">
        <h2>{{ $title }}</h2>
        <p>Framework testlerini buradan çalıştırabilirsiniz.</p>
    </div>

    <div class="test-grid">
        <div class="test-card">
            <h3>🔍 Temel Testler</h3>
            <div class="test-buttons">
                <a href="/test/database" class="btn btn-primary">Veritabanı Testi</a>
                <a href="/test/log" class="btn btn-info">Log Testi</a>
                <a href="/test/error" class="btn btn-warning">Hata Testi</a>
            </div>
        </div>

        <div class="test-card">
            <h3>🔄 Gelişmiş Testler</h3>
            <div class="test-buttons">
                <a href="/test/transaction" class="btn btn-success">Transaction Testi</a>
                <a href="/test/bulk" class="btn btn-secondary">Toplu İşlem Testi</a>
                <a href="/test/procedure" class="btn btn-primary">Stored Procedure Testi</a>
            </div>
        </div>

        <div class="test-card">
            <h3>📊 SCADA Testleri</h3>
            <div class="test-buttons">
                <a href="/users/scada/panel" class="btn btn-info">SCADA Panel</a>
                <a href="/api/sensor-data" class="btn btn-success">Sensör Verileri</a>
            </div>
        </div>

        <div class="test-card">
            <h3>👥 Kullanıcı Modülü</h3>
            <div class="test-buttons">
                <a href="/users" class="btn btn-primary">Kullanıcı Listesi</a>
                <a href="/users/1" class="btn btn-info">Kullanıcı Detayı</a>
                <a href="/api/users" class="btn btn-success">API Kullanıcılar</a>
            </div>
        </div>
    </div>

    <div class="test-info">
        <h3>📋 Test Bilgileri</h3>
        <ul>
            <li><strong>Ortam:</strong> {{ \Koru\Environment::get() }}</li>
            <li><strong>Debug Modu:</strong> {{ \Koru\Environment::isDebugging() ? 'Aktif' : 'Pasif' }}</li>
            <li><strong>Veritabanı:</strong> {{ config('DEFAULT_DB_DATABASE') }}</li>
            <li><strong>Log Seviyesi:</strong> {{ config('LOG_LEVEL') }}</li>
        </ul>
    </div>
</div>

<style>
.test-dashboard {
    padding: 20px;
}

.test-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.test-card {
    background: rgba(255,255,255,0.05);
    padding: 20px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.1);
}

.test-card h3 {
    margin-bottom: 15px;
    color: #00d4ff;
}

.test-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.test-buttons .btn {
    text-align: center;
    padding: 10px 15px;
}

.test-info {
    background: rgba(0, 212, 255, 0.1);
    padding: 20px;
    border-radius: 10px;
    margin-top: 30px;
}

.test-info ul {
    list-style: none;
    padding: 0;
}

.test-info li {
    padding: 5px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
</style>
@endsection