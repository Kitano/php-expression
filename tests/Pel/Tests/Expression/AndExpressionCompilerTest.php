<?php

namespace Pel\Tests\Expression;

use Pel\Expression\Expression;
use Pel\Expression\ExpressionCompiler;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class AndExpressionCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getAndExpressions
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

    public function getAndExpressions()
    {
        return array(
            array('"true" and not "true"',     false),
            array('"true" and "true"',         true),
            array('not "true" and not "true"', false),
            array('not "true" and "true"',     false),
        );
    }

    protected function setUp()
    {
        $this->compiler = new ExpressionCompiler();
    }
}
