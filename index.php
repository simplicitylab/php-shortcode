<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>Document</title>
	<link rel="stylesheet" href="demo.css" />
	<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
</head>
<body>
  <div id="container">
      <h2>Shortcode</h2>
    <section id="description">
          This is a demo of the Shortcode/Plugin system that is described <a href="http://www.simplicity.be/a-shortcode-and-plugin-system/">here</a>.
    </section>
      <textarea id="code">
[HelloWorld:repeat text="hello"]

[HelloWorld:glue word_a="hello" word_b="world"]

[HelloWorld:hash value="hello"]

[Math:sum number_a="17" number_b="3"]

[Math:sqrt value="16"]


      </textarea>
      <br>
      <button id="btnExecute">Execute</button>
      <h2>Result</h2>
      <div id="result"></div>

  </div>
</body>
<script>
     $(document).ready(function() {

    	  $('#btnExecute').bind('click' , function() {

    		  var text = $("#code").val();

    		  if ( text.length > 0 ) {

        		  $.post( "execute.php", { code: text } , function( data ) {

        			  if ( data.status === 100 )
        			  {
						  var result = '';

						  for ( var i = 0; i < data.results.length; i++) {
							  result += data.results[i] + "<br>";
						  }

						  // result
						  $('#result').html(result)

        			  } else {
        				  $("#result").html("There was an error while processing your Shortcode");
        			  }

        		  });

    		  }

    	  });

     });
</script>
</html>