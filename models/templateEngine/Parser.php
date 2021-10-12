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
    /** @var string */
    public $result;
    /** @var array  */
    public $tokens = [];

    /** @var string  */
    protected $_separator = '~~~@@@~~~';

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
     * @return array|false
     */
    public function revertTemplateResult() {

        $response = [];
        foreach($this->tokens as $method => $params) {
            $response = array_merge($response, call_user_func([$this, "{$method}Revert"], $params));
        }
        return $response;
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
     * @param bool $escape
     * @return array|false
     * @throws InvalidConfigException
     * @throws ResultTemplateMismatchException
     */
    protected function escapeTokenRevert(array $params, $escape = true) {

        $pattern = $this->_getPattern($params);
        $names = [];
        $values = [];

        if(preg_match_all($pattern, $this->content, $matches)) {
            foreach($matches[1] as $key => $name) {
                $names[] = $name;
                $this->content = str_replace($matches[0][$key], $this->_separator, $this->content);
            }

            $textParts = explode($this->_separator, $this->content);
            foreach($textParts as $part) {
                if(strpos($this->result, $part) === false) {
                    throw new ResultTemplateMismatchException('Result not matches original template.');
                }
            }
            $this->result = trim(str_replace($textParts, $this->_separator, $this->result), $this->_separator);
            $values = explode($this->_separator, $this->result);
            if($escape === true) {
                foreach($values as $k => $v) {
                    $values[$k] = htmlspecialchars_decode($v);
                }
            }
        }

        return array_combine($names, $values);
    }

    /**
     * @param array $params
     * @return array|false
     * @throws InvalidConfigException
     * @throws ResultTemplateMismatchException
     */
    protected function notEscapeTokenRevert(array $params) {

        return $this->escapeTokenRevert($params, false);
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