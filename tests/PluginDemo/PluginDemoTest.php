<?php

/**
 * Test the Lexer class
 *
 * @author Glenn De Backer < glenn@simplicity.be>
 */

namespace Simplicitylab\PluginDemo\Test;

use Simplicitylab\PluginDemo\PluginDemo;
use Simplicitylab\ShortCode\Lexer;
use Simplicitylab\ShortCode\Parser;

class PluginDemoTest extends \PHPUnit_Framework_TestCase
{

    private $pluginDemo;

    protected function setUp()
    {
        $this->pluginDemo = new PluginDemo();
    }


    /**
     * This method is needed to access private members of the class PluginDemo
     */
    protected static function getMethod($name)
    {
        $class = new \ReflectionClass('Simplicitylab\PluginDemo\PluginDemo');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }


    public function testGetFilesInPluginFolder()
    {

        // get private method
        $getPluginsInFolder = self::getMethod('getFilesInPluginFolder');

        $this->assertCount(2, $getPluginsInFolder->invoke($this->pluginDemo));
    }

    public function testIsValidPlugin()
    {

        // get private method
        $isValidPlugin = self::getMethod('isValidPlugin');

        $this->assertTrue($isValidPlugin->invokeArgs($this->pluginDemo, array("HelloWorld", "simple")));
        $this->assertFalse($isValidPlugin->invokeArgs($this->pluginDemo, array("HelloWorld", "imPrivate")));

        $parameters =  array( array("name" => "text", "value" => "hello") );
        $this->assertTrue($isValidPlugin->invokeArgs($this->pluginDemo, array("HelloWorld", "repeat", $parameters )));

        $parameters =  array( array("name" => "faulty", "value" => "hello") );
        $this->assertFalse($isValidPlugin->invokeArgs($this->pluginDemo, array("HelloWorld", "repeat", $parameters )));
    }

    public function testProcessSimple()
    {

        // lexer : convert into tokens
        $lexer = new Lexer("[HelloWorld:simple]");

        $tokens = array();

        while ($token = $lexer->nextToken()) {
            array_push($tokens, $token);
        }

        $this->assertCount(5, $tokens);

        // parse tokens
        $parser = new Parser();
        $parsed = $parser->parse($tokens);

        $this->assertCount(1, $parsed);

        // process
        $returnValue = $this->pluginDemo->process($parsed);
        $this->assertEquals("I'm simple", $returnValue[0]);
    }

    public function testProcessParameter()
    {

        // lexer : convert into tokens
        $lexer = new Lexer('[HelloWorld:repeat text="hello"]');

        $tokens = array();

        while ($token = $lexer->nextToken()) {
            array_push($tokens, $token);
        }

        $this->assertCount(10, $tokens);

        // parse tokens
        $parser = new Parser();
        $parsed = $parser->parse($tokens);

        $this->assertCount(1, $parsed);

        // process
        $returnValue = $this->pluginDemo->process($parsed);
        $this->assertEquals("I'm repeating hello", $returnValue[0]);
    }


    public function testProcessMultipleParameter()
    {

        // lexer : convert into tokens
        $lexer = new Lexer('[HelloWorld:glue word_a="hello" word_b="world"]');

        $tokens = array();

        while ($token = $lexer->nextToken()) {
            array_push($tokens, $token);
        }

        $this->assertCount(15, $tokens);

        // parse tokens
        $parser = new Parser();
        $parsed = $parser->parse($tokens);

        $this->assertCount(1, $parsed);

        // process
        $returnValue = $this->pluginDemo->process($parsed);
        $this->assertEquals("hello world", $returnValue[0]);
    }


    public function testMultipleShortCodes()
    {

        // lexer : convert into tokens
        $lexer = new Lexer('[HelloWorld:simple] [HelloWorld:repeat text="glenn"]');

        $tokens = array();

        while ($token = $lexer->nextToken()) {
            array_push($tokens, $token);
        }

        $this->assertCount(15, $tokens);

        // parse tokens
        $parser = new Parser();
        $parsed = $parser->parse($tokens);

        $this->assertCount(2, $parsed);

        // process
        $returnValue = $this->pluginDemo->process($parsed);
        $this->assertEquals("I'm simple", $returnValue[0]);
        $this->assertEquals("I'm repeating glenn", $returnValue[1]);
    }
}
