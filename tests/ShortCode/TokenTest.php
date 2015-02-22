<?php

/**
 * Test the Lexer class
 *
 * @author Glenn De Backer < glenn@simplicity.be>
 */

namespace Simplicitylab\Test;

use Simplicitylab\ShortCode\Token;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    public function testToken()
    {
        $token = new Token('TOKEN_LBRACKET', '[');
        $this->assertEquals($token->getType(), 'TOKEN_LBRACKET');
        $this->assertEquals($token->getText(), '[');
    }
}
