class ScadaPanel {
    constructor() {
        this.gauges = document.querySelectorAll('.scada-gauge');
        this.updateInterval = 5000; // 5 saniye
        this.init();
    }
    
    init() {
        this.updateGauges();
        this.startRealTimeUpdates();
        this.addEventListeners();
    }
    
    updateGauges() {
        this.gauges.forEach(gauge => {
            const value = parseFloat(gauge.dataset.value) || 0;
            const max = parseFloat(gauge.dataset.max) || 100;
            const percentage = (value / max) * 100;
            
            // Gauge progress güncellemesi
            const circle = gauge.querySelector('.gauge-circle');
            if (circle) {
                circle.style.setProperty('--gauge-progress', `${percentage}%`);
            }
            
            // Needle rotasyonu
            const needle = gauge.querySelector('.gauge-needle');
            if (needle) {
                const rotation = (percentage / 100) * 180 - 90;
                needle.style.transform = `rotate(${rotation}deg)`;
            }
            
            // Değer animasyonu
            this.animateValue(gauge, value);
        });
    }
    
    animateValue(gauge, targetValue) {
        const valueElement = gauge.querySelector('.value');
        if (!valueElement) return;
        
        const currentValue = parseFloat(valueElement.textContent) || 0;
        const increment = (targetValue - currentValue) / 20;
        let current = currentValue;
        
        const timer = setInterval(() => {
            current += increment;
            
            if ((increment > 0 && current >= targetValue) || 
                (increment < 0 && current <= targetValue)) {
                current = targetValue;
                clearInterval(timer);
            }
            
            valueElement.textContent = Math.round(current * 10) / 10;
        }, 50);
    }
    
    startRealTimeUpdates() {
        setInterval(() => {
            this.fetchLatestData();
        }, this.updateInterval);
    }
    
    async fetchLatestData() {
        try {
            // Gerçek uygulamada API'den veri çekilir
            // Bu örnekte random veri üretiyoruz
            this.gauges.forEach(gauge => {
                const max = parseFloat(gauge.dataset.max) || 100;
                const newValue = Math.random() * max;
                gauge.dataset.value = newValue.toFixed(1);
            });
            
            this.updateGauges();
            this.updateStatusIndicators();
        } catch (error) {
            console.error('Veri güncellenirken hata oluştu:', error);
        }
    }
    
    updateStatusIndicators() {
        const indicators = document.querySelectorAll('.status-indicator');
        indicators.forEach(indicator => {
            // Random olarak online/offline durumu
            const isOnline = Math.random() > 0.1; // %90 online şansı
            indicator.className = `status-indicator ${isOnline ? 'online' : 'offline'}`;
        });
    }
    
    addEventListeners() {
        // Gauge'lere tıklama olayı
        this.gauges.forEach(gauge => {
            gauge.addEventListener('click', () => {
                this.showGaugeDetails(gauge);
            });
        });
        
        // Klavye kısayolları
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                this.fetchLatestData();
            }
        });
    }
    
    showGaugeDetails(gauge) {
        const value = gauge.dataset.value;
        const unit = gauge.dataset.unit || '';
        const label = gauge.querySelector('.gauge-label').textContent;
        
        alert(`${label}: ${value}${unit}`);
    }
}

// Sayfa yüklendiğinde SCADA panelini başlat
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.scada-gauge')) {
        new ScadaPanel();
    }
    
    // Genel UI iyileştirmeleri
    initializeUI();
});

function initializeUI() {
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Loading animasyonları
    const cards = document.querySelectorAll('.user-card, .scada-panel');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// WebSocket desteği (opsiyonel)
class WebSocketManager {
    constructor(url) {
        this.url = url;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.connect();
    }
    
    connect() {
        try {
            this.ws = new WebSocket(this.url);
            
            this.ws.onopen = () => {
                console.log('WebSocket bağlantısı kuruldu');
                this.reconnectAttempts = 0;
            };
            
            this.ws.onmessage = (event) => {
                const data = JSON.parse(event.data);
                this.handleMessage(data);
            };
            
            this.ws.onclose = () => {
                console.log('WebSocket bağlantısı kapandı');
                this.reconnect();
            };
            
            this.ws.onerror = (error) => {
                console.error('WebSocket hatası:', error);
            };
        } catch (error) {
            console.error('WebSocket bağlantı hatası:', error);
        }
    }
    
    reconnect() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            setTimeout(() => {
                console.log(`Yeniden bağlanma denemesi ${this.reconnectAttempts}`);
                this.connect();
            }, 1000 * this.reconnectAttempts);
        }
    }
    
    handleMessage(data) {
        // Gelen veriyi işle
        if (data.type === 'sensor_update') {
            this.updateSensorData(data.payload);
        }
    }
    
    updateSensorData(sensorData) {
        Object.keys(sensorData).forEach(sensorId => {
            const gauge = document.querySelector(`[data-sensor-id="${sensorId}"]`);
            if (gauge) {
                gauge.dataset.value = sensorData[sensorId];
            }
        });
    }
}