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
     */
    public function getTemplateParams() {

        if(!$this->validate()) {
            throw new InvalidTemplateException(implode(', ', $this->firstErrors));
        }

        $parser = new Parser([
            'content' => $this->template,
            'result' => $this->result,
            'tokens' => $this->tokens,
        ]);

        return $parser->revertTemplateResult();
    }
}