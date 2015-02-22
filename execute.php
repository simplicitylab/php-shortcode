<?php
  // include autoloader
  require_once 'vendor/autoload.php';

  use Simplicitylab\PluginDemo\PluginDemo;
  use Simplicitylab\ShortCode\Lexer;
  use Simplicitylab\ShortCode\Parser;

  $pluginDemo = new PluginDemo();

  header('Content-type: application/json');

  if ( array_key_exists('code', $_POST) ) {

      $code = $_POST['code'];

      // lexer : convert into tokens
      $lexer = new Lexer($code);

      $tokens = array();

      while ($token = $lexer->nextToken()) {
        array_push($tokens, $token);
      }

      // parser : parse tokens
      $parser = new Parser();
      $parsed = $parser->parse($tokens);

      // execute code
      $returnValues = $pluginDemo->process($parsed);

      if ( $returnValues === false ) {
          echo json_encode(array("status" => 901));
      }else {
          echo json_encode(array("status" => 100, "results" => $returnValues));
      }


  } else {
      echo json_encode(array("status" => 900));
  }


?>
