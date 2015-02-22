<?php

/**
 * ShortCode Lexer
 *
 * Copyright (c) 2010 Glenn De Backer < glenn at simplicity dot be >
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * If you have any questions or comments, please email:
 * Glenn at simplicity dot be
 *
 * @author Glenn De Backer (glenn at  simplicity  dot be)
 *
 * @version 2.0
 */

namespace Simplicitylab\ShortCode;

class Lexer
{
    private $text;
    private $textLength;

    private $currentCharacter;
    private $indexCurrentCharacter;

    public function __construct($text)
    {

        // 1. store text length
        $this->setTextLength(strlen($text));

        // 2. set current character index and character to zero
        $this->setIndexCurrentCharacter(0);
        $this->setCurrentCharacter("");

        // 3. store text
        $this->setText($text);
    }


    /**
     * Gets next token
     */
    public function nextToken()
    {
        $buffer = "";
        $loop = true;

        while ($loop) {
            // 1.a if end of string is reached return EOF
            if ($this->getIndexCurrentCharacter() >= $this->getTextLength()) {
                return 0;
                // 1.b determine which token needs to be returned
            } else {
                // 1. get current character
                $this->setCurrentCharacter(substr($this->getText(), $this->getIndexCurrentCharacter(), 1));

                // 2. increase character pointer
                $this->setIndexCurrentCharacter($this->getIndexCurrentCharacter()+1);

                // 3. determine which token type it is
                switch ($this->getCurrentCharacter()) {
                    // 3.a if it is a single character token return
                    // token object
                    case '[':
                        return new Token('TOKEN_LBRACKET', '[');

                    case ']':
                        return new Token('TOKEN_RBRACKET', ']');

                    case '=':
                        return new Token('TOKEN_ASSIGN', '=');

                    case '"':
                        return new Token('TOKEN_QUOTE', '"');

                    case ':':
                        return new Token('TOKEN_COLON', ':');

                    // 3.b if it is a number or word token buffer it to return
                    // the whole word or number as a token_word or token_number
                    default:

                        // 3.b.a When it is a character
                        if ($this->isLetter($this->getCurrentCharacter())) {
                            // 1. add to buffer
                            $buffer .= $this->getCurrentCharacter();

                            // 2. check if next character is a letter or end is reached
                            //
                            // REMARK: because I already increased the index of the current chacter
                            // the index already points to the next character
                            if (!$this->isLetter(substr($this->getText(), $this->getIndexCurrentCharacter(), 1))
                                || ($this->getIndexCurrentCharacter() +1) >=$this->textLength
                            ) {
                                return new Token('TOKEN_WORD', $buffer);
                            }

                            // 3.b.c When it is a number
                        } elseif ($this->isNumber($this->getCurrentCharacter())) {
                            // 1. add to buffer
                            $buffer .= $this->getCurrentCharacter();

                            // 2. check if next character is a number or end is reached
                            if (!$this->isNumber(substr($this->getText(), $this->getIndexCurrentCharacter(), 1))
                                || ($this->getIndexCurrentCharacter() +1) >=$this->textLength
                            ) {
                                return new Token('TOKEN_NUMBER', $buffer);
                            }
                        }
                } // end determine token
            }
        }

        // return token unknown
        return new Token('TOKEN_UNKNOWN', null);
    }

    private function isLetter($character)
    {
        return preg_match("/[a-zA-Z_-]/", $character);
    }

    private function isNumber($character)
    {
        return preg_match("/[0-9]/", $character);
    }


    /**
     * Getters and Setters
     **/
    public function getTextLength()
    {
        return $this->textLength;
    }

    public function setTextLength($textLength)
    {
        $this->textLength = $textLength;
    }

    public function getCurrentCharacter()
    {
        return $this->currentCharacter;
    }

    public function setCurrentCharacter($currentCharacter)
    {
        $this->currentCharacter = $currentCharacter;
    }

    public function getIndexCurrentCharacter()
    {
        return $this->indexCurrentCharacter;
    }

    public function setIndexCurrentCharacter($indexCurrentCharacter)
    {
        $this->indexCurrentCharacter = $indexCurrentCharacter;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }
}
