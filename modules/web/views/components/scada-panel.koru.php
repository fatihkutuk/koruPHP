<div class="scada-panel">
    <div class="panel-header">
        <h3>{{ $title }}</h3>
        <div class="status-indicator {{ $status }}"></div>
    </div>
    
    <div class="panel-content">
        @include('web.components.scada-gauge', [
            'value' => $temperature ?? 0,
            'max' => 50,
            'unit' => '°C',
            'label' => 'Sıcaklık'
        ])
        
        @include('web.components.scada-gauge', [
            'value' => $pressure ?? 0,
            'max' => 300,
            'unit' => 'bar',
            'label' => 'Basınç'
        ])
        
        @include('web.components.scada-gauge', [
            'value' => $humidity ?? 0,
            'max' => 100,
            'unit' => '%',
            'label' => 'Nem'
        ])
    </div>
</div>