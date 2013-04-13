<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Pel\Tests\Expression;

use CG\Proxy\MethodInvocation;
use Pel\Expression\Expression;
use Pel\Expression\Compiler\ParameterExpressionCompiler;
use Pel\Expression\ExpressionCompiler;

class ParameterExpressionCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExpressionCompiler
     */
    private $compiler;

    public function testCompile()
    {
        $evaluator = eval($source = $this->compiler->compileExpression(new Expression(
            '#foo == "bar"')));

        $object = new ParameterAccessTest;
        $reflection = new \ReflectionMethod($object, 'usefulMethod');
        $invocation = new MethodInvocation($reflection, $object, array('bar'), array());
        $this->assertTrue($evaluator(array('object' => $invocation)));

        $invocation->arguments = array('foo');
        $this->assertFalse($evaluator(array('object' => $invocation)));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCompileThrowsExceptionWhenNoMethodInvocation()
    {
        $evaluator = eval($this->compiler->compileExpression(new Expression(
            '#foo == "fofo"')));

        $evaluator(array('object' => new \stdClass));
    }

    public function testCompileArrayWithParameters()
    {
        // TODO
//        $evaluator = eval($source = $this->compiler->compileExpression(new Expression(
//            '[#foo,"test"]')));
//
//        $object = new ParameterAccessTest;
//        $reflection = new \ReflectionMethod($object, 'moreUsefulMethod');
//        $invocation = new MethodInvocation($reflection, $object, array('foo_value'), array());
//
//        $expected = array('foo_value', 'test');
//        $this->assertEquals($expected, $evaluator(array('object' => $invocation)));
    }

    protected function setUp()
    {
        $this->compiler = new ExpressionCompiler();
        $this->compiler->addTypeCompiler(new ParameterExpressionCompiler());
    }
}

class ParameterAccessTest
{
    public function usefulMethod($foo)
    {
    }

    public function moreUsefulMethod($foo, $bar)
    {
    }
}
