<?php

/**
 * Test the Lexer class
 *
 * @author Glenn De Backer < glenn@simplicity.be>
 */

namespace Simplicitylab\ShortCode;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    public function testLexerStandard()
    {
        // init lexer object
        $lexer = new Lexer('[hello:world]');

        $tokens = array();

        while ($token = $lexer->nextToken()) {
            array_push($tokens, $token);
        }

        $this->assertEquals(count($tokens), 5);

        $this->assertEquals($tokens[0]->getType(), "TOKEN_LBRACKET");
        $this->assertEquals($tokens[0]->getText(), "[");

        $this->assertEquals($tokens[1]->getType(), "TOKEN_WORD");
        $this->assertEquals($tokens[1]->getText(), "hello");

        $this->assertEquals($tokens[2]->getType(), "TOKEN_COLON");
        $this->assertEquals($tokens[2]->getText(), ":");

        $this->assertEquals($tokens[3]->getType(), "TOKEN_WORD");
        $this->assertEquals($tokens[3]->getText(), "world");

        $this->assertEquals($tokens[4]->getType(), "TOKEN_RBRACKET");
        $this->assertEquals($tokens[4]->getText(), "]");
    }

    public function testLexerWithParams()
    {

        // init lexer object
        $lexer = new Lexer('[hello:paramworld param="hallo" otherparam="33333"]');

        $tokens = array();

        while ($token = $lexer->nextToken()) {
            array_push($tokens, $token);
        }

        $this->assertEquals(count($tokens), 15);

        $this->assertEquals($tokens[0]->getType(), "TOKEN_LBRACKET");
        $this->assertEquals($tokens[0]->getText(), "[");

        $this->assertEquals($tokens[1]->getType(), "TOKEN_WORD");
        $this->assertEquals($tokens[1]->getText(), "hello");

        $this->assertEquals($tokens[2]->getType(), "TOKEN_COLON");
        $this->assertEquals($tokens[2]->getText(), ":");

        $this->assertEquals($tokens[3]->getType(), "TOKEN_WORD");
        $this->assertEquals($tokens[3]->getText(), "paramworld");

        $this->assertEquals($tokens[4]->getType(), "TOKEN_WORD");
        $this->assertEquals($tokens[4]->getText(), "param");

        $this->assertEquals($tokens[5]->getType(), "TOKEN_ASSIGN");
        $this->assertEquals($tokens[5]->getText(), "=");

        $this->assertEquals($tokens[6]->getType(), "TOKEN_QUOTE");
        $this->assertEquals($tokens[6]->getText(), "\"");

        $this->assertEquals($tokens[7]->getType(), "TOKEN_WORD");
        $this->assertEquals($tokens[7]->getText(), "hallo");

        $this->assertEquals($tokens[8]->getType(), "TOKEN_QUOTE");
        $this->assertEquals($tokens[8]->getText(), "\"");

        $this->assertEquals($tokens[9]->getType(), "TOKEN_WORD");
        $this->assertEquals($tokens[9]->getText(), "otherparam");


        $this->assertEquals($tokens[10]->getType(), "TOKEN_ASSIGN");
        $this->assertEquals($tokens[10]->getText(), "=");

        $this->assertEquals($tokens[11]->getType(), "TOKEN_QUOTE");
        $this->assertEquals($tokens[11]->getText(), "\"");

        $this->assertEquals($tokens[12]->getType(), "TOKEN_NUMBER");
        $this->assertEquals($tokens[12]->getText(), "33333");

        $this->assertEquals($tokens[13]->getType(), "TOKEN_QUOTE");
        $this->assertEquals($tokens[13]->getText(), "\"");


        $this->assertEquals($tokens[14]->getType(), "TOKEN_RBRACKET");
        $this->assertEquals($tokens[14]->getText(), "]");
    }
}
