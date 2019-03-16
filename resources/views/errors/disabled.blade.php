<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Error 400 - Forbidden</title>
	<meta name="viewport" content="width=device-width">
	<style type="text/css">
		@import url(http://fonts.googleapis.com/css?family=Droid+Sans);

		body
		{
			font-family:'Droid Sans', sans-serif;
			font-size:10pt;
			color:#555;
			line-height: 25px;
		}



		.wsod-screen {
        font-family: Tahoma, Geneva, sans-serif;
        font-weight: normal;
        padding: 15px 5px;
				margin-top: 5vh;

        z-index: 9999999999999;
        background-color: #ffffff;
        color: #656565;
    }
    .wsod-screen > .box {
        text-align: center;
    }
    .wsod-screen > .box h1 {
        font-size: 50px;
    }
    .wsod-screen > .box .additional {
        opacity: 0.5;
    }
		a { color: #00e; text-decoration: none;}
		a:visited { color: #551a8b; }
		a:hover { color: #06e; }
		a:focus { outline: thin dotted; }
		a:hover, a:active { outline: 0; }
		a, a:visited
		{
			color:#2972A3;
		}
		a:hover
		{
			color:#72ADD4;
		}
	</style>
</head>
<body>

	<div class="wsod-screen">

    <div class="box">

        <h1>Disabled :(</h1>


				<p>
						This functionality has been disabled (DEMO VERSION).
				</p>

					<p class="additional">
						Additional information: Some features of the demo have been disabled to prevent abuse.
					</p>

					<p>
								<a href="{{ url()->previous() }}">&laquo; go back</a>
							</p>


    </div>

</div>

</body>
</html>
