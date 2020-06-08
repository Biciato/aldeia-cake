<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * Grana helper
 */
class GranaHelper extends Helper
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function formatar(int $valor, int $decimais = 2, string $separador_decimal = ",", string $separador_milhar = ".", string $prefixo = "", string $sufixo = "")
      {
        $formatado = number_format(($valor/pow(10, $decimais)), $decimais, $separador_decimal, $separador_milhar);
        return $prefixo . $formatado . $sufixo;
      }
    public function desformatar(string $valor_formatado)
      {
        return (int)preg_replace('/\D/', '', $valor_formatado);
      }

}
