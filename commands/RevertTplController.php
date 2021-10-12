<?php
/**
 * Created by Varenko Oleg
 * Date: 11.10.2021
 */

namespace app\commands;

use app\models\templateEngine\Dummy;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 *
 */
class RevertTplController extends Controller
{
    /**
     * @return int
     */
    public function actionIndex() {

        $dummy = new Dummy();

        $data = [
            [
                'template' => 'Hello, my name is {{name}}.',
                'result' => 'Hello, my name is Juni.',
            ],

            [
                'template' => 'Hello, my name is {{name}.',
                'result' => 'Hello, my name is Juni.',
            ],

            [
                'template' => 'Hello, my name is {{name}}.',
                'result' => 'Hello, my lastname is Juni.',
            ],

            [
                'template' => 'Hello, my name is {{name}}.',
                'result' => 'Hello, my name is .',
            ],

            [
                'template' => 'Hello, my name is {name}.',
                'result' => 'Hello, my name is <robot>.',
            ],

            [
                'template' => 'Hello, my name is {{name}}.',
                'result' => 'Hello, my name is &lt;robot&gt;.',
            ],
        ];

        $number = 1;
        foreach($data as $item) {

            $this->stdout("\r\n#{$number}");
            $this->stdout("\r\nTemplate: {$item['template']} result: {$item['result']}\r\n");
            $this->stdout("Revert result: ");
            try {
                var_dump($dummy->revertTemplateResult($item['template'], $item['result']));
            } catch(\Exception $e) {
                $this->stdout(get_class($e) . " error: {$e->getMessage()}\r\n");
            }
            $number++;
        }

        return ExitCode::OK;
    }
}