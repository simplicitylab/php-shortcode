# Architecture

The PHP-Shortcode library consists of 2 parts:

* Lexer
* Parser

## Lexer

The task of the lexer is very simple, it transforms code into a collection of tokens. A token meaning a word or an atomic element.

```
// Example : shortcode to tokens 
[Hello param="hello"] 

=> 

TOKEN_LBRAKET,
TOKEN_WORD,
TOKEN_SPACE,
TOKEN_WORD,
TOKEN_ASSIGN...
```

## Parser

The parser matches a set of tokens against a set of rules. 

```
//
//  Example : pattern to retrieve a parameter
//  param           =            "          hello         "
[TOKEN_WORD, TOKEN_ASSIGN , TOKEN_QUOTE, TOKEN_WORD, TOKEN_QUOTE]
```

If it finds a pattern it stores it into a simple array structure (which you can further process) containing its value and also the type. The type could be for example a function name, parameter value,... .

For example the following demonstrates how a shortcode is parsed.

```
//
// Example :[Hello:repeat param="hello"] to parsed JSON representation
{
   "parent" : "Hello",
   "child"  : "repeat",
   "parameters" : [{
       "name" : "param",
       "value" : "hello"
   }]
}
```