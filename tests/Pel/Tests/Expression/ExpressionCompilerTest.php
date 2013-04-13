<?php

namespace Pel\Tests\Expression;

use CG\Proxy\MethodInvocation;
use Pel\Expression\Expression;
use Pel\Expression\ExpressionCompiler;

class ExpressionCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExpressionCompiler
     */
    private $compiler;

    public function testCompileArrayExpression()
    {
        $evaluator = eval($this->compiler->compileExpression(new Expression('["foo","bar"]')));

        $expected = array('foo', 'bar');
        $this->assertEquals($expected, $evaluator(array()));
    }

    protected function setUp()
    {
        $this->compiler = new ExpressionCompiler();
    }
}
