<?php
/**
 * Created by Varenko Oleg
 * Date: 11.10.2021
 */

namespace app\models\templateEngine;

use yii\base\ViewRenderer;

/**
 *
 */
class DummyViewRenderer extends ViewRenderer
{
    /** @var string[]  */
    public $escapeToken = ['{{', '}}'];
    /** @var string[]  */
    public $notEscapeToken = ['{', '}'];

    /**
     * @param \yii\base\View $view
     * @param string $file
     * @param array $params
     * @return false|string
     * @throws \yii\base\InvalidConfigException
     */
    public function render($view, $file, $params) {

        $dummy = new Dummy([
            'escapeToken' => $this->escapeToken,
            'notEscapeToken' => $this->notEscapeToken,
        ]);

        return $dummy->executeTemplate($view, $file, $params);
    }
}