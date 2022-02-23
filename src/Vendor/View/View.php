<?php
namespace Vendor\View;
use MyProject\Exceptions\InvalidVarException;

class View
{
    private $templatesPath;
    private $extraVars = [];

    public function __construct(string $templatesPath) {
        $this->templatesPath = $templatesPath;
    }
    public function setVar(string $name, $value): void {
        if (isset($this->extraVars[$name])) {
            throw new InvalidVarException('Переменная "$' . $name . '" уже существует');
        }
        $this->extraVars[$name] = $value;
    }
    public function renderHtml(string $templateName, array $vars = [], int $code = 200) {
        http_response_code($code);

        $extractCount = extract($this->extraVars, EXTR_SKIP);
        if ($extractCount !== count($this->extraVars)) {
            throw new InvalidVarException('Ошибка импортирования переменных в текущую таблицу символов');
        }
        $extractCount = extract($vars, EXTR_SKIP);
        if ($extractCount !== count($vars)) {
            throw new InvalidVarException('Ошибка импортирования переменных в текущую таблицу символов');
        }
        ob_start();
        include $this->templatesPath . '/' . $templateName;
        $buffer = ob_get_contents();
        ob_end_clean();
    
        echo $buffer;
    }
}