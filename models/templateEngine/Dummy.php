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
    /** @var array  */
    protected $_templateParams = [];


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
     * @param $templatePath
     * @param array $params
     * @return string
     * @throws InvalidConfigException
     */
    public function getCompiledTemplatePath($templatePath, array $params = []) {

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
        $this->_templateParams = $params;
        $compiledTemplatePath = self::$_templateDir . '/' . md5($this->_templatePath . filemtime($this->_templatePath)) . '.php';

        if(!file_exists($compiledTemplatePath)) {

            $parser = new Parser([
                'content' => file_get_contents($this->_templatePath),
                'params' => $this->_templateParams,
                'tokens' => [
                    'escapeToken' => $this->escapeToken,
                    'notEscapeToken' => $this->notEscapeToken,
                ],
            ]);

            file_put_contents($compiledTemplatePath, $parser->parse());
        }

        return $compiledTemplatePath;
    }
}