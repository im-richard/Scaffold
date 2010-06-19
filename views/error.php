<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title>Scaffold Framework Error</title>
	
	<style>
		html { background:#e7e7e7; }
		h1 { font-size:24px; margin:9px 0 18px 0;}
		p {margin:0 0 18px 0;}
		h2 { font-size:18px; }
		strong {color:#000}
		pre {background:#eee;padding:20px;font-size:13px;-webkit-border-radius:5px}
		
		.content 
		{ 
			min-width:40%; 
			max-width:60%; 
			margin:30px auto; 
			font:15px/18px Arial; 
			padding:18px 20px 0; 
			background:#fff;
			color:#595959; 
			border:1px solid #aaa; 
			margin-bottom: 20px; 
			-webkit-border-radius:6px; 
			-moz-border-radius:6px; 
			-webkit-box-shadow:0 2px 5px rgba(0,0,0,0.2);
		}
		
		<?php
			$path = dirname(__FILE__) . '/stripe.png';
			$data = 'data:image/'.pathinfo($path, PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($path));
		?>
		
		.meta
		{
			background:url(<?php echo $data; ?>);
			background-color:#c24a4a;
			color:#ffe5e5;
			font-size:12px;
			text-shadow:0 -1px 0 rgba(0,0,0,0.4);
			margin:21px -21px 0;
			padding:0 20px 0;
			line-height:30px;			
			-webkit-animation-iteration-count:infinite;
			-webkit-animation-duration:5s;
			-webkit-animation-name:unicorn;
			-webkit-animation-timing-function:linear;
		}
		
		@-webkit-keyframes unicorn 
		{
			from	{ background-position:0 0 }
			to		{ background-position:222px 0 }
		}
		
		.meta.bottom
		{
			-webkit-border-bottom-left-radius:6px; -moz-border-bottomleft-radius:6px; 
			-webkit-border-bottom-right-radius:6px; -moz-border-bottomright-radius:6px; 
		}
		
		.meta p {margin:0}
		
		code{
			color:#c24a4a;
			font-size:14px;
		}

	</style>
</head>
<body>
	
	<div class="content">
		<h1><?php echo $title; ?></h1>
		<p id="message"><?php echo $message; ?></p>
		<div class="meta bottom">
			<p><?php echo "$file [$line]"; ?></p>
		</div>
	</div>
	
	<?php if(isset($css)): ?>
	<div class="content">
		<pre><code><?php echo $css ?></code></pre>
	</div>
	<?php endif; ?>

</body>
</html>