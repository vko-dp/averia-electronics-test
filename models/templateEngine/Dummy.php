<?php
/**
 * Created by Varenko Oleg
 * Date: 11.10.2021
 */

namespace app\models\templateEngine;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\FileHelper;

/**
 *
 */
class Dummy extends Model
{
    /** @var string[]  */
    public $escapeToken = ['{{', '}}'];
    /** @var string[]  */
    public $notEscapeToken = ['{', '}'];


    /** @var string */
    protected $_templatePath;


    /** @var string */
    protected static $_templateDir;

    /**
     * @throws \yii\base\Exception
     */
    public function init() {
        parent::init();

        if(is_null(self::$_templateDir)) {
            self::$_templateDir = Yii::$app->runtimePath . '/dummyTemplates';
            FileHelper::createDirectory(self::$_templateDir, 0777);
        }
    }

    /**
     * @param $view
     * @param $__tplFile
     * @param $__tplParams
     * @return false|string
     * @throws InvalidConfigException
     */
    public function executeTemplate($view, $__tplFile, $__tplParams) {

        extract($__tplParams, EXTR_SKIP);

        ob_start();

        require $this->_getCompiledTemplatePath($__tplFile, $__tplParams);

        $string = ob_get_contents();
        ob_end_clean();

        return $string;
    }

    /**
     * @param $template
     * @param $result
     * @return array|false
     * @throws InvalidTemplateException
     */
    public function revertTemplateResult($template, $result) {

        $revert = new RevertDummy([
            'template' => $template,
            'result' => $result,
            'tokens' => $this->_getTokens(),
        ]);

        return $revert->getTemplateParams();
    }

    /**
     * @param $templatePath
     * @param array $params
     * @return string
     * @throws InvalidConfigException
     */
    protected function _getCompiledTemplatePath($templatePath, array $params = []) {

        if(empty($templatePath)) {
            throw new InvalidConfigException('Dummy::templatePath is required param');
        }

        if(!file_exists($templatePath)) {
            throw new InvalidConfigException("File: {$templatePath} not found");
        }

        foreach($params as $key => $value) {
            if(is_numeric($key)) {
                throw new InvalidConfigException('$params must bee assoc Array');
            }
        }

        $this->_templatePath = $templatePath;
        $compiledTemplatePath = self::$_templateDir . '/' . md5($this->_templatePath . filemtime($this->_templatePath) . implode(',', array_keys($params))) . '.php';

        if(!file_exists($compiledTemplatePath)) {

            $parser = new Parser([
                'content' => file_get_contents($this->_templatePath),
                'tokens' => $this->_getTokens(),
            ]);

            file_put_contents($compiledTemplatePath, $parser->parse());
        }

        return $compiledTemplatePath;
    }

    /**
     * @return array
     */
    protected function _getTokens() {
        return [
            'escapeToken' => $this->escapeToken,
            'notEscapeToken' => $this->notEscapeToken,
        ];
    }
}