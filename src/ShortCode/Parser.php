<?php

/**
 * ShortCode Parser
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

class Parser
{
    private $tokens = array();
    private $tokenIndex = 0;
    private $numberOfTokens = 0;


    private $parsed = array();
    private $names = array();
    private $parameters = array();

    /**
     * Parse tokens
     * @param $tokens
     * @return array parsed
     **/
    public function parse($tokens)
    {
        $blockStarted = false;

        // set number of tokens
        $this->setNumberOfTokens(count($tokens));

        // set tokens
        $this->setTokens($tokens);

        // loop through tokens
        while ($this->getRemainingTokens()) {
            //
            // match rules
            //
            if ($this->matchRule(array('TOKEN_LBRACKET', 'TOKEN_WORD', 'TOKEN_COLON', 'TOKEN_WORD'))) {
                // set block started flag
                $blockStarted = true;

                // reset name and parameters
                $this->clearNames();
                $this->clearParameters();

                // example : you  know the word value is situated at the current token index + 1
                $parent = $this->getTokenTextAtIndex($this->getCurrentTokenIndex() + 1);
                $child = $this->getTokenTextAtIndex($this->getCurrentTokenIndex() + 3);

                // store into names array
                $this->setNames(array("parent" => $parent, "child" => $child));


                // skip tokens to process
                $this->setCurrentTokenIndex($this->getCurrentTokenIndex() + 4);

                continue;
            } elseif ($this->matchRule(array('TOKEN_LBRACKET', 'TOKEN_WORD'))) {
                $parent = $this->getTokenTextAtIndex($this->getCurrentTokenIndex() + 1);
                $child = 0;

                // set block started flag
                $blockStarted = true;

                // reset name and parameters
                $this->clearNames();
                $this->clearParameters();

                // store into names array
                $this->setNames(array("parent" => $parent, "child" => $child));

                // skip tokens to process
                $this->setCurrentTokenIndex($this->getCurrentTokenIndex() + 2);

                // set block started flag
                $this->setBlockStarted(true);

                continue;
            } elseif ($this->matchRule(array('TOKEN_WORD',
                'TOKEN_ASSIGN', 'TOKEN_QUOTE', 'TOKEN_WORD', 'TOKEN_QUOTE'))) {
                $paramName = $this->getTokenTextAtIndex($this->getCurrentTokenIndex());
                $paramValue = $this->getTokenTextAtIndex($this->getCurrentTokenIndex() + 3);

                $this->storeParameters(array("name" => $paramName, "value" => $paramValue));

                // skip tokens to process
                $this->setCurrentTokenIndex($this->getCurrentTokenIndex() + 4);

                continue;
            } elseif ($this->matchRule(array('TOKEN_WORD',
                'TOKEN_ASSIGN', 'TOKEN_QUOTE', 'TOKEN_NUMBER', 'TOKEN_QUOTE'))
            ) {
                $paramName = $this->getTokenTextAtIndex($this->getCurrentTokenIndex());
                $paramValue = $this->getTokenTextAtIndex($this->getCurrentTokenIndex() + 3);

                $this->storeParameters(array("name" => $paramName, "value" => $paramValue));

                // skip tokens to process
                $this->setCurrentTokenIndex($this->getCurrentTokenIndex() + 4);

                continue;
            } elseif ($this->matchRule(array('TOKEN_RBRACKET'))) {
                // end of block ]
                // if block was started end it
                if ($blockStarted) {
                    // set block started flag
                    $blockStarted = false;

                    // store into return array
                    $this->storeParsed(array("name"=>$this->getNames(), "parameters"=>$this->getParameters()));
                }
            }

            // increase token index
            $this->increaseCurrentTokenIndex();
        }

        // if all tokens has been processed
        return $this->getParsed();
    }

    /**
     * Returns the remaining tokens available for processing
     */
    private function getRemainingTokens()
    {
        return $this->getNumberOfTokens() - $this->getCurrentTokenIndex();
    }

    /**
     * match against rule
     * @param array $elements
     * @return boolean true if matched
     */
    private function matchRule($elements)
    {
        $matched = true;

        // 1.  store number of elements
        $numberOfElements = count($elements);

        //  2. determine if there are enough tokens left
        //     to match rule against
        //     (if the rule consists out of 3 elements and there are only 2 tokens left you can skip any further work)
        if ($numberOfElements <= $this->getRemainingTokens()) {
            for ($i = 0; $i < $numberOfElements; $i++) {
                // compare token against elements
                if (strcmp($this->getTokenTypeAtIndex($this->getCurrentTokenIndex() + $i), $elements[$i]) != 0) {
                    $matched = false;
                }
            }

            return $matched;
        } else {
            return false;
        }
    }

    /**
     *  Getters and Setters
     **/

    public function getNumberOfTokens()
    {
        return $this->numberOfTokens;
    }


    public function setNumberOfTokens($numberOfTokens)
    {
        $this->numberOfTokens = $numberOfTokens;
    }


    public function getCurrentTokenIndex()
    {
        return $this->tokenIndex;
    }


    public function setCurrentTokenIndex($tokenIndex)
    {
        $this->tokenIndex = $tokenIndex;
    }


    public function increaseCurrentTokenIndex()
    {
        $this->tokenIndex = $this->tokenIndex + 1;
    }


    public function getNames()
    {
        return $this->names;
    }

    public function setNames($names)
    {
        $this->names = $names;
    }

    public function clearNames()
    {
        unset($this->names);
    }


    public function getParameters()
    {
        return $this->parameters;
    }


    public function storeParameters($parameters)
    {
        array_push($this->parameters, $parameters);
    }


    public function clearParameters()
    {
        unset($this->parameters);
        $this->parameters = array();
    }


    public function getTokens()
    {
        return $this->tokens;
    }


    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
    }


    public function getTokenTypeAtIndex($index)
    {
        if ($index <= $this->getNumberOfTokens()) {
            return $this->tokens[$index]->getType();
        } else {
            return 0;
        }
    }


    public function getTokenTextAtIndex($index)
    {
        if ($index <= $this->getNumberOfTokens()) {
            return $this->tokens[$index]->getText();
        } else {
            return 0;
        }
    }


    public function getParsed()
    {
        return $this->parsed;
    }

    public function storeParsed($block)
    {
        array_push($this->parsed, $block);
    }

    public function setParsed($parsed)
    {
        $this->parsed = $parsed;
    }
}
