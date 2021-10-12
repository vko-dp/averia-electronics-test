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
        ];

        foreach($data as $item) {

            $this->stdout("Template: {$item['template']} result: {$item['result']} => params: \r\n");
            try {
                var_dump($dummy->revertTemplateResult($item['template'], $item['result']));
            } catch(\Exception $e) {
                $this->stdout(get_class($e) . " error: {$e->getMessage()}\r\n");
            }
        }

        return ExitCode::OK;
    }
}