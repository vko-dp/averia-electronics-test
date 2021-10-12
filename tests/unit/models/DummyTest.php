<?php
/**
 * Created by Varenko Oleg
 * Date: 12.10.2021
 */

namespace unit\models;

use app\models\templateEngine\Dummy;

class DummyTest extends \Codeception\Test\Unit
{
    public function testValidTemplateAndResult()
    {
        expect_that($response = (new Dummy())->revertTemplateResult('Hello, my name is {{name}}.', 'Hello, my name is Juni.'));
        expect($response['name'])->equals('Juni');
    }

    public function testNotValidTemplate()
    {
        expect_that($response = (new Dummy())->revertTemplateResult('Hello, my name is {{name}.', 'Hello, my name is Juni.'));
        expect($response['name'])->equals('Juni');
    }

    public function testNotValidResult()
    {
        expect_that($response = (new Dummy())->revertTemplateResult('Hello, my name is {{name}}.', 'Hello, my lastname is Juni.'));
        expect($response['name'])->equals('Juni');
    }

    public function testEmptyParam()
    {
        expect_that($response = (new Dummy())->revertTemplateResult('Hello, my name is {{name}}.', 'Hello, my name is .'));
        expect($response['name'])->equals('');
    }

    public function testNotEscape()
    {
        expect_that($response = (new Dummy())->revertTemplateResult('Hello, my name is {name}.', 'Hello, my name is <robot>.'));
        expect($response['name'])->equals('<robot>');
    }

    public function testEscape()
    {
        expect_that($response = (new Dummy())->revertTemplateResult('Hello, my name is {{name}}.', 'Hello, my name is &lt;robot&gt;.'));
        expect($response['name'])->equals('<robot>');
    }
}