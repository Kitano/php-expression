<?php

namespace Pel\Tests\Expression;

use Pel\Expression\ExpressionLexer;

class ExpressionLexerTest extends \PHPUnit_Framework_TestCase
{
    private $lexer;

    public function testParameter()
    {
        $this->lexer->setInput('#contact');

        $this->assertEquals(array(
            'contact',
            0,
            ExpressionLexer::T_PARAMETER,
        ), $this->lexer->next);
        $this->assertFalse($this->lexer->moveNext());
    }

    protected function setUp()
    {
        $this->lexer = new ExpressionLexer();
    }
}