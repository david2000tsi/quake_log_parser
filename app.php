<?php
	include_once 'utils.php';

	$GLOBALS["list"] = null;
	$allScore = getAllPLayersScore();
	if($allScore && !isset($allScore["error"]) && count($allScore) > 0)
	{
		$GLOBALS["list"] = $allScore;
	}
?>

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
            	text-shadow: 1px 0 0;
            }

            input {
            	padding: 14px 0px;
            	font-size: 18px;
            	text-shadow: 0.5px 0 0;
            }

            .imp_text {
            	width: 65%;
            	float: left;
            	background-color: #dadada;
            	color: #646263;
            	border-left: 2px solid white;
            	border-top: 2px solid white;
            	border-right: 0px solid white;
            	border-bottom: 2px solid white;
            	padding-left: 10px;
            }

            .imp_submit {
            	width: 32.5%;
            	float: right;
            	background-color: #646263;
            	color: #f7a600;
            	border-left: 2px solid #646263;
            	border-right: 2px solid #646263;
            	border-top: 2px solid #646263;
            	border-bottom: 2px solid #646263;
            }

            table {
            	width: 100%;
            	height: 100%;
            	border-collapse: collapse;
            }

            th {
            	padding: 15px 8px;
            }

            td {
            	padding: 22px 8px 5px 8px;
            	color: white;
            	vertical-align: bottom;
            	font-size: 21px;
            }

            .th_color {
            	background-color: #f7a600;
            }

            .th_left {
            	width: 74%;
				text-align: left;
				text-shadow: 1px 0 0;
			}

            .th_right {
            	width: 20%;
            	text-align: center;
            	text-shadow: 1px 0 0;
            }

            .td_color_1 {
            	background: #7c7a7b;
            }

            .td_color_2 {
            	background: #646263;
            }

            .td_left {
            	width: 60%;
				text-align: left;
			}

            .td_right {
            	width: 30%;
            	text-align: center;
            	text-shadow: 1px 0 0;
            }

            img {
            	width: 35px;
            	height: 35px;
            	float: left;
            }
        </style>
    </head>
    <body>
    	<div class="content">
    		<div class="content_2">
			    <h2>RANKING</h2>
				<form id="form">
					<input type="hidden" id="moderequest" name="moderequest" value="getplayerscore">
					<input type="text" id="nomejogador" name="nomejogador" class="imp_text" placeholder="Busca por nome">
		    		<input type="submit" class="imp_submit" value="Buscar">
		    	</form>
		    	<br>
		    	<br>
		    	<br>
				<table>
				    <thead>
				        <tr>
				            <th class="th_left th_color"><label>NAME</label></th>
				            <th class="th_right th_color"><img src="poison.svg"><label>KILLS</label></th>
				        </tr>
				    </thead>
				    <tbody>
				    	<?php
				    		if(isset($GLOBALS["list"]) && !empty($GLOBALS["list"]))
				    		{
				    			foreach($GLOBALS["list"] as $chave => $line)
				    			{
				    				$player = $line["nome_jogador"];
				    				$kills = $line["sum_qtd_kills"];
				    				$fontcolor = "";

				    				if($kills < 0)
				    				{
				    					$fontcolor = "style='color:#f7a600;'";
				    				}

				    				if($chave%2==0)
				    				{
					    				echo("<tr>");
					    				echo("<td class='td_left td_color_1'>$player</td>");
					    				echo("<td class='td_right td_color_1' ".$fontcolor.">$kills</td>");
					    				echo("</tr>");
					    			}
					    			else
					    			{
					    				echo("<tr>");
					    				echo("<td class='td_left td_color_2'>$player</td>");
					    				echo("<td class='td_right td_color_2' ".$fontcolor.">$kills</td>");
					    				echo("</tr>");
				    				}
				    			}
				    		}
				    		else
				    		{
				    			echo("<tr>");
			    				echo("<td class='td_left td_color_1'>Empty</td>");
			    				echo("<td class='td_right td_color_1'>0</td>");
			    				echo("</tr>");
				    		}
				    	?>
				    </tbody>
				    <tfoot>
				    </tfoot>
				</table>
				<br>
			</div>
		</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
		<script type="text/javascript">
			$("#form").submit(function(event){
			    event.preventDefault();
			    console.log("Iniciando requisicao...");
			    var form = $('#form').serialize();
			    console.log(form);
			    $.ajax({
				  method: "POST",
				  url: "utils.php",
				  data: form,
				  dataType: "json",
				  success: function (data) {
				  	if($.isEmptyObject(data.error)){
				  		console.log("Ok!");
				  		console.log(data);
				  		alert("O jogador " + data.nome_jogador + " tem " + data.sum_qtd_kills + " kills!");
				  	}else{
				  		console.log(data.error);
				  		alert("Jogador não encontrado no banco de dados!");
				  	}
				  },
				  error: function(data) {
				  	console.log('Erro na requisicao!!!');
				  	console.log(data);
				  }
				});
			});
		</script>
    </body>
</html>
