<?php
/**
 * Created by Varenko Oleg
 * Date: 11.10.2021
 */

namespace app\models\templateEngine;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 *
 */
class RevertDummy extends Model
{
    /** @var array  */
    public $tokens = [];
    /** @var string */
    public $template;
    /** @var string */
    public $result;

    /**
     * @return array[]
     */
    public function rules() {
        return [
            [['template', 'result', 'tokens'], 'required'],
            [['template', 'result'], 'string'],
            ['tokens', 'validateTokens'],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @throws InvalidConfigException
     * @throws InvalidTemplateException
     */
    public function validateTokens($attribute, $params) {
        if(!$this->hasErrors()) {

            foreach($this->tokens as $name => $item) {
                if(!isset($item[0], $item[1])) {
                    throw new InvalidConfigException("{$name} invalid token");
                }

                if(strpos($this->template, $item[0]) !== false || strpos($this->template, $item[1]) !== false) {
                    $parser = new Parser([
                        'content' => $this->template,
                        'tokens' => [$name => $item],
                    ]);
                    $result = $parser->parse();

                    if($this->template == $result) {
                        throw new InvalidTemplateException('Invalid template.');
                    }
                }
            }
        }
    }

    /**
     * @return array|false
     * @throws InvalidTemplateException
     * @throws ResultTemplateMismatchException
     */
    public function getTemplateParams() {

        if(!$this->validate()) {
            throw new InvalidTemplateException(implode(', ', $this->firstErrors));
        }

        $separator = '~~~@@@~~~';
        $names = [];
        $values = [];

        foreach($this->tokens as $item) {

            $openToken = preg_quote($item[0]);
            $closeToken = preg_quote($item[1]);
            $pattern = "|{$openToken}([^{$closeToken}]+){$closeToken}|isU";
            if(preg_match_all($pattern, $this->template, $matches)) {
                foreach($matches[1] as $key => $name) {
                    $names[] = $name;
                    $this->template = str_replace($matches[0][$key], $separator, $this->template);
                }

                $textParts = explode($separator, $this->template);
                foreach($textParts as $part) {
                    if(strpos($this->result, $part) === false) {
                        throw new ResultTemplateMismatchException('Result not matches original template.');
                    }
                }
                $this->result = trim(str_replace($textParts, $separator, $this->result), $separator);
                $values = explode($separator, $this->result);
            }
        }

        return array_combine($names, $values);
    }
}