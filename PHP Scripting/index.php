<html>
	<head>
		<title> Weather Search </title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<style type="text/css">
			table.spl td{text-align:center;}
		</style>
	</head>
	<body>
	
		<h2 align="center"> Weather Search</h2>
		<div align="center">
		<form name="locationform" action="" method="post">
		<div style="border:1px solid black; width:290px; padding: 2px 2px; align:center;" >
		<table style="border:1px solid black;" >
			<tr><td>Location</td>
				<td><input name="location" type="text" id="locationid" maxlength="255"></td>
			</tr>
			<tr><td>Location Type</td>
				<td><select name="locationtype" id="locationtypeid">
						<option value="city" Selected>City</option>
						<option value="zipcode" id="ziptype"> ZIP Code</option>
					</select>
				</td>
			</tr>
			<tr><td>Temperature Unit</td>
				<td><input type="radio" name="temperatureUnit" value="f" checked="checked">Fahrenheit</input>				
				    <input type="radio" name="temperatureUnit" value="c" id="cel">Celsius</input>
			    </td>
			</tr>
			<tr><td></td><td><input type="submit" name="submit" value="Search"></input></td>
			</tr>
			</table>
			</div>
		</form>
		</div>

		
		<?php if(isset($_POST["submit"])): ?> 

			<?php if($_POST["location"]==""): ?>
				<script type="text/javascript">
					window.alert("Please enter a location");
				</script>
			
			<?php else: ?>
			<?php
			
			function getWOEID()
			{			
				$value_of_location=$_POST["location"];
				
				?>
				
					<script type="text/javascript">
						document.getElementById("locationid").value="<?php echo $value_of_location; ?>";
						<?php
						if($_POST["temperatureUnit"]=="c")
						{
							?>
								document.getElementById("cel").checked="checked";
							<?php
						}
						if($_POST["locationtype"]=="zipcode")
						{
							?>
							 document.getElementById("locationtypeid").value="zipcode";
							<?php
						}
						
						?>
					</script>
				
				<?php
				
				$my_yahoo_app_id="?appid=BRPSCFbV34Gkv94p7YnCS1ckDgu.2Zthw6R3NhPBTTOtldxIit84fH631Fb0dc9riB8apPm0";				
				if ($_POST["locationtype"]=="zipcode")
				{	
					$value_of_location=urlencode(trim($_POST["location"]));
					//echo $value_of_location;
					$match= '/^[0-9][0-9][0-9][0-9][0-9]$/';					
					if(preg_match($match,$value_of_location))
					{
						$url="http://where.yahooapis.com/v1/concordance/usps/".$value_of_location.$my_yahoo_app_id;
						//echo "<p>".$url."</p>";
						
						$getData=@file_get_contents($url);
						
						try
						{
							$woeid_xml=new SimpleXMLElement($getData);
						}
						catch(exception $e)
						{
							 echo "<h2 align=\"center\">Zero results found!</h2>";
							 return ;
							
						}
						
						$woeid=(int)($woeid_xml[0]->woeid);
						//echo "<p>".$woeid."</p>";
									
						$url_weather="http://weather.yahooapis.com/forecastrss?w=".$woeid."&u=".$_POST["temperatureUnit"]; 
						
						$weatherDetails=@file_get_contents($url_weather);
						$weatherXML=new SimpleXMLElement($weatherDetails);
						
						//echo "<p>".$weatherXML->channel->title."<p>";
						
						$namespaces=$weatherXML->getNameSpaces(true);
						
						$yweather=$weatherXML->channel->item->children($namespaces['yweather']);
						$temperature=$yweather->condition->attributes()->text." ".$yweather->condition->attributes()->temp."<sup>o</sup>".$weatherXML->channel->children($namespaces['yweather'])->units->attributes()->temperature;
						$alt = $yweather->condition->attributes()->text;
						
						$city=$weatherXML->channel->children($namespaces['yweather'])->location->attributes()->city;
						$region=$weatherXML->channel->children($namespaces['yweather'])->location->attributes()->region;
						$country=$weatherXML->channel->children($namespaces['yweather'])->location->attributes()->country;
						
						$geo=$weatherXML->channel->item->children($namespaces['geo']);
						$latitude=$weatherXML->channel->item->children($namespaces['geo'])->lat;
						$longitude=$weatherXML->channel->item->children($namespaces['geo'])->long;
						
						$link=$weatherXML->channel->item->description;
						$imgRegEx='/http\:[\/\.a-z0-9]+.gif/';
						preg_match($imgRegEx,$link,$imageLink);
						$detailsLink=$weatherXML->channel->link;
						
						if($imageLink=="")
						{	
							$imageLink="NA";
						}
						if($temperature=="")
						{
							$temperature="NA";
						}
						if($city=="")
						{
							$city="NA";
						}
						if($region=="")
						{
							$region="NA";
						}
						if($country=="")
						{
							$country="NA";
						}
						if($latitude=="")
						{
							$latitude="NA";
						}
						if($longitude=="")
						{
							$longitude="NA";
						}
						if($detailsLink=="")
						{
							$detailsLink="NA";
						}
						
						?>	
						<table class="spl" border="1" align="center">
						<caption>Result for zipcode<?php echo $_POST["location"]." "; ?></caption>
						<tr>
							<th>Weather</th><th>Temperature</th><th>City</th><th>Region</th><th>Country</th><th>Latitude</th>
							<th>Longitude</th><th>Link to Details</th>
						</tr>
						<tr>
							<td><a target="\_blank" href="<?php echo $url_weather; ?>"> <img src='<?php echo $imageLink[0]; ?>' alt='<?php echo $alt; ?>' title='<?php echo $alt; ?>' /></a></td>
							<td><?php echo $temperature; ?></td>
							<td><?php echo $city; ?></td>
							<td><?php echo $region; ?></td>
							<td><?php echo $country; ?></td>
							<td><?php echo $latitude; ?></td>
							<td><?php echo $longitude; ?></td>

							<td><a target="\_blank" href="<?php echo $detailsLink; ?>"> Details</a></td>
						</tr>
						</table>
						<?php
						
					}
					else
					{ ?>
							<script type="text/javascript">
							 window.alert("Invalid zipcode.Zipcode must be 5 digits");
						    </script>
					<?php 
					}
				}
				
				else
				{
								
					$value_of_location = preg_replace('/(\s+)/','_',$value_of_location);
				   // echo $value_of_location;
					$url="http://where.yahooapis.com/v1/places\$and(.q($value_of_location),.type(7));start=0;count=5".$my_yahoo_app_id;
					//echo $url;					

					$getData=@file_get_contents($url);
					$woeid_xml=new SimpleXMLElement($getData);
					if(($woeid_xml == NULL) || ($woeid_xml->count()==0))
					{	
						 ?>
							<script type="text/javascript">
							 window.alert("No records found for this location");
						    </script>
						<?php return;
					}
					//find out actual valid rows and count them
					
					$actual_count = 0;
					for($i=0; $i<$woeid_xml->count(); $i++)
					{
						$woeid=$woeid_xml->place[$i]->woeid;
						$url_weather="http://weather.yahooapis.com/forecastrss?w=".$woeid."&u=".$_POST["temperatureUnit"];
						$weatherDetails=@file_get_contents($url_weather);
						try
						{
						$weatherXML=new SimpleXMLElement($weatherDetails);
							if(($weatherXML ===false) ||
							   (!($namespaces = $weatherXML->getNameSpaces(true))))
							   {
								continue;
							   }
						}
						
						catch(exception $e)
						{
							continue;
						}
						
						$actual_count++;
						if($actual_count == 1) {
						?>	
						<table class="spl" border="1" align="center">
						
						<tr>
							<th>Weather</th><th>Temperature</th><th>City</th><th>Region</th><th>Country</th><th>Latitude</th>
							<th>Longitude</th><th>Link to Details</th>
						</tr>
						
					<?php }
						$namespaces=$weatherXML->getNameSpaces(true);
						
						$yweather=$weatherXML->channel->item->children($namespaces['yweather']);
						$temperature=$yweather->condition->attributes()->text." ".$yweather->condition->attributes()->temp."<sup>o</sup>".$weatherXML->channel->children($namespaces['yweather'])->units->attributes()->temperature;
						$alt = $yweather->condition->attributes()->text;
						
						$city=$weatherXML->channel->children($namespaces['yweather'])->location->attributes()->city;
						$region=$weatherXML->channel->children($namespaces['yweather'])->location->attributes()->region;
						$country=$weatherXML->channel->children($namespaces['yweather'])->location->attributes()->country;
						
						$geo=$weatherXML->channel->item->children($namespaces['geo']);
						$latitude=$weatherXML->channel->item->children($namespaces['geo'])->lat;
						$longitude=$weatherXML->channel->item->children($namespaces['geo'])->long;
						
						$link=$weatherXML->channel->item->description;
						$imgRegEx='/http\:[\/\.a-z0-9]+.gif/';
						preg_match($imgRegEx,$link,$imageLink);
						$detailsLink=$weatherXML->channel->link;
						
						if($imageLink=="")
						{	
							$imageLink="NA";
						}
						if($temperature=="")
						{
							$temperature="NA";
						}
						if($city=="")
						{
							$city="NA";
						}
						if($region=="")
						{
							$region="NA";
						}
						if($country=="")
						{
							$country="NA";
						}
						if($latitude=="")
						{
							$latitude="NA";
						}
						if($longitude=="")
						{
							$longitude="NA";
						}
						if($detailsLink=="")
						{
							$detailsLink="NA";
						}
						
						?>
						
						<tr>
						
						
						<td> <a target="\_blank" href="<?php echo $url_weather; ?>"> <img src='<?php echo $imageLink[0]; ?>' alt='<?php echo $alt; ?>' title='<?php echo $alt; ?>' /></a></td>
							<td><?php echo $temperature; ?></td>
							<td><?php echo $city; ?></td>
							<td><?php echo $region; ?></td>
							<td><?php echo $country; ?></td>
							<td><?php echo $latitude; ?></td>
							<td><?php echo $longitude; ?></td>
							<td><a target="\_blank" href="<?php echo $detailsLink; ?>"> Details</a></td>
							
						</tr>
						<?php
					}
					?>
					
					<caption>
						<?php if($actual_count==1)
						{
							$caption=$actual_count." result for city ".$_POST["location"];
						}
						
						else
						{
							$caption=$actual_count." result(s) for city ".$_POST["location"];
						}
						echo $caption;
						?>
						</caption>
					
					</table>
					<?php
					
				}
				
			}   //end of function getWOEID()
			
			
			?>
			
			
			<?php getWOEID(); ?>
				
			
			<?php endif; ?>
			
		<?php endif; ?>
	</body>
</html>