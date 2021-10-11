<?php
/**
 * Created by Varenko Oleg
 * Date: 11.10.2021
 */

namespace app\models\templateEngine;

use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 *
 */
class Parser extends Component
{
    /** @var string */
    public $content;
    /** @var array  */
    public $params = [];
    /** @var array  */
    public $tokens = [];

    /** @var string  */
    protected $_tplParamsVarName = '$__tplParams';

    /**
     * @return string
     */
    public function parse() {

        foreach($this->tokens as $method => $params) {
            call_user_func([$this, $method], $params);
        }

        $params = $v = var_export($this->params, true);

        return "<?php
{$this->_tplParamsVarName} = {$params};

{$this->content}
        ";

    }

    /**
     * @param array $params
     * @param bool $escape
     * @throws InvalidConfigException
     */
    protected function escapeToken(array $params, $escape = true) {

        $pattern = $this->_getPattern($params);

        $this->content = preg_replace_callback(
            $pattern,
            function ($matches) use ($escape) {
                foreach($this->params as $name => $value) {
                    if($name == $matches[0]) {
                        return $escape === true ?  "<?= htmlspecialchars({$this->_tplParamsVarName}['{$name}']); ?>" : "<?= {$this->_tplParamsVarName}['{$name}']; ?>";
                    }
                }
                return $matches[0];
            },
            $this->content
        );
    }

    /**
     * @param array $params
     * @throws InvalidConfigException
     */
    protected function notEscapeToken(array $params) {

        $this->escapeToken($params, false);
    }

    /**
     * @param array $params
     * @return string
     * @throws InvalidConfigException
     */
    protected function _getPattern(array $params) {

        if(!isset($params[0], $params[1])) {
            throw new InvalidConfigException('$params must have 2 elements open and close token');
        }
        $openToken = preg_quote($params[0]);
        $closeToken = preg_quote($params[1]);
        return "|{$openToken}([^{$closeToken}]+){$closeToken}|isU";
    }
}