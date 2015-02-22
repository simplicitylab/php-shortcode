<?php

/**
 * Test the Lexer class
 *
 * @author Glenn De Backer < glenn@simplicity.be>
 */

namespace Simplicitylab\Test;

use Simplicitylab\ShortCode\Lexer;
use Simplicitylab\ShortCode\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{

    public function testParserStandard()
    {

        // init lexer object
        $lexer = new Lexer('[hello:world]');

        // translate string into token streams
        $tokens = array();
        while ($token = $lexer->nextToken()) {
            array_push($tokens, $token);
        }

        // init parser object
        $parser = new Parser();

        // parse tokens
        $parsed = $parser->parse($tokens);

        $this->assertEquals(count($parsed), 1);
        $this->assertEquals(count($parsed[0]["name"]), 2);
        $this->assertEquals(count($parsed[0]["parameters"]), 0);

        $this->assertEquals($parsed[0]["name"]["parent"], "hello");
        $this->assertEquals($parsed[0]["name"]["child"], "world");
    }


    public function testParserMultiple()
    {

        // init lexer object
        $lexer = new Lexer('[hello:world] [another:test]');

        // translate string into token streams
        $tokens = array();
        while ($token = $lexer->nextToken()) {
            array_push($tokens, $token);
        }

        // init parser object
        $parser = new Parser();

        // parse tokens
        $parsed = $parser->parse($tokens);

        $this->assertEquals(count($parsed), 2);
        $this->assertEquals(count($parsed[0]["name"]), 2);
        $this->assertEquals(count($parsed[0]["parameters"]), 0);
        $this->assertEquals(count($parsed[1]["name"]), 2);
        $this->assertEquals(count($parsed[0]["parameters"]), 0);


        $this->assertEquals($parsed[0]["name"]["parent"], "hello");
        $this->assertEquals($parsed[0]["name"]["child"], "world");
        $this->assertEquals($parsed[1]["name"]["parent"], "another");
        $this->assertEquals($parsed[1]["name"]["child"], "test");
    }

    public function testParserParam()
    {

        // init lexer object
        $lexer = new Lexer('[hello:world hello="test"]');

        // translate string into token streams
        $tokens = array();
        while ($token = $lexer->nextToken()) {
            array_push($tokens, $token);
        }

        // init parser object
        $parser = new Parser();

        // parse tokens
        $parsed = $parser->parse($tokens);

        $this->assertEquals(count($parsed), 1);
        $this->assertEquals(count($parsed[0]["name"]), 2);
        $this->assertEquals(count($parsed[0]["parameters"][0]), 2);

        $this->assertEquals($parsed[0]["name"]["parent"], "hello");
        $this->assertEquals($parsed[0]["name"]["child"], "world");

        $this->assertEquals($parsed[0]["parameters"][0]["name"], "hello");
        $this->assertEquals($parsed[0]["parameters"][0]["value"], "test");
    }
}
