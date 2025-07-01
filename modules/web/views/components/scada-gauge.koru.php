<div class="scada-gauge" data-value="{{ $value }}" data-max="{{ $max ?? 100 }}" data-unit="{{ $unit ?? '' }}">
    <div class="gauge-container">
        <div class="gauge-circle">
            <div class="gauge-needle" style="transform: rotate({{ ($value / ($max ?? 100)) * 180 - 90 }}deg);"></div>
        </div>
        <div class="gauge-value">
            <span class="value">{{ $value }}</span>
            <span class="unit">{{ $unit ?? '' }}</span>
        </div>
    </div>
    <div class="gauge-label">{{ $label }}</div>
</div>