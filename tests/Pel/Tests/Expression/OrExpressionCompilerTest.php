<?php

namespace Pel\Tests\Expression;

use Pel\Expression\Expression;
use Pel\Expression\ExpressionCompiler;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class OrExpressionCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getOrExpressions
     */
    public function testAndExpressionCompiler($expr, $expectedResult)
    {
        $evaluator = eval(
            $source = $this->compiler->compileExpression(
                new Expression($expr)
            )
        );

        $this->assertSame($expectedResult, $evaluator(array()));
    }

    public function getOrExpressions()
    {
        return array(
            array('"true" or not "true"',     true),
            array('"true" or "true"',         true),
            array('not "true" or not "true"', false),
            array('not "true" or "true"',     true),
        );
    }

    protected function setUp()
    {
        $this->compiler = new ExpressionCompiler();
    }
}
