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

/* @web/user/show.html.twig */
class __TwigTemplate_a823b0694ff9b78cf7cb628a9f2d97bb extends Template
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
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("title", $context)) ? (Twig\Extension\CoreExtension::default(($context["title"] ?? null), "Kullanıcı Detayı")) : ("Kullanıcı Detayı")), "html", null, true);
        yield "</h2>
    <a href=\"";
        // line 6
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('url')->getCallable()("/users"), "html", null, true);
        yield "\" class=\"btn btn-secondary\">← Geri Dön</a>
</div>

<div class=\"user-detail\">
    <div class=\"user-profile\">
        <div class=\"profile-avatar\">
            <img src=\"";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('asset')->getCallable()(("images/" . ((CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "avatar", [], "any", true, true, false, 12)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "avatar", [], "any", false, false, false, 12), "default.jpg")) : ("default.jpg")))), "html", null, true);
        yield "\" 
                 alt=\"";
        // line 13
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "name", [], "any", false, false, false, 13), "html", null, true);
        yield "\">
        </div>
        <div class=\"profile-info\">
            <h1>";
        // line 16
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "name", [], "any", false, false, false, 16), "html", null, true);
        yield "</h1>
            <p class=\"user-email\">";
        // line 17
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "email", [], "any", false, false, false, 17), "html", null, true);
        yield "</p>
            <p class=\"user-status\">
                <span class=\"status status-";
        // line 19
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "status", [], "any", false, false, false, 19), "html", null, true);
        yield "\">";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "status", [], "any", false, false, false, 19), "html", null, true);
        yield "</span>
            </p>
        </div>
    </div>

    <div class=\"user-details-grid\">
        <div class=\"detail-card\">
            <h3>Kişisel Bilgiler</h3>
            ";
        // line 27
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "bio", [], "any", false, false, false, 27)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 28
            yield "                <p><strong>Bio:</strong> ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "bio", [], "any", false, false, false, 28), "html", null, true);
            yield "</p>
            ";
        }
        // line 30
        yield "            ";
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "phone", [], "any", false, false, false, 30)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 31
            yield "                <p><strong>Telefon:</strong> ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "phone", [], "any", false, false, false, 31), "html", null, true);
            yield "</p>
            ";
        }
        // line 33
        yield "            <p><strong>Kayıt Tarihi:</strong> ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "created_at", [], "any", false, false, false, 33), "html", null, true);
        yield "</p>
            ";
        // line 34
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "last_login", [], "any", false, false, false, 34)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 35
            yield "                <p><strong>Son Giriş:</strong> ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "last_login", [], "any", false, false, false, 35), "html", null, true);
            yield "</p>
            ";
        }
        // line 37
        yield "            <p><strong>Giriş Sayısı:</strong> ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "login_count", [], "any", true, true, false, 37)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "login_count", [], "any", false, false, false, 37), 0)) : (0)), "html", null, true);
        yield "</p>
        </div>

        <div class=\"detail-card\">
            <h3>Hesap Durumu</h3>
            <p><strong>Durum:</strong> ";
        // line 42
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "status", [], "any", false, false, false, 42), "html", null, true);
        yield "</p>
            ";
        // line 43
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "email_verified_at", [], "any", false, false, false, 43)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 44
            yield "                <p><strong>Email Doğrulanma:</strong> ✅ ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, ($context["user"] ?? null), "email_verified_at", [], "any", false, false, false, 44), "html", null, true);
            yield "</p>
            ";
        } else {
            // line 46
            yield "                <p><strong>Email Doğrulanma:</strong> ❌ Doğrulanmamış</p>
            ";
        }
        // line 48
        yield "        </div>
    </div>
</div>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@web/user/show.html.twig";
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
        return array (  161 => 48,  157 => 46,  151 => 44,  149 => 43,  145 => 42,  136 => 37,  130 => 35,  128 => 34,  123 => 33,  117 => 31,  114 => 30,  108 => 28,  106 => 27,  93 => 19,  88 => 17,  84 => 16,  78 => 13,  74 => 12,  65 => 6,  61 => 5,  58 => 4,  51 => 3,  40 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"@web/layouts/app.html.twig\" %}

{% block content %}
<div class=\"page-header\">
    <h2>{{ title|default('Kullanıcı Detayı') }}</h2>
    <a href=\"{{ url('/users') }}\" class=\"btn btn-secondary\">← Geri Dön</a>
</div>

<div class=\"user-detail\">
    <div class=\"user-profile\">
        <div class=\"profile-avatar\">
            <img src=\"{{ asset('images/' ~ (user.avatar|default('default.jpg'))) }}\" 
                 alt=\"{{ user.name }}\">
        </div>
        <div class=\"profile-info\">
            <h1>{{ user.name }}</h1>
            <p class=\"user-email\">{{ user.email }}</p>
            <p class=\"user-status\">
                <span class=\"status status-{{ user.status }}\">{{ user.status }}</span>
            </p>
        </div>
    </div>

    <div class=\"user-details-grid\">
        <div class=\"detail-card\">
            <h3>Kişisel Bilgiler</h3>
            {% if user.bio %}
                <p><strong>Bio:</strong> {{ user.bio }}</p>
            {% endif %}
            {% if user.phone %}
                <p><strong>Telefon:</strong> {{ user.phone }}</p>
            {% endif %}
            <p><strong>Kayıt Tarihi:</strong> {{ user.created_at }}</p>
            {% if user.last_login %}
                <p><strong>Son Giriş:</strong> {{ user.last_login }}</p>
            {% endif %}
            <p><strong>Giriş Sayısı:</strong> {{ user.login_count|default(0) }}</p>
        </div>

        <div class=\"detail-card\">
            <h3>Hesap Durumu</h3>
            <p><strong>Durum:</strong> {{ user.status }}</p>
            {% if user.email_verified_at %}
                <p><strong>Email Doğrulanma:</strong> ✅ {{ user.email_verified_at }}</p>
            {% else %}
                <p><strong>Email Doğrulanma:</strong> ❌ Doğrulanmamış</p>
            {% endif %}
        </div>
    </div>
</div>
{% endblock %}", "@web/user/show.html.twig", "C:\\koruPHP\\modules\\web\\views\\user\\show.html.twig");
    }
}
