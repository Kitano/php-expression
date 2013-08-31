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

    public function testCompileArrayAccessExpressionInteger()
    {
        $evaluator = eval($this->compiler->compileExpression(new Expression('foos[0]')));

        $context = array(
            'foos' => array(
                'foo',
                'bar',
            ),
        );
        $expected = 'foo';
        $this->assertEquals($expected, $evaluator($context));
    }

    public function testCompileArrayAccessExpressionString()
    {
        $evaluator = eval($this->compiler->compileExpression(new Expression('foos["foo"]')));

        $context = array(
            'foos' => array(
                'foo' => 'Adrien',
                'bar' => 'William',
            ),
        );
        $expected = 'Adrien';
        $this->assertEquals($expected, $evaluator($context));
    }

    public function testComplexExpression()
    {
        $expression = 'requests[0].attributes.get(name, "default")';
        $evaluator = eval($this->compiler->compileExpression(new Expression($expression)));
        $context = array(
            'requests' => array(
                new Request(),
            ),
            'name' => 'Adrien',
        );

        $expected = array('Adrien', 'default');
        $this->assertEquals($expected, $evaluator($context));
    }

    protected function setUp()
    {
        $this->compiler = new ExpressionCompiler();
    }
}

class Request
{
    public $attributes;

    public function __construct()
    {
        $this->attributes = new ParameterBag();
    }

    public function getRequestFormat()
    {
        return 'html';
    }
}

class ParameterBag
{
    public function get($name, $default)
    {
        return array($name, $default);
    }
}
