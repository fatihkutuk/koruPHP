<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* @web/user/scada.html.twig */
class __TwigTemplate_c4ba994f7c0763e5013e024ab1f558b0 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "@web/layouts/app.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $this->parent = $this->load("@web/layouts/app.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 4
        yield "<div class=\"page-header\">
    <h2>";
        // line 5
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("title", $context)) ? (Twig\Extension\CoreExtension::default(($context["title"] ?? null), "SCADA Panel")) : ("SCADA Panel")), "html", null, true);
        yield "</h2>
    <div class=\"scada-status\">
        <span class=\"status-indicator online\"></span>
        <span>Sistem Aktif - ";
        // line 8
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate("now", "d.m.Y H:i:s"), "html", null, true);
        yield "</span>
    </div>
</div>

<div class=\"scada-dashboard\">
    <!-- İstatistik Kartları -->
    <div class=\"stats-grid\">
        <div class=\"stat-card\">
            <h3>Toplam Sensör</h3>
            <div class=\"stat-value\">";
        // line 17
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["stats"] ?? null), "total_sensors", [], "any", true, true, false, 17)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["stats"] ?? null), "total_sensors", [], "any", false, false, false, 17), 0)) : (0)), "html", null, true);
        yield "</div>
        </div>
        <div class=\"stat-card\">
            <h3>Aktif Sensör</h3>
            <div class=\"stat-value\">";
        // line 21
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["stats"] ?? null), "active_sensors", [], "any", true, true, false, 21)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["stats"] ?? null), "active_sensors", [], "any", false, false, false, 21), 0)) : (0)), "html", null, true);
        yield "</div>
        </div>
        <div class=\"stat-card\">
            <h3>Kritik Uyarı</h3>
            <div class=\"stat-value critical\">";
        // line 25
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["stats"] ?? null), "critical_alerts", [], "any", true, true, false, 25)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["stats"] ?? null), "critical_alerts", [], "any", false, false, false, 25), 0)) : (0)), "html", null, true);
        yield "</div>
        </div>
        <div class=\"stat-card\">
            <h3>Son Güncelleme</h3>
            <div class=\"stat-value small\">";
        // line 29
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["stats"] ?? null), "last_update", [], "any", true, true, false, 29)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["stats"] ?? null), "last_update", [], "any", false, false, false, 29), "Bilinmiyor")) : ("Bilinmiyor")), "html", null, true);
        yield "</div>
        </div>
    </div>

    <!-- SCADA Gauges -->
    <div class=\"gauge-container\">
        <div class=\"scada-gauge\" data-value=\"";
        // line 35
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "temperature", [], "any", true, true, false, 35)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "temperature", [], "any", false, false, false, 35), 0)) : (0)), "html", null, true);
        yield "\" data-max=\"50\" data-unit=\"°C\">
            <div class=\"gauge-circle\">
                <div class=\"gauge-needle\"></div>
            </div>
            <div class=\"gauge-value\">
                <span class=\"value\">";
        // line 40
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "temperature", [], "any", true, true, false, 40)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "temperature", [], "any", false, false, false, 40), 0)) : (0)), "html", null, true);
        yield "</span>
                <span class=\"unit\">°C</span>
            </div>
            <div class=\"gauge-label\">Sıcaklık</div>
        </div>

        <div class=\"scada-gauge\" data-value=\"";
        // line 46
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "pressure", [], "any", true, true, false, 46)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "pressure", [], "any", false, false, false, 46), 0)) : (0)), "html", null, true);
        yield "\" data-max=\"300\" data-unit=\"bar\">
            <div class=\"gauge-circle\">
                <div class=\"gauge-needle\"></div>
            </div>
            <div class=\"gauge-value\">
                <span class=\"value\">";
        // line 51
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "pressure", [], "any", true, true, false, 51)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "pressure", [], "any", false, false, false, 51), 0)) : (0)), "html", null, true);
        yield "</span>
                <span class=\"unit\">bar</span>
            </div>
            <div class=\"gauge-label\">Basınç</div>
        </div>

        <div class=\"scada-gauge\" data-value=\"";
        // line 57
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "humidity", [], "any", true, true, false, 57)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "humidity", [], "any", false, false, false, 57), 0)) : (0)), "html", null, true);
        yield "\" data-max=\"100\" data-unit=\"%\">
            <div class=\"gauge-circle\">
                <div class=\"gauge-needle\"></div>
            </div>
            <div class=\"gauge-value\">
                <span class=\"value\">";
        // line 62
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "humidity", [], "any", true, true, false, 62)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "humidity", [], "any", false, false, false, 62), 0)) : (0)), "html", null, true);
        yield "</span>
                <span class=\"unit\">%</span>
            </div>
            <div class=\"gauge-label\">Nem</div>
        </div>

        <div class=\"scada-gauge\" data-value=\"";
        // line 68
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "flow_rate", [], "any", true, true, false, 68)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "flow_rate", [], "any", false, false, false, 68), 0)) : (0)), "html", null, true);
        yield "\" data-max=\"200\" data-unit=\"L/min\">
            <div class=\"gauge-circle\">
                <div class=\"gauge-needle\"></div>
            </div>
            <div class=\"gauge-value\">
                <span class=\"value\">";
        // line 73
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "flow_rate", [], "any", true, true, false, 73)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["gaugeData"] ?? null), "flow_rate", [], "any", false, false, false, 73), 0)) : (0)), "html", null, true);
        yield "</span>
                <span class=\"unit\">L/min</span>
            </div>
            <div class=\"gauge-label\">Akış Hızı</div>
        </div>
    </div>

    <!-- Canlı Grafik (JavaScript ile güncellenecek) -->
    <div class=\"chart-container\">
        <h3>Canlı Sensör Grafikleri</h3>
        <div class=\"charts-grid\">
            <div class=\"chart-card\">
                <canvas id=\"temperatureChart\" width=\"400\" height=\"200\"></canvas>
            </div>
            <div class=\"chart-card\">
                <canvas id=\"pressureChart\" width=\"400\" height=\"200\"></canvas>
            </div>
        </div>
    </div>

    <!-- Sensör Verileri Tablosu -->
    ";
        // line 94
        if ((($tmp =  !Twig\Extension\CoreExtension::testEmpty(($context["sensorData"] ?? null))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 95
            yield "        <div class=\"sensor-data-table\">
            <h3>Son Sensör Verileri</h3>
            <table class=\"data-table\">
                <thead>
                    <tr>
                        <th>Sensör ID</th>
                        <th>Tip</th>
                        <th>Değer</th>
                        <th>Durum</th>
                        <th>Lokasyon</th>
                        <th>Zaman</th>
                    </tr>
                </thead>
                <tbody>
                    ";
            // line 109
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["sensorData"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["sensor"]) {
                // line 110
                yield "                        <tr class=\"";
                if ((CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "status", [], "any", false, false, false, 110) == "critical")) {
                    yield "row-critical";
                } elseif ((CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "status", [], "any", false, false, false, 110) == "warning")) {
                    yield "row-warning";
                }
                yield "\">
                            <td><strong>";
                // line 111
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "sensor_id", [], "any", false, false, false, 111), "html", null, true);
                yield "</strong></td>
                            <td>";
                // line 112
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::titleCase($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "sensor_type", [], "any", false, false, false, 112)), "html", null, true);
                yield "</td>
                            <td>";
                // line 113
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "value", [], "any", false, false, false, 113), "html", null, true);
                yield " ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "unit", [], "any", false, false, false, 113), "html", null, true);
                yield "</td>
                            <td>
                                <span class=\"status status-";
                // line 115
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "status", [], "any", false, false, false, 115), "html", null, true);
                yield "\">
                                    ";
                // line 116
                if ((CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "status", [], "any", false, false, false, 116) == "critical")) {
                    yield "🔴";
                } elseif ((CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "status", [], "any", false, false, false, 116) == "warning")) {
                    yield "🟡";
                } else {
                    yield "🟢";
                }
                // line 117
                yield "                                    ";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::titleCase($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "status", [], "any", false, false, false, 117)), "html", null, true);
                yield "
                                </span>
                            </td>
                            <td>";
                // line 120
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "location", [], "any", true, true, false, 120)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "location", [], "any", false, false, false, 120), "Bilinmiyor")) : ("Bilinmiyor")), "html", null, true);
                yield "</td>
                            <td>";
                // line 121
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["sensor"], "created_at", [], "any", false, false, false, 121), "html", null, true);
                yield "</td>
                        </tr>
                    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['sensor'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 124
            yield "                </tbody>
            </table>
        </div>
    ";
        } else {
            // line 128
            yield "        <div class=\"alert alert-info\">
            <p>Henüz sensör verisi bulunmuyor.</p>
        </div>
    ";
        }
        // line 132
        yield "
    <!-- Sistem Durumu -->
    <div class=\"system-status\">
        <h3>Sistem Durumu</h3>
        <div class=\"status-grid\">
            <div class=\"status-item\">
                <span class=\"status-indicator online\"></span>
                <span>Veritabanı Bağlantısı</span>
            </div>
            <div class=\"status-item\">
                <span class=\"status-indicator online\"></span>
                <span>Sensör Ağı</span>
            </div>
            <div class=\"status-item\">
                <span class=\"status-indicator ";
        // line 146
        yield (((CoreExtension::getAttribute($this->env, $this->source, ($context["stats"] ?? null), "critical_alerts", [], "any", false, false, false, 146) > 0)) ? ("critical") : ("online"));
        yield "\"></span>
                <span>Alarm Sistemi</span>
            </div>
            <div class=\"status-item\">
                <span class=\"status-indicator online\"></span>
                <span>Backup Sistemi</span>
            </div>
        </div>
    </div>
</div>

<script>
// SCADA Panel için ek JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Gauge değerlerini güncelle
    updateGauges();
    
    // Her 5 saniyede bir güncelle
    setInterval(function() {
        updateGauges();
    }, 5000);
    
    function updateGauges() {
        document.querySelectorAll('.scada-gauge').forEach(function(gauge) {
            const value = parseFloat(gauge.dataset.value);
            const max = parseFloat(gauge.dataset.max);
            const percentage = (value / max) * 100;
            
            const needle = gauge.querySelector('.gauge-needle');
            if (needle) {
                const rotation = (percentage / 100) * 180 - 90;
                needle.style.transform = `rotate(\${rotation}deg)`;
            }
        });
    }
});
</script>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@web/user/scada.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  298 => 146,  282 => 132,  276 => 128,  270 => 124,  261 => 121,  257 => 120,  250 => 117,  242 => 116,  238 => 115,  231 => 113,  227 => 112,  223 => 111,  214 => 110,  210 => 109,  194 => 95,  192 => 94,  168 => 73,  160 => 68,  151 => 62,  143 => 57,  134 => 51,  126 => 46,  117 => 40,  109 => 35,  100 => 29,  93 => 25,  86 => 21,  79 => 17,  67 => 8,  61 => 5,  58 => 4,  51 => 3,  40 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"@web/layouts/app.html.twig\" %}

{% block content %}
<div class=\"page-header\">
    <h2>{{ title|default('SCADA Panel') }}</h2>
    <div class=\"scada-status\">
        <span class=\"status-indicator online\"></span>
        <span>Sistem Aktif - {{ \"now\"|date(\"d.m.Y H:i:s\") }}</span>
    </div>
</div>

<div class=\"scada-dashboard\">
    <!-- İstatistik Kartları -->
    <div class=\"stats-grid\">
        <div class=\"stat-card\">
            <h3>Toplam Sensör</h3>
            <div class=\"stat-value\">{{ stats.total_sensors|default(0) }}</div>
        </div>
        <div class=\"stat-card\">
            <h3>Aktif Sensör</h3>
            <div class=\"stat-value\">{{ stats.active_sensors|default(0) }}</div>
        </div>
        <div class=\"stat-card\">
            <h3>Kritik Uyarı</h3>
            <div class=\"stat-value critical\">{{ stats.critical_alerts|default(0) }}</div>
        </div>
        <div class=\"stat-card\">
            <h3>Son Güncelleme</h3>
            <div class=\"stat-value small\">{{ stats.last_update|default('Bilinmiyor') }}</div>
        </div>
    </div>

    <!-- SCADA Gauges -->
    <div class=\"gauge-container\">
        <div class=\"scada-gauge\" data-value=\"{{ gaugeData.temperature|default(0) }}\" data-max=\"50\" data-unit=\"°C\">
            <div class=\"gauge-circle\">
                <div class=\"gauge-needle\"></div>
            </div>
            <div class=\"gauge-value\">
                <span class=\"value\">{{ gaugeData.temperature|default(0) }}</span>
                <span class=\"unit\">°C</span>
            </div>
            <div class=\"gauge-label\">Sıcaklık</div>
        </div>

        <div class=\"scada-gauge\" data-value=\"{{ gaugeData.pressure|default(0) }}\" data-max=\"300\" data-unit=\"bar\">
            <div class=\"gauge-circle\">
                <div class=\"gauge-needle\"></div>
            </div>
            <div class=\"gauge-value\">
                <span class=\"value\">{{ gaugeData.pressure|default(0) }}</span>
                <span class=\"unit\">bar</span>
            </div>
            <div class=\"gauge-label\">Basınç</div>
        </div>

        <div class=\"scada-gauge\" data-value=\"{{ gaugeData.humidity|default(0) }}\" data-max=\"100\" data-unit=\"%\">
            <div class=\"gauge-circle\">
                <div class=\"gauge-needle\"></div>
            </div>
            <div class=\"gauge-value\">
                <span class=\"value\">{{ gaugeData.humidity|default(0) }}</span>
                <span class=\"unit\">%</span>
            </div>
            <div class=\"gauge-label\">Nem</div>
        </div>

        <div class=\"scada-gauge\" data-value=\"{{ gaugeData.flow_rate|default(0) }}\" data-max=\"200\" data-unit=\"L/min\">
            <div class=\"gauge-circle\">
                <div class=\"gauge-needle\"></div>
            </div>
            <div class=\"gauge-value\">
                <span class=\"value\">{{ gaugeData.flow_rate|default(0) }}</span>
                <span class=\"unit\">L/min</span>
            </div>
            <div class=\"gauge-label\">Akış Hızı</div>
        </div>
    </div>

    <!-- Canlı Grafik (JavaScript ile güncellenecek) -->
    <div class=\"chart-container\">
        <h3>Canlı Sensör Grafikleri</h3>
        <div class=\"charts-grid\">
            <div class=\"chart-card\">
                <canvas id=\"temperatureChart\" width=\"400\" height=\"200\"></canvas>
            </div>
            <div class=\"chart-card\">
                <canvas id=\"pressureChart\" width=\"400\" height=\"200\"></canvas>
            </div>
        </div>
    </div>

    <!-- Sensör Verileri Tablosu -->
    {% if sensorData is not empty %}
        <div class=\"sensor-data-table\">
            <h3>Son Sensör Verileri</h3>
            <table class=\"data-table\">
                <thead>
                    <tr>
                        <th>Sensör ID</th>
                        <th>Tip</th>
                        <th>Değer</th>
                        <th>Durum</th>
                        <th>Lokasyon</th>
                        <th>Zaman</th>
                    </tr>
                </thead>
                <tbody>
                    {% for sensor in sensorData %}
                        <tr class=\"{% if sensor.status == 'critical' %}row-critical{% elseif sensor.status == 'warning' %}row-warning{% endif %}\">
                            <td><strong>{{ sensor.sensor_id }}</strong></td>
                            <td>{{ sensor.sensor_type|title }}</td>
                            <td>{{ sensor.value }} {{ sensor.unit }}</td>
                            <td>
                                <span class=\"status status-{{ sensor.status }}\">
                                    {% if sensor.status == 'critical' %}🔴{% elseif sensor.status == 'warning' %}🟡{% else %}🟢{% endif %}
                                    {{ sensor.status|title }}
                                </span>
                            </td>
                            <td>{{ sensor.location|default('Bilinmiyor') }}</td>
                            <td>{{ sensor.created_at }}</td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    {% else %}
        <div class=\"alert alert-info\">
            <p>Henüz sensör verisi bulunmuyor.</p>
        </div>
    {% endif %}

    <!-- Sistem Durumu -->
    <div class=\"system-status\">
        <h3>Sistem Durumu</h3>
        <div class=\"status-grid\">
            <div class=\"status-item\">
                <span class=\"status-indicator online\"></span>
                <span>Veritabanı Bağlantısı</span>
            </div>
            <div class=\"status-item\">
                <span class=\"status-indicator online\"></span>
                <span>Sensör Ağı</span>
            </div>
            <div class=\"status-item\">
                <span class=\"status-indicator {{ stats.critical_alerts > 0 ? 'critical' : 'online' }}\"></span>
                <span>Alarm Sistemi</span>
            </div>
            <div class=\"status-item\">
                <span class=\"status-indicator online\"></span>
                <span>Backup Sistemi</span>
            </div>
        </div>
    </div>
</div>

<script>
// SCADA Panel için ek JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Gauge değerlerini güncelle
    updateGauges();
    
    // Her 5 saniyede bir güncelle
    setInterval(function() {
        updateGauges();
    }, 5000);
    
    function updateGauges() {
        document.querySelectorAll('.scada-gauge').forEach(function(gauge) {
            const value = parseFloat(gauge.dataset.value);
            const max = parseFloat(gauge.dataset.max);
            const percentage = (value / max) * 100;
            
            const needle = gauge.querySelector('.gauge-needle');
            if (needle) {
                const rotation = (percentage / 100) * 180 - 90;
                needle.style.transform = `rotate(\${rotation}deg)`;
            }
        });
    }
});
</script>
{% endblock %}", "@web/user/scada.html.twig", "C:\\koruPHP\\modules\\web\\views\\user\\scada.html.twig");
    }
}
