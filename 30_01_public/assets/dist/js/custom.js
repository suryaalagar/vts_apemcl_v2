// validate for tripcancellationForm
		$("#tripcancellationForm").validate({
			rules: {
				ccustomerName: {
					required: true,
					minlength:2,		
				},
				ccustomerPNR: {
					required: true,
					minlength:2,		
				},	
				ccustomerMobile: {
					required: true,
					number: true,
					maxlength:10
				},
				cancellationReason: {
					required: true,
					maxlength:600
				},
				canDescription: {
					required: true,
					maxlength:600
				},
				cfollowupDescription: {
					required: true,
					maxlength:600
				}				
			},
			messages: {	
			}
		});		
	
    $("#bcustomerName,#customerName,#ccustomerName,#fcustomerName,#fccustomerName").keypress(function (e){
    var code =e.keyCode || e.which;
       if((code<65 || code>90)
       &&(code<97 || code>122)&&code!=32&&code!=46)  
      {
       //alert("Only alphabates are allowed");
       return false;
      }
    });
	$("#mobileNo,#customerMobile,#ccustomerMobile,#fcustomerMobile,#fccustomerMobile").keypress(function (e1){
    var code =e1.keyCode || e1.which;
       if(code > 31 && (code < 48 || code > 57))
       
      {
      // alert("Plz Enter Valid Mobile No");
       return false;
      }
    });

	
	
	
	
	
	
	 jQuery().ready(function() {

    // validate form on keyup and submit
    var v = jQuery("#bookingForm").validate({
      rules: {
        bcustomerName: {
          required: true,
          minlength: 2,
          maxlength: 16
        },
        cemail: {
          minlength: 2,
          email: true,
          maxlength: 100,
        },
		cmobileNo: {
          required: true,
          minlength: 10,
          maxlength: 10,
		  number: true
        },
		cphoneNo: {
          minlength: 10,
          maxlength: 10,
		  number: true
        },
		ccustomerType: {
          required: true,
        },
		caddress: {
          maxlength: 170,
        },
		pickupTime: {
		  required: true,
        },
		weight: {
		  required: true,
		  maxlength: 4,
        },
		weightMeasurement: {
		  required: true,
        },
		pickupLocation: {
		  required: true,
        },		
		dropLocation: {
		  required: true,
        },				
		plan: {
		  required: true,
        },
		tripcategory: {
		  required: true,
        },		
		
        
      },
      errorElement: "span",
      errorClass: "help-inline-error",
    });

    $(".open1").click(function() {
      if (v.form()) {
        $(".frm").hide("fast");
        $("#sf2").show("slow");
      }
    });
	$(".open2").click(function() {
      if (v.form()) {
        $(".frm").hide("fast");
        $("#sf3").show("slow");
      }
    });
   
	
  });
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
var source, destination;
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
google.maps.event.addDomListener(window, 'load', function () {
   // new google.maps.places.SearchBox(document.getElementById('pickupLocation'));
   // new google.maps.places.SearchBox(document.getElementById('dropLocation'));
    directionsDisplay = new google.maps.DirectionsRenderer({ 'draggable': false });
});
 
 
 
 
function GetRoute() {
    var loc = new google.maps.LatLng(11.0168445, 76.95583209999995);
    var mapOptions = {
        zoom: 10,
        center: loc
    };
	
    setTimeout(function(){
		//alert();
            map = new google.maps.Map(document.getElementById('dvMap'), mapOptions);
            directionsDisplay.setMap(map);
            
    },700);

    //*********DIRECTIONS AND ROUTE**********************//
    source = document.getElementById("pickupLocation").value;
    destination = document.getElementById("dropLocation").value;
 
    var request = {
        origin: source,
        destination: destination,
        travelMode: google.maps.TravelMode.DRIVING
    };
    directionsService.route(request, function (response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
        }
    });
	
	//***********Lat lng *******************//
	
	var source = source;
	var destination = destination;
	
	getLatitudeLongitude(showResult, source);
	
	getLatitudeLongitude(showResult1, destination);
	
	function showResult(result) {
		//alert(result.geometry.location.lat());
		document.getElementById('pickupLocationLat').value = result.geometry.location.lat();
		document.getElementById('pickupLocationLng').value = result.geometry.location.lng();
	}
	
	function showResult1(result) {
		//alert(result.geometry.location.lat());
		document.getElementById('dropLocationLat').value = result.geometry.location.lat();
		document.getElementById('dropLocationLng').value = result.geometry.location.lng();
	}
	
	function getLatitudeLongitude(callback, address) {
		// If adress is not supplied, use default value 'Ferrol, Galicia, Spain'
		address = address || 'Ferrol, Galicia, Spain';
		// Initialize the Geocoder
		geocoder = new google.maps.Geocoder();
		if (geocoder) {
			geocoder.geocode({
				'address': address
			}, function (results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					callback(results[0]);
				}
			});
		}
	}
		
	
    //*********DISTANCE AND DURATION**********************//
    var service = new google.maps.DistanceMatrixService();
	
    service.getDistanceMatrix({
        origins: [source],
        destinations: [destination],
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.METRIC,
        avoidHighways: false,
        avoidTolls: false
    }, function (response, status) {
		
        if (status == google.maps.DistanceMatrixStatus.OK && response.rows[0].elements[0].status != "ZERO_RESULTS") {
			
            var distance = response.rows[0].elements[0].distance.text;
			
            var dvDistance = document.getElementById("dvDistance");
			var Distanceprice = document.getElementById("Distanceprice");
			var Waitingprice = document.getElementById("waitingprice");
			var Subtotal = document.getElementById("subtotal");
			var Amounttopay = document.getElementById("amounttopay");
			var Packageprice = document.getElementById("Packageprice");
			var Packagetime = document.getElementById("Packagetime");
			var Packagekm = document.getElementById("Packagekm");
			var Charegeperkm = document.getElementById("Charegeperkm");
			var ServiceTax = document.getElementById("ServiceTax");
			var ServiceTaxPrice = document.getElementById("ServiceTaxPrice");
			var CancelPrice = document.getElementById("CancelFair");
			
            dvDistance.innerHTML = "";
			Distanceprice.innerHTML = "";
			Waitingprice.innerHTML = "";
			Subtotal.innerHTML = "";
			Amounttopay.innerHTML = "";
			CancelPrice.innerHTML = "";
            dvDistance.innerHTML += distance;
			
			Packageprice.innerHTML = "";
			Packagetime.innerHTML = "";
			Packagekm.innerHTML = "";
			Charegeperkm.innerHTML = "";
			ServiceTax.innerHTML = '18%';
			ServiceTaxPrice.innerHTML = "";
			
			$('#PromoDiscount').empty();
			$('#cpromoocode').val('');
			$('#discountdetails').empty();
			$('#cpromocheck').removeClass('disabled');
			
			
			$('#approxamount').val('');
			// **********booking form values *************//
			var b_distance = "";
			
			var c_id = $("#bcustomerId").val();
			var b_type = $("input[name=tripcategory]").val();
			var b_plan = $("#plan").val();
			var t_type = $("#tarifftype").val();
			var t_mode = $("#transportmode").val();			
			var b_package = $("#package").val();
			var b_loadtime = $("#pickupTime").val();			
			var b_weight = $("#weight").val();
			var p_model = $("#preferredModelId").val();	
			
			    var b_distance = distance.replace(/[^\d.-]/g, '');			
				$('#approxkm').val(b_distance);
			//convert b_loadtime to 24 hrs
			    var hours = Number(b_loadtime.match(/^(\d+)/)[1]);
				var minutes = Number(b_loadtime.match(/:(\d+)/)[1]);
				var AMPM = b_loadtime.match(/\s(.*)$/)[1];
				if(AMPM == "PM" && hours<12) hours = hours+12;
				if(AMPM == "AM" && hours==12) hours = hours-12;
				var sHours = hours.toString();
				var sMinutes = minutes.toString();
				if(hours<10) sHours = "0" + sHours;
				if(minutes<10) sMinutes = "0" + sMinutes;
				
				b_loadtime = sHours+':'+sMinutes;
				
				$.ajax({
				url: '<?php echo site_url('/price_calculation/aprox_price/');?>',
				type: 'POST',
				dataType : "json",
				data : {"c_id" : c_id,
						"b_type" : b_type, 				
						"b_plan" : b_plan,
						"t_type" : t_type, 
						"t_mode" : t_mode, 
						"b_package" : b_package, 
						"b_loadtime" : b_loadtime, 
						"b_weight" : b_weight, 
						"p_model" : p_model,
						"b_distance" : b_distance},
				success: function(data) {
					
					 //console.log(data);
					 
					//alert(data.cancel_amount);
					
					var service_tax_percent = 18;
					
					var cancel_charge = parseFloat(data.cancel_amount);
						cancel_charge = cancel_charge.toFixed(2);
						
						//alert(cancel_charge);
					CancelPrice.innerHTML += cancel_charge;
					$('#cancel_price').val(cancel_charge);
					if(t_type == '1'){ //km tariff
					
						Waitingprice.innerHTML += data.amount_waiting_per_min;					
						
						if( b_loadtime < '22:00'){ //day tariff
							
							if(b_plan == '1'){ // bike 
								//alert('1');
								
								distance_price = data.amount_min_day_km;				
								
								
							}else if(b_plan == '2'){ // shared
							
								//alert('1');
									if(t_mode == '2'){
										
										var express_charge	= data.amount_min_day_km / 2;	
										
										distance_price = parseFloat(data.amount_min_day_km) + parseFloat(express_charge);
									
									}else{
									
										distance_price = data.amount_min_day_km;
									
									}								
								
							}else // partial and full
							{	
								//alert(b_distance);
								if(b_distance < 5){
									
									distance_price = data.amount_min_day_km;
									
								}else{
									
									var amount_min_day_km = parseFloat(data.amount_min_day_km);
									b_distance = b_distance - 5;
									
									distance_price = (data.amount_day_km * b_distance) + amount_min_day_km;
									distance_price = distance_price.toFixed(2);
								}
								
							}
												
							sub_total = distance_price;
							
							var service_tax_amount = (sub_total / 100) * service_tax_percent;
							
							service_tax_amount = service_tax_amount.toFixed(2);
							
							ServiceTaxPrice.innerHTML += service_tax_amount;
								$('#servicetax_price').val(service_tax_amount);
							sub_total = parseFloat(sub_total) + /*parseFloat(service_tax_amount) +*/ parseFloat(cancel_charge);
							
							amount_to_pay = parseFloat(sub_total) + parseFloat(service_tax_amount);
							amount_to_pay = amount_to_pay.toFixed(2);
							
							$('#baseprice').val(sub_total);
							$('#approxamount').val(amount_to_pay);
							
							Distanceprice.innerHTML += distance_price;					
							Subtotal.innerHTML += sub_total;
							Charegeperkm.innerHTML += data.amount_day_km;
														
							Amounttopay.innerHTML += amount_to_pay;
					
						}else // night tariff
						{				
							if(b_plan < '3'){ // bike and shared
								
								distance_price = data.amount_min_night_km;
								
							}else // partial and full
							{	
								if(b_distance < 5){
									
									distance_price = data.amount_min_night_km;
									
								}else{
									
									var amount_min_night_km = parseFloat(data.amount_min_night_km);
									b_distance = b_distance - 5;
									
									distance_price = (data.amount_night_km * b_distance) + amount_min_night_km;
									distance_price = distance_price.toFixed(2);
									
								}
								
							}
							
							sub_total = distance_price;
							
							var service_tax_amount = (sub_total / 100) * service_tax_percent;
							
							service_tax_amount = service_tax_amount.toFixed(2);
							
							ServiceTaxPrice.innerHTML += service_tax_amount;
							$('#servicetax_price').val(service_tax_amount);
							sub_total = parseFloat(sub_total) +/* parseFloat(service_tax_amount) +*/ parseFloat(cancel_charge);
							
							amount_to_pay = parseFloat(sub_total) + parseFloat(service_tax_amount);
							amount_to_pay = amount_to_pay.toFixed(2);
							$('#baseprice').val(sub_total);
							$('#approxamount').val(amount_to_pay);
					
							Distanceprice.innerHTML += distance_price;					
							Subtotal.innerHTML += sub_total;
							Charegeperkm.innerHTML += data.amount_night_km;
							
							Amounttopay.innerHTML += amount_to_pay;
						}
						
						
					}else //pack tariff
					{
						var pac_time_hours = Number(data.package_time.match(/^(\d+)/)[1]);
						var pac_time_minutes = Number(data.package_time.match(/:(\d+)/)[1]);
						
						//alert(pac_time_minutes);					
						
						Packagetime.innerHTML += pac_time_hours+':'+pac_time_minutes;    
						Waitingprice.innerHTML += data.amount_waiting_per_min;					
						
						if( b_loadtime < '22:00'){
											
							package_price = data.amount_min_day_km;
						
							sub_total = package_price;
														
							var service_tax_amount = (sub_total / 100) * service_tax_percent;
							
							service_tax_amount = service_tax_amount.toFixed(2);
							
							ServiceTaxPrice.innerHTML += service_tax_amount;
							$('#servicetax_price').val(service_tax_amount);
							sub_total = parseFloat(sub_total) +/* parseFloat(service_tax_amount) +*/ parseFloat(cancel_charge);
							
							amount_to_pay = parseFloat(sub_total) + parseFloat(service_tax_amount);
							amount_to_pay = amount_to_pay.toFixed(2);
							$('#baseprice').val(sub_total);
							$('#approxamount').val(amount_to_pay);						
						
							Packageprice.innerHTML += package_price;					
							Subtotal.innerHTML += sub_total;
							Packagekm.innerHTML += data.min_day_km;
							Charegeperkm.innerHTML += data.amount_min_day_km;
							
							Amounttopay.innerHTML += amount_to_pay;
						
						}else
						{
						
							package_price = data.amount_min_night_km;
						
							sub_total = package_price;
							
							var service_tax_amount = (sub_total / 100) * service_tax_percent;
							
							service_tax_amount = service_tax_amount.toFixed(2);
							
							ServiceTaxPrice.innerHTML += service_tax_amount;
							$('#servicetax_price').val(service_tax_amount);
							sub_total = parseFloat(sub_total) +/* parseFloat(service_tax_amount) +*/ parseFloat(cancel_charge);
							
							amount_to_pay = parseFloat(sub_total) + parseFloat(service_tax_amount);
							amount_to_pay = amount_to_pay.toFixed(2);
							$('#baseprice').val(sub_total);
							$('#approxamount').val(amount_to_pay);
						
							Packageprice.innerHTML += package_price;					
							Subtotal.innerHTML += sub_total;
							Packagekm.innerHTML += data.min_night_km;
							Charegeperkm.innerHTML += data.amount_min_night_km;
														
							Amounttopay.innerHTML += amount_to_pay;							
							
						}
						
						
					}
					
					
				},
				error: function(){   
					//alert('Error while request data..');
					console.log('Error while request data..');
				}        
				});
		
		
			
        } else {
			
            //alert("Unable to find the distance via road.");
			console.log("Unable to find the distance via road.");
        
		}
    });
}

















   $(document).ready(function () {
	   //alert();
	   
		 $("#pickupLocation").focus(function(){
			// alert();
			 $('#pickup_map_canvas').removeClass('hide');
			 		setTimeout(function(){
         var lat = 11.0168445,
             lng = 76.95583209999995,
             latlng = new google.maps.LatLng(lat, lng),
             image = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png';

         //zoomControl: true,
         //zoomControlOptions: google.maps.ZoomControlStyle.LARGE,

         var mapOptions = {
             center: new google.maps.LatLng(lat, lng),
             zoom: 13,
             mapTypeId: google.maps.MapTypeId.ROADMAP,
             panControl: true,
             panControlOptions: {
                 position: google.maps.ControlPosition.TOP_RIGHT
             },
             zoomControl: true,
             zoomControlOptions: {
                 style: google.maps.ZoomControlStyle.LARGE,
                 position: google.maps.ControlPosition.TOP_left
             }
         },
         map = new google.maps.Map(document.getElementById('pickup_map_canvas'), mapOptions),
             marker = new google.maps.Marker({
                 position: latlng,
                 map: map,
                 icon: image,
				 draggable: true
             });

         var input = document.getElementById('pickupLocation');
         var autocomplete = new google.maps.places.Autocomplete(input, {
             types: ["geocode"]
         });

         autocomplete.bindTo('bounds', map);
         var infowindow = new google.maps.InfoWindow();

         google.maps.event.addListener(autocomplete, 'place_changed', function (event) {
             infowindow.close();
             var place = autocomplete.getPlace();
             if (place.geometry.viewport) {
                 map.fitBounds(place.geometry.viewport);
             } else {
                 map.setCenter(place.geometry.location);
                 map.setZoom(10);
             }

             moveMarker(place.name, place.geometry.location);
			 //alert(place.geometry.location.lat());
             //$('#pickupLocationLat').val(place.geometry.location.lat());
             //$('#pickupLocationLng').val(place.geometry.location.lng());
         });
		
         google.maps.event.addListener(map, 'click', function (event) {
            // $('.MapLat').val(event.latLng.lat());
             //$('.MapLon').val(event.latLng.lng());
             infowindow.close();
                     var geocoder = new google.maps.Geocoder();
                     geocoder.geocode({
                         "latLng":event.latLng
                     }, function (results, status) {
                         console.log(results, status);
                         if (status == google.maps.GeocoderStatus.OK) {
                             console.log(results);
                             var lat = results[0].geometry.location.lat(),
                                 lng = results[0].geometry.location.lng(),
                                 placeName = results[0].address_components[0].long_name,
                                 latlng = new google.maps.LatLng(lat, lng);

                             moveMarker(placeName, latlng);
                             $("#pickupLocation").val(results[0].formatted_address);							
							 //$('#pickupLocationLat').val(lat);
             				 //$('#pickupLocationLng').val(lng);
                         }
                     });
         });
		 
        initialize();
         function moveMarker(placeName, latlng) {
             marker.setIcon(image);
             marker.setPosition(latlng);
             infowindow.setContent(placeName);
             //infowindow.open(map, marker);
         }
		 },100); 
		 });
		 $("#pickupLocation").blur(function(){
			 
			 if($('#pickupLocation').val() == ''){
				// alert();
			 $('#pickup_map_canvas').addClass('hide');
			 
			 }else{
			 	//alert();
				$('.close-map1').removeClass('hide');
				
				$('.close-map1').click(function(){
				 $('#pickup_map_canvas').addClass('hide');		
				 $('.close-map1').addClass('hide');
				});
			 }
			 		 
		 });
		 
		 
		 $("#dropLocation").focus(function(){
	     $('#drop_map_canvas').removeClass('hide');
			 		setTimeout(function(){
         var lat = 11.0168445,
             lng = 76.95583209999995,
             latlng = new google.maps.LatLng(lat, lng),
             image = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png';

         //zoomControl: true,
         //zoomControlOptions: google.maps.ZoomControlStyle.LARGE,

         var mapOptions = {
             center: new google.maps.LatLng(lat, lng),
             zoom: 13,
             mapTypeId: google.maps.MapTypeId.ROADMAP,
             panControl: true,
             panControlOptions: {
                 position: google.maps.ControlPosition.TOP_RIGHT
             },
             zoomControl: true,
             zoomControlOptions: {
                 style: google.maps.ZoomControlStyle.LARGE,
                 position: google.maps.ControlPosition.TOP_left
             }
         },
         map = new google.maps.Map(document.getElementById('drop_map_canvas'), mapOptions),
             marker = new google.maps.Marker({
                 position: latlng,
                 map: map,
                 icon: image,
				 draggable: true
             });

         var input = document.getElementById('dropLocation');
         var autocomplete = new google.maps.places.Autocomplete(input, {
             types: ["geocode"]
         });

         autocomplete.bindTo('bounds', map);
         var infowindow = new google.maps.InfoWindow();

         google.maps.event.addListener(autocomplete, 'place_changed', function (event) {
             infowindow.close();
             var place = autocomplete.getPlace();
             if (place.geometry.viewport) {
                 map.fitBounds(place.geometry.viewport);
             } else {
                 map.setCenter(place.geometry.location);
                 map.setZoom(17);

             }

             moveMarker(place.name, place.geometry.location);
            // $('#dropLocationLat').val(place.geometry.location.lat());
            // $('#dropLocationLng').val(place.geometry.location.lng());
         });
		 
         google.maps.event.addListener(map, 'click', function (event) {
            // $('.MapLat').val(event.latLng.lat());
             //$('.MapLon').val(event.latLng.lng());
             infowindow.close();
                     var geocoder = new google.maps.Geocoder();
                     geocoder.geocode({
                         "latLng":event.latLng
                     }, function (results, status) {
                         console.log(results, status);
                         if (status == google.maps.GeocoderStatus.OK) {
                             console.log(results);
                             var lat = results[0].geometry.location.lat(),
                                 lng = results[0].geometry.location.lng(),
                                 placeName = results[0].address_components[0].long_name,
                                 latlng = new google.maps.LatLng(lat, lng);

                             moveMarker(placeName, latlng);
                             $("#dropLocation").val(results[0].formatted_address);
							// $('#dropLocationLat').val(lat);
             				// $('#dropLocationLng').val(lng);
                         }
                     });
         });
		 
        initialize();
         function moveMarker(placeName, latlng) {
             marker.setIcon(image);
             marker.setPosition(latlng);
             infowindow.setContent(placeName);
             //infowindow.open(map, marker);
         }
		 },100); 
		 });
		 $("#dropLocation").blur(function(){
			 
			 if($('#dropLocation').val() == ''){
				// alert();
			 $('#drop_map_canvas').addClass('hide');
			 
			 }else{
			 	//alert();
				$('.close-map2').removeClass('hide');
				
				$('.close-map2').click(function(){
				 $('#drop_map_canvas').addClass('hide');		
				 $('.close-map2').addClass('hide');
				});
			 }
			 		 
		 });
		 
     });	
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	$(document).ready(function(){
		
		$('.timepicker').timepicker({
					timeFormat: 'h:mm p',
					scrollbar: true,
					dynamic: false,
					dropdown: true,
					scrollbar: true,
					'minTime': new Date(),
		});		
				
		$("#scheduleType").change(function(){
			
			if($('#scheduleType').val() == 0){
				//alert();
				
				 $('.timepicker').timepicker('option', 'minTime', '12:00am');
				
			}else{
				 $('.timepicker').timepicker('option', 'minTime', new Date());
			}
		});
	});
    $('.datepicker').datepicker({
        format: 'dd-mm-yyyy',
		startDate: '+0d',
        autoclose: true,
		defaultDate: new Date()
    });
	
    //hide and show preferred model based on plan value
	$('#plan').change(function(){
		//alert($('#plan').val());
		if($('#plan').val() == '1' || $('#plan').val() == '2'){
			//alert();
			$('.plan-box').addClass('hide');
			$('#preferredModelId').val('');
			$('#plan_disable').addClass('hide');
			$('#tarifftype').val('1');
			
			$('.pack-box').addClass('hide');
			$('#tons').addClass('hide');
			$('#weightMeasurement').val('0');
		
		}else{
			//alert();
			$('.plan-box').removeClass('hide');
			$('#preferredModelId').val('');
			
			$('#plan_disable').removeClass('hide');
			$('#tons').removeClass('hide');
			
		}
		
	});
	
	$('#tarifftype').change(function(){
		//alert($('#plan').val());
		if($('#tarifftype').val() == '2'){
			
			$('.pack-box').removeClass('hide');
			$('.txt-show').removeClass('hide');
			$('.txt-hide').addClass('hide');
		}else{
			//alert();
			$('#package').val('');
			$('.pack-box').addClass('hide');
			$('.txt-show').addClass('hide');
			$('.txt-hide').removeClass('hide');
		}
		
	});
	
	//Restrict weignt basedd on plan
	$("#weight").blur(function(){
				//alert();
			if(($('#plan').val() == 1) && ($('#weightMeasurement').val() == 0) && ($('#weight').val() > 5))
			{
				
				toAppend = '<b class="red">More than 5 Kg not Allowed..!</b>';
				$('#weignt-msg').empty().append(toAppend);
				$('.open2').addClass('hide');
				
				
			}else if(($('#plan').val() == 2) && ($('#weightMeasurement').val() == 0) && ($('#weight').val() > 50))
			{
				
				toAppend = '<b class="red">More than 50 Kg not Allowed..!</b>';
				$('#weignt-msg').empty().append(toAppend);
				$('.open2').addClass('hide');
				
			}else if(($('#plan').val() == 3) && ($('#weightMeasurement').val() == 1) && ($('#weight').val() > 1))
			{
				
				toAppend = '<b class="red">More than 1 ton not Allowed..!</b>';
				$('#weignt-msg').empty().append(toAppend);
				$('.open2').addClass('hide');
				
			}else if(($('#plan').val() == 3) && ($('#weightMeasurement').val() == 0) && ($('#weight').val() > 1000))
			{
				
				toAppend = '<b class="red">More than 1 ton not Allowed..!</b>';
				$('#weignt-msg').empty().append(toAppend);
				$('.open2').addClass('hide');
				
			}else if(($('#plan').val() == 4) && ($('#weightMeasurement').val() == 1) && ($('#weight').val() > 1.5))
			{
				
				toAppend = '<b class="red">More than 1.5 ton not Allowed..!</b>';
				$('#weignt-msg').empty().append(toAppend);
				$('.open2').addClass('hide');
				
			}else if(($('#plan').val() == 4) && ($('#weightMeasurement').val() == 0) && ($('#weight').val() > 1500))
			{
				
				toAppend = '<b class="red">More than 1.5 ton not Allowed..!</b>';
				$('#weignt-msg').empty().append(toAppend);
				$('.open2').addClass('hide');
				
			}else if(($('#plan').val() == 1) && ($('#weightMeasurement').val() == 0) && $('#weight').val() == 0){
				
				toAppend = '<b class="red">Invalid weight..!</b>';
				$('#weignt-msg').empty().append(toAppend);
				$('.open2').addClass('hide');
				
			}else if($('#weight').val() == '0'){
				
				toAppend = '<b class="red">Invalid weight..!</b>';
				$('#weignt-msg').empty().append(toAppend);
				$('.open2').addClass('hide');
				
			}else{
				
				$('#weignt-msg').empty();
				$('.open2').removeClass('hide');
			}
					 
		 });
		 
		 
		 $("#weightMeasurement").change(function(){
				//alert();
			if(($('#plan').val() == 3) && ($('#weightMeasurement').val() == 1) && ($('#weight').val() > 1))
			{
				
				toAppend = '<b class="red">More than 1 ton not Allowed..!</b>';
				$('#weignt-msg').empty().append(toAppend);
				$('.open2').addClass('hide');
				
			}else if(($('#plan').val() == 3) && ($('#weightMeasurement').val() == 0) && ($('#weight').val() > 1000))
			{
				
				toAppend = '<b class="red">More than 1 ton not Allowed..!</b>';
				$('#weignt-msg').empty().append(toAppend);
				$('.open2').addClass('hide');
				
			}else if(($('#plan').val() == 4) && ($('#weightMeasurement').val() == 1) && ($('#weight').val() > 1.5))
			{
				
				toAppend = '<b class="red">More than 1.5 ton not Allowed..!</b>';
				$('#weignt-msg').empty().append(toAppend);
				$('.open2').addClass('hide');
				
			}else if(($('#plan').val() == 4) && ($('#weightMeasurement').val() == 0) && ($('#weight').val() > 1500))
			{
				
				toAppend = '<b class="red">More than 1.5 ton not Allowed..!</b>';
				$('#weignt-msg').empty().append(toAppend);
				$('.open2').addClass('hide');
				
			}else{
				
				$('#weignt-msg').empty();
				$('.open2').removeClass('hide');
			}
					 
		 });
	
	
  $(function() {
  	$('#fctype').change(function(){		
		$('.cd-hide').toggleClass('hide show');
		$('.fb-hide').toggleClass('show hide');
	});
	
    $('#callType').change(function(){
    
        $('.c-box').addClass('hide');
        
        switch ($('#callType').val()) {
            case '1':
                $('.c-hide-1').addClass('hide');
                $('.c-hide-4').removeClass('hide');
                break;
            case '2':
                $('.c-hide-4').addClass('hide');
                $('.c-hide-1').removeClass('hide');
                $('#callTypeEntry1').removeClass('hide');
                break;
            case '3':
				$('.c-hide-4').addClass('hide');
				$('.c-hide-1').addClass('hide');
                $('#callTypeEntry2').removeClass('hide');
                break;
            case '4':			
				$('.c-hide-4').addClass('hide');
				$('.c-hide-1').addClass('hide');
                $('#callTypeEntry3').removeClass('hide');
                break;
            case '5':
				$('.c-hide-4').addClass('hide');
				$('.c-hide-1').addClass('hide');
                $('#callTypeEntry4').removeClass('hide');
                break;
            case '6':			
				$('.c-hide-4').addClass('hide');
				$('.c-hide-1').addClass('hide');
                $('#callTypeEntry5').removeClass('hide');
                break;
        }
        
    });
    
    $('#scheduleType').change(function(){
         if($('#scheduleType').val() == 0){
             $(".c-date").removeClass('hide');
			 $(".c-time").toggleClass('col-md-5 col-md-3');
         }else{
            $(".c-date").addClass('hide');
			$(".c-time").toggleClass('col-md-5 col-md-3');
         }
    });
    
    $(".c-reject").hide();
    //$(".c-time").hide();
    //$(".c-date").hide();
    $('input').on('ifChecked', function(event){			
        //alert($(this).val()); 
        if ($(this).attr("value") == "new") {
            $(".c-reject").hide();
            //$(".c-time").hide();
            //$(".c-date").hide();
        }
        if ($(this).attr("value") == "later") {

            $(".c-reject").hide();
            //$(".c-time").show();
            //$(".c-date").show();
        }
        if ($(this).attr("value") == "reject") {
            //$(".c-time").hide();
            //$(".c-date").hide();
             $(".c-reject").show();
        }
    });
  });
  
$(window).keydown(function(event) {
 
 
  if(event.ctrlKey && event.keyCode == 66) { 
    //alert("Hey! Ctrl+b event captured!");
    event.preventDefault(); 
$('#callType').val('2');
                $('.c-hide-4').addClass('hide');
                $('#callTypeEntry6').addClass('hide');

				$('#callTypeEntry2').addClass('hide');

				$('#callTypeEntry3').addClass('hide');

				$('#callTypeEntry4').addClass('hide');

				$('#callTypeEntry5').addClass('hide');

				$('#callTypeEntry1').removeClass('hide');	
  }
  if(event.ctrlKey && event.keyCode == 67) { 
    //alert("Hey! Ctrl+c event captured!");
    event.preventDefault();
 $('#callType').val('4');
				$('.c-hide-4').addClass('hide');				
                $('#callTypeEntry1').addClass('hide');
				$('#callTypeEntry2').addClass('hide');
				$('#callTypeEntry6').addClass('hide');
				$('#callTypeEntry4').addClass('hide');
				$('#callTypeEntry5').addClass('hide');
                $('#callTypeEntry3').removeClass('hide'); 
  }
  if(event.ctrlKey && event.keyCode == 88) { 
   // alert("Hey! Ctrl+x event captured!");
    event.preventDefault(); 
 $('#callType').val('1');
                $('.c-hide-4').removeClass('hide');
                $('#callTypeEntry1').addClass('hide');
				$('#callTypeEntry2').addClass('hide');
				$('#callTypeEntry3').addClass('hide');
				$('#callTypeEntry4').addClass('hide');
				$('#callTypeEntry5').addClass('hide');
				$('#callTypeEntry6').addClass('hide');
  }
  
});
    
	
	
	
	
	
	
	
	
	
	
	 $(document).ready(function(){  
  
  //get booking customer name 
  var dataList = document.getElementById('json-bcusnamelist');
    
  $("#bcustomerName").keyup(function(){
   //alert();
  if($("#bcustomerName").val().length>=1){
    //alert();
    var data = $(this).val();

    $.ajax({
        url: '<?php echo site_url('/customer/customer_detail/');?>'+ data,
        type: 'POST',			
        success: function(data) {
            //alert();
            var obj = JSON.parse(data);
            var total = obj.length;
            var toAppend = '';
            var items=[]; 
                $.each(obj,function(i,o){
                toAppend += '<option>'+o.customer_name+'</option>';
                //items.push($('<option>').text(o.LocationName));			
            });
            //$('#json-loclist').append.apply($('#json-loclist'), items);
         $('#json-bcusnamelist').empty().append(toAppend);
		 
		 $("#bcustomerName").blur(function(){
			// $(this).val= data.customer_name;			 
			 for(var i=0;i<total;i++){
				 if(obj[i].customer_name == $("#bcustomerName").val()){
					// alert(obj[i].customer_name);
					$('#bcustomerId').val(obj[i].id);					
       				$('#cemail').val(obj[i].email_id);
		 			$('#cmobileNo').val(obj[i].mobile_number);
					$('#cphoneNo').val(obj[i].contact_number);
		 			$('#cgender').val(obj[i].gender);
					$('#ccustomerType').val(obj[i].customer_type);
		 			$('#caadhar').val(obj[i].aadhar_card_number);
					$('#fileupload').val(obj[i].upath_aadhar_card);	
					$('#caddress').val(obj[i].address);	
										
					 break;
				 }			 	
			 }			 
		 });
        },
        error: function(){   
            //alert('Error while request Location List..');
			console.log('Error while request customer name.');
        }        
    });
    }
  });	
  
  
  
   //get booking customer mobile 
  var dataList = document.getElementById('json-cmobileNo');
    
  $("#cmobileNo").keyup(function(){
  //alert();
  if($("#cmobileNo").val().length>=1){
    //alert();
    var data = $(this).val();

    $.ajax({
        url: '<?php echo site_url('/customer/customer_detail/');?>'+ data,
        type: 'POST',			
        success: function(data) {
            //alert();
            var obj = JSON.parse(data);
            var total = obj.length;
            var toAppend = '';
            var items=[]; 
                $.each(obj,function(i,o){
                toAppend += '<option>'+o.mobile_number+'</option>';
                //items.push($('<option>').text(o.LocationName));			
            });
            //$('#json-loclist').append.apply($('#json-loclist'), items);
         $('#json-cmobileNo').empty().append(toAppend);
		 
		 $("#cmobileNo").blur(function(){
			// $(this).val= data.Mobile;			 
			 for(var i=0;i<total;i++){
				 if(obj[i].mobile_number == $("#cmobileNo").val()){
					// alert(obj[i].Mobile);
					$('#bcustomerId').val(obj[i].id);					
       				$('#cemail').val(obj[i].email_id);
		 			$('#bcustomerName').val(obj[i].customer_name);
					$('#cmobileNo').val(obj[i].mobile_number);					
					$('#cphoneNo').val(obj[i].contact_number);
		 			$('#cgender').val(obj[i].gender);
					$('#ccustomerType').val(obj[i].customer_type);
		 			$('#caadhar').val(obj[i].aadhar_card_number);
					$('#fileupload').val(obj[i].upath_aadhar_card);	
					$('#caddress').val(obj[i].address);	
					
					 break;
				 }			 	
			 }
		 });
        },
        error: function(){   
            //alert('Error while request Location List..');
			console.log('Error while request customer mobile no.');
        }        
    });
    }
  });	
  
  
  
  
  
   //get cancel customer mobile 
  var dataList = document.getElementById('json-cpnrlist');
    
  $("#ccustomerPNR").keyup(function(){
  //alert();
  if($("#ccustomerPNR").val().length>=1){
    //alert();
    var data = $(this).val();

    $.ajax({
        url: '<?php echo site_url('/customer/customer_detail1/');?>'+ data,
        type: 'POST',			
        success: function(data) {
           // alert();
            var obj = JSON.parse(data);
            var total = obj.length;
            var toAppend = '';
            var items=[]; 
                $.each(obj,function(i,o){
                toAppend += '<option>'+o.booking_number+'</option>';
                //items.push($('<option>').text(o.LocationName));			
            });
            //$('#json-loclist').append.apply($('#json-loclist'), items);
         $('#json-cpnrlist').empty().append(toAppend);
		 
		 $("#ccustomerPNR").blur(function(){
			// $(this).val= data.Mobile;			 
			 for(var i=0;i<total;i++){
				 if(obj[i].booking_number == $("#ccustomerPNR").val()){
					// alert(obj[i].Mobile);
					$('#bookingid').val(obj[i].bid);					
       				$('#ccustomerMobile').val(obj[i].mobile_number);
		 			$('#ccustomerName').val(obj[i].customer_name);
					
					 break;
				 }			 	
			 }
		 });
		 
		 $("#ccustomerPNR").focus(function (){
				if($('#ccustomerPNR').val() != ''){
					$('#bcustomerId').val('');
					$('#ccustomerMobile').val('');
		 			$('#ccustomerName').val('');
					
				}		
			});	
        },
        error: function(){   
            //alert('Error while request Location List..');
			console.log('Error while request customer mobile no.');
        }        
    });
    }
  });	
  
  
   //get booking customer email
  var dataList = document.getElementById('json-cemail');
    
  /*$("#cemail").keyup(function(){
  // alert();
  if($("#cemail").val().length>=1){
    //alert();
    var data = $(this).val();

    $.ajax({
        url: '<?php echo site_url('/customer/customer_detail/');?>'+ data,
        type: 'POST',			
        success: function(data) {
            //alert();
            var obj = JSON.parse(data);
            var total = obj.length;
            var toAppend = '';
            var items=[]; 
                $.each(obj,function(i,o){
                toAppend += '<option>'+o.email_id+'</option>';
                //items.push($('<option>').text(o.LocationName));			
            });
            //$('#json-loclist').append.apply($('#json-loclist'), items);
         $('#json-cemail').empty().append(toAppend);
		 
		 $("#cemail").blur(function(){
			 //alert();
			 $(this).val= data.email_id;			 
			 for(var i=0;i<total;i++){
				 if(obj[i].email_id == $("#cemail").val()){
					// alert(obj[i].email_id);	 
       				$('#bcustomerName').val(obj[i].customer_name);
		 			$('#cmobileNo').val(obj[i].mobile_number);
					$('#cphoneNo').val(obj[i].contact_number);
		 			$('#cgender').val(obj[i].gender);
					$('#ccustomerType').val(obj[i].customer_type);
		 			$('#caadhar').val(obj[i].aadhar_card_number);
					$('#caddress').val(obj[i].address);
					 break;
				 }			 	
			 }
		 });
		 /*$("#cemail").focus(function (){
				if($('#cemail').val() != ''){
					
					$('#bcustomerName').val('');
		 			$('#cmobileNo').val('');
					$('#cphoneNo').val('');
		 			$('#cgender').val('');
					$('#ccustomerType').val('');
		 			$('#caadhar').val('');
					$('#caddress').val('');
					
				}					
		 });
        },
        error: function(){   
            //alert('Error while request Location List..');
        }        
    });
    }
  });	*/
  
  
  //check promocode is valid or not
$("#cpromocheck").click(function(){
  //alert();
    var data = $("#cpromoocode").val();
    $.ajax({
        url: '<?php echo site_url('/promocode/check_promocode/');?>'+ data,
        type: 'POST',			
        success: function(data) {
           // alert($("#cpromoocode").val());
		
			var obj = JSON.parse(data);
				
				var service_tax_percent = 18;
				var Subtotal = document.getElementById("subtotal");				
				var Amounttopay = document.getElementById("amounttopay");
				var ServiceTaxPrice = document.getElementById("ServiceTaxPrice");
					
				var toAppend = '';
				if( obj.promo_code == $("#cpromoocode").val()){
							if(obj.promo_percentage != ''){
								var toAppend = '';
								//alert('1');
								toAppend += '<br/><b class="red">Promocode Valid..!</b> <br/> Discount: <b>'+ obj.promo_percentage +'<b> &#37;';
								$('#discountdetails').empty().append(toAppend); 
								$('#promopercentage').val(obj.promo_percentage);
								
								var promo_amount = ($('#baseprice').val() /100) * obj.promo_percentage;
								//alert(promo_amount);
								promo_amount = promo_amount.toFixed(2);
								
								$('#PromoDiscount').empty();
								
								PromoDiscount.innerHTML+= promo_amount;
								$('#promoamount').val(promo_amount);
								var amount_to_pay = parseFloat($('#baseprice').val()) - parseFloat(promo_amount);
								
								amount_to_pay = amount_to_pay.toFixed(2);
								Subtotal.innerHTML = "";
								Subtotal.innerHTML += amount_to_pay;
								var service_tax_amount = (amount_to_pay / 100) * service_tax_percent;
								
								ServiceTaxPrice.innerHTML = "";				
								ServiceTaxPrice.innerHTML += service_tax_amount.toFixed(2);
								$('#servicetax_price').val(service_tax_amount.toFixed(2));
								amount_to_pay = parseFloat(amount_to_pay) +  parseFloat(service_tax_amount); 
								
								$('#approxamount').val(amount_to_pay);	
								
								$('#amounttopay').empty();
								amounttopay.innerHTML+= amount_to_pay;
								
							}else if(obj.promo_amount != ''){
								var toAppend = '';
								//alert('2');
								toAppend += '<br/><b class="red">Promocode Valid..!</b> <br/> Discount: <b>'+obj.promo_amount+'</b> &#x20B9;';
								$('#discountdetails').empty().append(toAppend);
								promo_amount = parseFloat(obj.promo_amount);
								$('#promoamount').val(obj.promo_amount);								
								$('#PromoDiscount').empty();
								
								PromoDiscount.innerHTML+= promo_amount;
								
								promo_amount = parseFloat(obj.promo_amount);
								
								amount_to_pay = parseFloat($('#baseprice').val()) - promo_amount;
								Subtotal.innerHTML = "";
								Subtotal.innerHTML += amount_to_pay;
								amount_to_pay = amount_to_pay.toFixed(2);
								
								var service_tax_amount = (amount_to_pay / 100) * service_tax_percent;
								ServiceTaxPrice.innerHTML ="";				
								ServiceTaxPrice.innerHTML += service_tax_amount.toFixed(2);
								$('#servicetax_price').val(service_tax_amount.toFixed(2));
								amount_to_pay = parseFloat(amount_to_pay) +  parseFloat(service_tax_amount); 
								
								$('#approxamount').val(amount_to_pay);
								
								$('#amounttopay').empty();
								amounttopay.innerHTML+= amount_to_pay;
							}
							$('#cpromocheck').addClass('disabled');
				}else if($("#cpromoocode").val() == ''){
					
					var toAppend = '';
					toAppend += '<br/><b class="red">Enter Promocode..!</b>';
					$('#discountdetails').empty().append(toAppend);
				
				}else if(obj == false){
					
					var toAppend = '';
					toAppend += '<br/><b class="red">Promocode InValid..!</b>';
					$('#discountdetails').empty().append(toAppend);
				
				}	
				 
        },
        error: function(){
           //alert('Error while request Location List..');
		   console.log('Error while request promecode.');
        }        
    });
    //}
  });
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  <!------------------------>
  
  $("#reasonType").change(function(){
  //alert();
  //if($("#pickupLocation").val().length>2){
   // alert();
    var data = $(this).val();
    $.ajax({
        url: '<?php echo site_url('/trips/reason/');?>'+ data,
        type: 'POST',			
        success: function(data) {
            //alert();
            var obj = JSON.parse(data);
            //alert(obj);
             var toAppend = '';
             var items=[]; 
                $.each(obj,function(i,o){
                toAppend += '<option>'+o.Reason+'</option>';
                //items.push($('<option>').text(o.LocationName));			
            });
            //$('#json-loclist').append.apply($('#json-loclist'), items);
         $('#creason').empty().append(toAppend);     
        },
        error: function(){
            //alert('Error while request reason List..');
			console.log('Error while request reason List..');
        }        
    });
    //}
  });
  
 
  //preferred model

 var dataList = document.getElementById('json-modellist');
    
  $("#preferredModel").keyup(function(){
  // alert();
  if($("#preferredModel").val().length>=1){
	  
    //alert();
    var data = $('#passengerCount').val();
	//alert(data);
    $.ajax({
        url: '<?php echo site_url('/booking/vehicle_models/');?>'+ data,
        type: 'POST',			
        success: function(data) {
           // alert('1');
            var obj = JSON.parse(data);            
             var toAppend = '';
             var items=[]; 
                $.each(obj,function(i,o){
					
                toAppend += '<option value="'+o.model_name+' ('+o.Count+')'+'" id="'+o.id+'"></option>';
                //items.push($('<option>').text(o.LocationName));			
            });
            //$('#json-loclist').append.apply($('#json-loclist'), items);
         $('#json-modellist').empty().append(toAppend);
		 
		 $("#preferredModel").blur(function(){
				 
			 var val = document.getElementById('preferredModel').value;
      		 var m_id = $('#json-modellist').find('option[value="' + val + '"]').attr('id');
			 //alert(m_id);
			 $('#preferredModelId').val(m_id);	
				
				//Get package list 
				$.ajax({
					url: '<?php echo site_url('/booking/package_list/');?>'+m_id+'/'+$('#plan').val(),
					type: 'POST',			
					success: function(data) {
						 
					 if(data != ''){
						//alert(data);
						var obj = JSON.parse(data);
						//alert(obj.length);
						$('#package').empty();
						$('#package').append('<option value="">Select</option>');				
						for(var i=0;i < obj.length;i++){
							
							$('#package').append('<option value="' + obj[i].id + '">' + obj[i].package_name+'</option>');
						}
						 
						 
					 }else{
						 
						$('#package').empty();
						$('#package').append('<option value="">Select</option>'); 
						 
					 }
					
					},
					error: function(){
						//alert('Error while request Model List..');
						console.log('Error while request package List..');
					}        
				});

			 
		 });
		      
        },
        error: function(){
            //alert('Error while request Model List..');
			console.log('Error while request Model List..');
        }        
    });
    }
  });
  

});














jQuery().ready(function() {
 
  // Binding next button on first step
  $(".open1").click(function() {
      if (v.form()) {
        $(".frm").hide("fast");
        $("#sf2").show("slow");
      }
   });
 
   // Binding next button on second step
   $(".open2").click(function() {
      if (v.form()) {
        $(".frm").hide("fast");
        $("#sf3").show("slow");
      }
    });
 
     // Binding back button on second step
    $(".back2").click(function() {
      $(".frm").hide("fast");
      $("#sf1").show("slow");
    });
 
     // Binding back button on third step
     $(".back3").click(function() {
      $(".frm").hide("fast");
      $("#sf1").show("slow");
    });
 
    $(".open3").click(function() {
      if (v.form()) {
        // optional
        // used delay form submission for a seccond and show a loader image
        $("#loader").show();
         setTimeout(function(){
           $("#basicform").html('<h2>Thanks for your time.</h2>');
         }, 1000);
        // Remove this if you are not using ajax method for submitting values
        return false;
      }
    });
	
	$('input[type=radio][name=tripcategory]').change(function() {
        if (this.value != 1) {            
			$('.plan-bike').attr('disabled', 'disabled');
			$('#plan_disable').addClass('hide');
        }
        else if (this.value == 1) {
            $('.plan-bike').removeAttr('disabled');
			$('#plan_disable').removeClass('hide');
        }
    });
	
});