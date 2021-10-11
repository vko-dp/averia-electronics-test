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
    public $tokens = [];

    /**
     * @return string
     */
    public function parse() {

        foreach($this->tokens as $method => $params) {
            call_user_func([$this, $method], $params);
        }
        return $this->content;
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
                return $escape === true ?  "<?= htmlspecialchars(\${$matches[1]}); ?>" : "<?= \${$matches[1]}; ?>";
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