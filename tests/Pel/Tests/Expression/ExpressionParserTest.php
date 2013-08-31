<?php

namespace Pel\Expression;

use Pel\Expression\Ast\IsEqualExpression;
use Pel\Expression\Ast\NotExpression;
use Pel\Expression\Ast\ParameterExpression;
use Pel\Expression\Ast\ConstantExpression;
use Pel\Expression\Ast\GetItemExpression;
use Pel\Expression\Ast\ArrayExpression;
use Pel\Expression\Ast\MethodCallExpression;
use Pel\Expression\Ast\GetPropertyExpression;
use Pel\Expression\Ast\VariableExpression;
use Pel\Expression\Ast\OrExpression;
use Pel\Expression\Ast\AndExpression;
use Pel\Expression\Ast\FunctionExpression;
use Pel\Expression\ExpressionParser;

class ExpressionParserTest extends \PHPUnit_Framework_TestCase
{
    private $parser;

    public function testSingleFunction()
    {
        $this->assertEquals(new FunctionExpression('myFunction', array()),
            $this->parser->parse('myFunction()'));
    }

    public function testAnd()
    {
        $expected = new AndExpression(
            new FunctionExpression('isEverythingRight', array()),
            new FunctionExpression('isSimple', array()));

        $this->assertEquals($expected, $this->parser->parse('isEverythingRight() && isSimple()'));
        $this->assertEquals($expected, $this->parser->parse('isEverythingRight() and isSimple()'));
    }

    public function testOr()
    {
        $expected = new OrExpression(
            new FunctionExpression('isEverythingRight', array()),
            new FunctionExpression('isSimple', array())
        );

        $this->assertEquals($expected, $this->parser->parse('isEverythingRight() || isSimple()'));
        $this->assertEquals($expected, $this->parser->parse('isEverythingRight() OR isSimple()'));
    }

    public function testNot()
    {
        $expected = new NotExpression(new FunctionExpression('isTrue', array()));

        $this->assertEquals($expected, $this->parser->parse('!isTrue()'));
        $this->assertEquals($expected, $this->parser->parse('not isTrue()'));
    }

    public function testGetProperty()
    {
        $expected = new GetPropertyExpression(new VariableExpression('A'), 'foo');
        $this->assertEquals($expected, $this->parser->parse('A.foo'));
    }

    public function testMethodCall()
    {
        $expected = new MethodCallExpression(new VariableExpression('A'), 'foo', array());
        $this->assertEquals($expected, $this->parser->parse('A.foo()'));
    }

    public function testArray()
    {
        $expected = new ArrayExpression(array(
            'foo' => new ConstantExpression('bar'),
        ));
        $this->assertEquals($expected, $this->parser->parse('{"foo":"bar",}'));
        $this->assertEquals($expected, $this->parser->parse('{"foo":"bar"}'));

        $expected = new ArrayExpression(array(
            new ConstantExpression('foo'),
            new ConstantExpression('bar'),
        ));
        $this->assertEquals($expected, $this->parser->parse('["foo","bar",]'));
        $this->assertEquals($expected, $this->parser->parse('["foo","bar"]'));
    }

    public function testGetItem()
    {
        $expected = new GetItemExpression(
            new GetPropertyExpression(new VariableExpression('A'), 'foo'),
            new ConstantExpression('foo')
        );
        $this->assertEquals($expected, $this->parser->parse('A.foo["foo"]'));
    }

    public function testParameter()
    {
        $expected = new ParameterExpression('contact');
        $this->assertEquals($expected, $this->parser->parse('#contact'));
    }

    public function testArrayOfParameters()
    {
        $expected = new ArrayExpression(array(
            new ParameterExpression('foo'),
            new ParameterExpression('bar'),
        ));
        $this->assertEquals($expected, $this->parser->parse('[#foo,#bar]'));
    }

    public function testIsEqual()
    {
        $expected = new IsEqualExpression(new MethodCallExpression(
            new VariableExpression('user'), 'getUsername', array()),
            new ConstantExpression('Johannes'));
        $this->assertEquals($expected, $this->parser->parse('user.getUsername() == "Johannes"'));
    }

    /**
     * @dataProvider getPrecedenceTests
     */
    public function testPrecedence($expected, $expr)
    {
        $this->assertEquals($expected, $this->parser->parse($expr));
    }

    public function getPrecedenceTests()
    {
        $tests = array();

        $expected = new OrExpression(
            new AndExpression(new VariableExpression('A'), new VariableExpression('B')),
            new VariableExpression('C')
        );
        $tests[] = array($expected, 'A && B || C');
        $tests[] = array($expected, '(A && B) || C');

        $expected = new OrExpression(
            new VariableExpression('C'),
            new AndExpression(new VariableExpression('A'), new VariableExpression('B'))
        );
        $tests[] = array($expected, 'C || A && B');
        $tests[] = array($expected, 'C || (A && B)');

        $expected = new AndExpression(
            new AndExpression(new VariableExpression('A'), new VariableExpression('B')),
            new VariableExpression('C')
        );
        $tests[] = array($expected, 'A && B && C');

        $expected = new AndExpression(
            new VariableExpression('A'),
            new OrExpression(new VariableExpression('B'), new VariableExpression('C'))
        );
        $tests[] = array($expected, 'A && (B || C)');

        return $tests;
    }

    public function testInteger()
    {
        $expected = new ConstantExpression(1);
        $expression = $this->parser->parse('1');

        $this->assertEquals($expected, $expression);
        $this->assertSame($expected->value, $expression->value);
    }

    protected function setUp()
    {
        $this->parser = new ExpressionParser();
    }
}
