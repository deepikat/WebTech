<?php if(isset($_GET["location"]))
		{	
			$value_of_location=$_GET["location"];
			$temperatureUnit=$_GET["temperatureUnit"];
			$my_yahoo_app_id="?appid=BRPSCFbV34Gkv94p7YnCS1ckDgu.2Zthw6R3NhPBTTOtldxIit84fH631Fb0dc9riB8apPm0";				
			if ($_GET["locationtype"]=="zipcode")
			{	
					$value_of_location=urlencode(trim($_GET["location"]));

						$url="http://where.yahooapis.com/v1/concordance/usps/".$value_of_location.$my_yahoo_app_id;

						$getData=simplexml_load_file($url);
		
						$woeid=($getData->woeid);
	
						$url_weather="http://weather.yahooapis.com/forecastrss?w=".$woeid."&u=".$_GET["temperatureUnit"]; 

						$weatherDetails=simplexml_load_file($url_weather);

	
						$namespaces=$weatherDetails->getNameSpaces(true);
						
						$yweather=$weatherDetails->channel->item->children($namespaces['yweather']);
						$temperature=$yweather->condition->attributes()->text." ".$yweather->condition->attributes()->temp."<sup>o</sup>".$weatherDetails->channel->children($namespaces['yweather'])->units->attributes()->temperature;
						$alt = $yweather->condition->attributes()->text;
						$temperatureMeasure=$yweather->condition->attributes()->temp;
						$city=$weatherDetails->channel->children($namespaces['yweather'])->location->attributes()->city;
						$region=$weatherDetails->channel->children($namespaces['yweather'])->location->attributes()->region;
						$country=$weatherDetails->channel->children($namespaces['yweather'])->location->attributes()->country;
						
						$geo=$weatherDetails->channel->item->children($namespaces['geo']);
						$latitude=$weatherDetails->channel->item->children($namespaces['geo'])->lat;
						$longitude=$weatherDetails->channel->item->children($namespaces['geo'])->long;
						
						$link=$weatherDetails->channel->item->description;
						$imgRegEx='/http\:[\/\.a-z0-9]+.gif/';
						preg_match($imgRegEx,$link,$imageLink);
						$detailsLink=$weatherDetails->channel->link;
						
					$XMLback = new SimpleXMLElement("<weather></weather>");
                    //$XMLback->addChild("feed", $url_weather);
					$XMLback->feed=$url_weather;
                    //$XMLback->addChild("link", $detailsLink);
					$XMLback->link=$detailsLink;
                    $tagForLoc= $XMLback->addChild("location");
                    $tagForLoc->addAttribute('city',$city);
                    $tagForLoc->addAttribute('region',$region);
                    $tagForLoc->addAttribute('country',$country);
                   
                    $tagForUnit= $XMLback->addChild("units");
                    $tagForUnit->addAttribute('temperature',$temperatureUnit);
                   
                    $tagForCondition= $XMLback->addChild("condition");
                    $tagForCondition->addAttribute('text',$alt);
                    $tagForCondition->addAttribute('temp',$temperatureMeasure);
                   
                    $XMLback->addChild("img",$imageLink[0]);
                   
                    foreach($yweather->forecast as $iterator)
                    {
                        $tagForForecast=$XMLback->addChild("forecast");
                        $day=$iterator->attributes()->day;
                        $low=$iterator->attributes()->low;
                        $high=$iterator->attributes()->high;
                        $text_attr=$iterator->attributes()->text;
                        $tagForForecast->addAttribute('day',$day);
                        $tagForForecast->addAttribute('low',$low);
                        $tagForForecast->addAttribute('high',$high);
                        $tagForForecast->addAttribute('text',$text_attr);
                    }
                    
                    Header('Content-type: text/xml');
                    echo $XMLback->asXML();

				}
				else
				{
								
					$value_of_location = preg_replace('/(\s+)/','_',$value_of_location);

					$url="http://where.yahooapis.com/v1/places\$and(.q($value_of_location),.type(7));start=0;count=5".$my_yahoo_app_id;
				

					$getData=simplexml_load_file($url);


						$woeid=$getData->place[0]->woeid;
						$url_weather="http://weather.yahooapis.com/forecastrss?w=".$woeid."&u=".$_GET["temperatureUnit"];
						$weatherDetails=simplexml_load_file($url_weather);
						
						$namespaces=$weatherDetails->getNameSpaces(true);
						
						$yweather=$weatherDetails->channel->item->children($namespaces['yweather']);
						$temperature=$yweather->condition->attributes()->text." ".$yweather->condition->attributes()->temp."<sup>o</sup>".$weatherDetails->channel->children($namespaces['yweather'])->units->attributes()->temperature;
						$alt = $yweather->condition->attributes()->text;
						$temperatureMeasure=$yweather->condition->attributes()->temp;
						$city=$weatherDetails->channel->children($namespaces['yweather'])->location->attributes()->city;
						$region=$weatherDetails->channel->children($namespaces['yweather'])->location->attributes()->region;
						$country=$weatherDetails->channel->children($namespaces['yweather'])->location->attributes()->country;
						
						$geo=$weatherDetails->channel->item->children($namespaces['geo']);
						$latitude=$weatherDetails->channel->item->children($namespaces['geo'])->lat;
						$longitude=$weatherDetails->channel->item->children($namespaces['geo'])->long;
						
						$link=$weatherDetails->channel->item->description;
						$imgRegEx='/http\:[\/\.a-z0-9]+.gif/';
						preg_match($imgRegEx,$link,$imageLink);
						$detailsLink=$weatherDetails->channel->link;
					$XMLback = new SimpleXMLElement("<weather></weather>");
					
                   //$XMLback->addChild("feed", $url_weather);
					$XMLback->feed=$url_weather;
					
                   // $XMLback->addChild("link", $detailsLink);
					$XMLback->link=$detailsLink;
					
                    $tagForLoc= $XMLback->addChild("location");
                    $tagForLoc->addAttribute('city',$city);
                    $tagForLoc->addAttribute('region',$region);
                    $tagForLoc->addAttribute('country',$country);
                   
                    $tagForUnit= $XMLback->addChild("units");
                    $tagForUnit->addAttribute('temperature',$temperatureUnit);
                   
                    $tagForCondition= $XMLback->addChild("condition");
                    $tagForCondition->addAttribute('text',$alt);
                    $tagForCondition->addAttribute('temp',$temperatureMeasure);
                   
                    $XMLback->addChild("img",$imageLink[0]);
                   
                    foreach($yweather->forecast as $iterator)
                    {
                        $tagForForecast=$XMLback->addChild("forecast");
                        $day=$iterator->attributes()->day;
                        $low=$iterator->attributes()->low;
                        $high=$iterator->attributes()->high;
                        $text_attr=$iterator->attributes()->text;
                        $tagForForecast->addAttribute('day',$day);
                        $tagForForecast->addAttribute('low',$low);
                        $tagForForecast->addAttribute('high',$high);
                        $tagForForecast->addAttribute('text',$text_attr);
                    }
                    
                    Header('Content-type: text/xml');
                    echo $XMLback->asXML();
					

			}  
			
		}
?>