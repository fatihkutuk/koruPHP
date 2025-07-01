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

/* @web/layouts/app.html.twig */
class __TwigTemplate_beba05e9048c7ee79329991212368dde extends Template
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

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"tr\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>";
        // line 6
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("title", $context)) ? (Twig\Extension\CoreExtension::default(($context["title"] ?? null), "koruPHP")) : ("koruPHP")), "html", null, true);
        yield " - koruPHP</title>
    <link rel=\"stylesheet\" href=\"";
        // line 7
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('asset')->getCallable()("css/scada.css"), "html", null, true);
        yield "\">
</head>
<body>
    <nav class=\"navbar\">
        <div class=\"nav-brand\">
            <h1>koruPHP</h1>
        </div>
        <ul class=\"nav-menu\">
            <li><a href=\"";
        // line 15
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('url')->getCallable()("/"), "html", null, true);
        yield "\">Ana Sayfa</a></li>
            <li><a href=\"";
        // line 16
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('url')->getCallable()("/users"), "html", null, true);
        yield "\">Kullanıcılar</a></li>
            <li><a href=\"";
        // line 17
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('url')->getCallable()("/scada"), "html", null, true);
        yield "\">SCADA Panel</a></li>
            <li><a href=\"";
        // line 18
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('url')->getCallable()("/test"), "html", null, true);
        yield "\">Test</a></li>
        </ul>
    </nav>
    
    <main class=\"container\">
        ";
        // line 23
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 24
        yield "    </main>
    
    <script src=\"";
        // line 26
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->env->getFunction('asset')->getCallable()("js/scada.js"), "html", null, true);
        yield "\"></script>
</body>
</html>";
        yield from [];
    }

    // line 23
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_content(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@web/layouts/app.html.twig";
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
        return array (  99 => 23,  91 => 26,  87 => 24,  85 => 23,  77 => 18,  73 => 17,  69 => 16,  65 => 15,  54 => 7,  50 => 6,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"tr\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{{ title|default('koruPHP') }} - koruPHP</title>
    <link rel=\"stylesheet\" href=\"{{ asset('css/scada.css') }}\">
</head>
<body>
    <nav class=\"navbar\">
        <div class=\"nav-brand\">
            <h1>koruPHP</h1>
        </div>
        <ul class=\"nav-menu\">
            <li><a href=\"{{ url('/') }}\">Ana Sayfa</a></li>
            <li><a href=\"{{ url('/users') }}\">Kullanıcılar</a></li>
            <li><a href=\"{{ url('/scada') }}\">SCADA Panel</a></li>
            <li><a href=\"{{ url('/test') }}\">Test</a></li>
        </ul>
    </nav>
    
    <main class=\"container\">
        {% block content %}{% endblock %}
    </main>
    
    <script src=\"{{ asset('js/scada.js') }}\"></script>
</body>
</html>", "@web/layouts/app.html.twig", "C:\\koruPHP\\modules\\web\\views\\layouts\\app.html.twig");
    }
}
