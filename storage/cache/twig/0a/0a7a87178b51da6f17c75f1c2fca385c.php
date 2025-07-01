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

/* @web/user/index.html.twig */
class __TwigTemplate_af1564842e8f237eccfda3a4d71d9d64 extends Template
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
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("title", $context)) ? (Twig\Extension\CoreExtension::default(($context["title"] ?? null), "Kullanıcı Listesi")) : ("Kullanıcı Listesi")), "html", null, true);
        yield "</h2>
    <p>Toplam ";
        // line 6
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::length($this->env->getCharset(), ($context["users"] ?? null)), "html", null, true);
        yield " kullanıcı bulundu</p>
</div>

";
        // line 9
        if (Twig\Extension\CoreExtension::testEmpty(($context["users"] ?? null))) {
            // line 10
            yield "    <div class=\"alert alert-info\">
        <p>Henüz kullanıcı bulunamadı.</p>
        <p><a href=\"";
            // line 12
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('url')->getCallable()("/test"), "html", null, true);
            yield "\">Test Sayfasına Dön</a></p>
    </div>
";
        } else {
            // line 15
            yield "    <div class=\"user-grid\">
        ";
            // line 16
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(($context["users"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["user"]) {
                // line 17
                yield "            <div class=\"user-card\">
                <div class=\"user-avatar\">
                    <img src=\"";
                // line 19
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('asset')->getCallable()(("images/" . ((CoreExtension::getAttribute($this->env, $this->source, $context["user"], "avatar", [], "any", true, true, false, 19)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "avatar", [], "any", false, false, false, 19), "default.jpg")) : ("default.jpg")))), "html", null, true);
                yield "\" 
                         alt=\"";
                // line 20
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "name", [], "any", false, false, false, 20), "html", null, true);
                yield "\" 
                         onerror=\"this.src='";
                // line 21
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('asset')->getCallable()("images/default.jpg"), "html", null, true);
                yield "'\">
                </div>
                <h3>";
                // line 23
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "name", [], "any", false, false, false, 23), "html", null, true);
                yield "</h3>
                <p><strong>Email:</strong> ";
                // line 24
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "email", [], "any", false, false, false, 24), "html", null, true);
                yield "</p>
                <p><strong>Durum:</strong> 
                    <span class=\"status status-";
                // line 26
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "status", [], "any", false, false, false, 26), "html", null, true);
                yield "\">";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "status", [], "any", false, false, false, 26), "html", null, true);
                yield "</span>
                </p>
                ";
                // line 28
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["user"], "bio", [], "any", false, false, false, 28)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 29
                    yield "                    <p><strong>Bio:</strong> ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::slice($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["user"], "bio", [], "any", false, false, false, 29), 0, 50), "html", null, true);
                    yield "...</p>
                ";
                }
                // line 31
                yield "                ";
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["user"], "phone", [], "any", false, false, false, 31)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 32
                    yield "                    <p><strong>Telefon:</strong> ";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["user"], "phone", [], "any", false, false, false, 32), "html", null, true);
                    yield "</p>
                ";
                }
                // line 34
                yield "                <div class=\"user-actions\">
                    <a href=\"";
                // line 35
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('url')->getCallable()(("/users/" . CoreExtension::getAttribute($this->env, $this->source, $context["user"], "id", [], "any", false, false, false, 35))), "html", null, true);
                yield "\" class=\"btn btn-primary\">Detay Görüntüle</a>
                </div>
            </div>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['user'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 39
            yield "    </div>
";
        }
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@web/user/index.html.twig";
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
        return array (  153 => 39,  143 => 35,  140 => 34,  134 => 32,  131 => 31,  125 => 29,  123 => 28,  116 => 26,  111 => 24,  107 => 23,  102 => 21,  98 => 20,  94 => 19,  90 => 17,  86 => 16,  83 => 15,  77 => 12,  73 => 10,  71 => 9,  65 => 6,  61 => 5,  58 => 4,  51 => 3,  40 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends \"@web/layouts/app.html.twig\" %}

{% block content %}
<div class=\"page-header\">
    <h2>{{ title|default('Kullanıcı Listesi') }}</h2>
    <p>Toplam {{ users|length }} kullanıcı bulundu</p>
</div>

{% if users is empty %}
    <div class=\"alert alert-info\">
        <p>Henüz kullanıcı bulunamadı.</p>
        <p><a href=\"{{ url('/test') }}\">Test Sayfasına Dön</a></p>
    </div>
{% else %}
    <div class=\"user-grid\">
        {% for user in users %}
            <div class=\"user-card\">
                <div class=\"user-avatar\">
                    <img src=\"{{ asset('images/' ~ (user.avatar|default('default.jpg'))) }}\" 
                         alt=\"{{ user.name }}\" 
                         onerror=\"this.src='{{ asset('images/default.jpg') }}'\">
                </div>
                <h3>{{ user.name }}</h3>
                <p><strong>Email:</strong> {{ user.email }}</p>
                <p><strong>Durum:</strong> 
                    <span class=\"status status-{{ user.status }}\">{{ user.status }}</span>
                </p>
                {% if user.bio %}
                    <p><strong>Bio:</strong> {{ user.bio|slice(0, 50) }}...</p>
                {% endif %}
                {% if user.phone %}
                    <p><strong>Telefon:</strong> {{ user.phone }}</p>
                {% endif %}
                <div class=\"user-actions\">
                    <a href=\"{{ url('/users/' ~ user.id) }}\" class=\"btn btn-primary\">Detay Görüntüle</a>
                </div>
            </div>
        {% endfor %}
    </div>
{% endif %}
{% endblock %}", "@web/user/index.html.twig", "C:\\koruPHP\\modules\\web\\views\\user\\index.html.twig");
    }
}
