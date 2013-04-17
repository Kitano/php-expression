<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bguery
 * Date: 17/04/13
 * Time: 20:37
 * To change this template use File | Settings | File Templates.
 */

namespace Pel\Tests\Expression;


use Pel\Expression\Expression;
use Pel\Expression\ExpressionCompiler;

class NotExpressionCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getFalseExpressions
     */
    public function testNotExpressionCompiler($expr, $expectedResult)
    {
        $evaluator = eval(
            $source = $this->compiler->compileExpression(
                new Expression($expr)
            )
        );

        $this->assertSame($expectedResult, $evaluator(array()));
    }

    public function getFalseExpressions()
    {
        return array(
            array('not "a string is always true"', false),
            // (except if it is empty)
            array('not ""', true),
            // zero as string is evaluated to false
            array('not "0"', true),
            // but "1" is true
            array('not "1"', false),
            // the same expr with another notation
            array('! "a string is always true"', false),
            // (except if it is empty)
            array('! ""', true),
            // zero as string is evaluated to false
            array('! "0"', true),
            // but "1" is true
            array('! "1"', false),
        );
    }

    protected function setUp()
    {
        $this->compiler = new ExpressionCompiler();
    }
}
