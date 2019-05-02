<!doctype html>
<html>
    <head>
        <meta charset="utf-8">

        <title>Ranking</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

        <!-- Styles -->
        <style>
        	html, body {
                background-color: #fff;
                font-family: 'Roboto', sans-serif;
                font-size: 26px;
			  	width: 100%;
            }

            .content{
				width: 42%;
				margin: 0 auto;
				background: #dadada;
			}

			.content_2 {
				width: 65%;
				height: 80%;
				margin: 0 auto;
				border: 20px solid #dadada;
			}

			h2 {
            	text-align: left;
            	background-color: #dadada;
            }

            input {
            	padding: 18px 0px;
            	font-size: 18px;
            }

            .imp_text {
            	width: 64%;
            	float: left;
            	background-color: #dadada;
            	color: #646263;
            	border: 3px solid white;
            }

            .imp_submit {
            	width: 35%;
            	float: right;
            	background-color: #646263;
            	color: #f7a600;
            }

            table {
            	width: 100%;
            	height: 100%;
            	border-collapse: collapse;
            }

            th, td {
            	padding: 15px 30px;
  				text-align: left;
            }

            td {
            	color: white;
            }

            .td_color_1 {
            	background: #7c7a7b;
            }

            .td_color_2 {
            	background: #646263;
            }

            .fl_left {
            	width: 60%;
            	vertical-align: bottom;
				text-align: left;
				text-shadow: 1px 0 0;
			}

            .fl_right {
            	width: 25%;
            	vertical-align: bottom;
            	text-align: center;
            	text-shadow: 1px 0 0;
            }

            .thead_color {
            	background-color: #f7a600;
            }

            img {
            	width: 40px;
            	height: 40px;
            	float: left;
            }
        </style>
    </head>
    <body>
    	<div class="content">
    		<div class="content_2">
			    <h2>RANKING</h2>
				<form>
		    		<input type="text" class="imp_text" placeholder="Busca por nome">
		    		<input type="submit" class="imp_submit" value="Buscar">
		    	</form>
		    	<br>
		    	<br>
		    	<br>
				<table>
				    <thead>
				        <tr>
				            <th class="fl_left thead_color"><label>NAME</label></th>
				            <th class="fl_right thead_color"><img src="poison.svg"><label>KILLS</label></th>
				        </tr>
				    </thead>
				    <tbody>
				    	<?php for($i=1; $i<=10;) { ?>
				    		<tr>
					    		<td class="fl_left td_color_1">Player <?php echo($i++); ?></td>
					    		<td class="fl_right td_color_1"><?php echo(rand(1, 50)); ?></td>
					    	</tr>

					    	<tr>
					    		<td class="fl_left td_color_2">Player <?php echo($i++); ?></td>
					    		<td class="fl_right td_color_2"><?php echo(rand(1, 50)); ?></td>
					    	</tr>
				    	<?php } ?>
				    </tbody>
				    <tfoot>
				    </tfoot>
				</table>
				<br>
			</div>
		</div>
    </body>
</html>
