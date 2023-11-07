

        $(document).ready(function () {
            

       //  $('.adduser').hide(); 
          var device_type= $('#device_type').val();

          if (device_type=='0') 
          {
                   $(".fuel_types").hide();
                   $(".fueltype_status").hide();  
                   $(".fueltype_analog").hide(); 
                   $(".mdvr_terminal_no").hide();

                  $('.tank_diameter').hide();
                  $('.tank_length').hide();
                  $('.tank_width').hide();
                  $('.tank_height').hide();
                  $(".alter_tank_width").hide();
                  $(".alter_tank_height").hide();
                  $(".alter_tank_length").hide();
                  $(".temperature").hide();
				  $(".enginerpm_types").hide();
				  $(".rpmtypes").hide();
             }
          
           $('#device_type').click(function(){
             var device_type= $('#device_type').val();

           //  alert(device_type);
           if (device_type=='0' || device_type=='2' || device_type=='7' || device_type=='17' || device_type=='18') 
           {
              
                  $(".fuel_types").hide();  
                  $(".fueltype_analog").hide(); 
                  $(".mdvr_terminal_no").hide();
                  $(".fueltype_status").hide();
                  $(".temperature").hide();
				   $(".enginerpm_types").hide();
                  $(".rpmtypes").hide();
             }

              if (device_type=='1' || device_type=='3' ||device_type=='9'  ||  device_type=='10' || device_type=='11') 
           // alert(device_type);
             {
                 $(".fuel_types").show();  
                 $(".fueltype_analog").hide(); 
                 $(".mdvr_terminal_no").hide();
                 $(".fueltype_status").hide();
                 $(".temperature").hide();
				  $(".enginerpm_types").hide();
                  $(".rpmtypes").hide();
                  
             }


             if (device_type=='14' || device_type=='15') 
             // alert(device_type);
               {
               
                $('.tank_diameter').hide();
                $('.tank_length').hide();
                 $('.tank_width').hide();
                 $('.tank_height').hide();
                 $(".alter_tank_width").hide();
                $(".alter_tank_height").hide();
                $(".alter_tank_length").hide();
                $(".fuel_types").show();  
                $(".fueltype_analog").show(); 
                 $(".fueltype_status").show();
                 $(".temperature").hide();
               }
        
               
             if (device_type=='4') 

             {
                $(".fuel_types").hide();  
                $(".fueltype_analog").hide(); 
                 $(".mdvr_terminal_no").show();
                 $(".temperature").hide();

             }
             if (device_type=='5' || device_type=='6' || device_type=='12') 

             {
                 $(".fuel_types").show();  
                 $(".fueltype_analog").hide(); 
                 $(".mdvr_terminal_no").show();
                 $(".temperature").hide();

             }


             if (device_type=='8' || device_type=='16') 

             {
              $(".temperature").show();
              $(".fuel_types").hide();
                   $(".fueltype_status").hide();  
                   $(".fueltype_analog").hide(); 
                   $(".mdvr_terminal_no").hide();


                  $('.tank_diameter').hide();
                  $('.tank_length').hide();
                  $('.tank_width').hide();
                  $('.tank_height').hide();
                  $(".alter_tank_width").hide();
                  $(".alter_tank_height").hide();
                  $(".alter_tank_length").hide();1
             }
			 if (device_type=='17') 
             {
              $(".enginerpm_types").show();
              $(".temperature").hide();
              $(".fuel_types").hide();
                   $(".fueltype_status").hide();  
                   $(".fueltype_analog").hide(); 
                   $(".mdvr_terminal_no").hide();


                  $('.tank_diameter').hide();
                  $('.tank_length').hide();
                  $('.tank_width').hide();
                  $('.tank_height').hide();
                  $(".alter_tank_width").hide();
                  $(".alter_tank_height").hide();
                  $(".alter_tank_length").hide();1
             }
             if (device_type=='19') 
             {
				$('.tank_diameter').hide();
                $('.tank_length').hide();
                 $('.tank_width').hide();
                 $('.tank_height').hide();
                 $(".alter_tank_width").hide();
                $(".alter_tank_height").hide();
                $(".alter_tank_length").hide();
                $(".fuel_types").show();  
                $(".fueltype_analog").show(); 
                 $(".fueltype_status").show();
                 $(".temperature").hide();
				 
             $(".enginerpm_types").show();
			 $(".rpmtypes").show(); 
              
             }
             
           });

    });

              function fuel_type1(){

      var fuel_tank_type = $('#fuel_tank_type').val();
      var fuel_model = $('#fuel_model').val();
    // alert(fuel_tank_type);
      
    if(fuel_model=='1')
    {
      if(fuel_tank_type ==''){
          $('.tank_diameter').hide();
          $('.tank_length').hide();
          $('.tank_width').hide();
          $('.tank_height').hide();
          $(".alter_tank_width").hide();
         $(".alter_tank_height").hide();
         $(".alter_tank_length").hide();
      }else if(fuel_tank_type =='1'){
       // alert('hi');
          $('.tank_diameter').hide();
          $('.tank_length').show();
          $('.tank_width').show();
          $('.tank_height').show();
            $(".alter_tank_width").hide();
         $(".alter_tank_height").hide();
         $(".alter_tank_length").hide();

          //  $("#tank_diameter").val('');
          //  $("#alter_tank_width").val('');
          //  $("#alter_tank_height").val(''); 



      }else if(fuel_tank_type =='2'){// alert();
          $('.tank_diameter').show();
          $('.tank_length').show();
          $('.tank_width').show();
          $('.tank_height').show();
          $(".alter_tank_width").hide();
         $(".alter_tank_height").hide();
         $(".alter_tank_length").hide();

          //  $("#alter_tank_width").val('');
          //  $("#alter_tank_height").val(''); 


      }else if(fuel_tank_type =='3'){
          $('.tank_diameter').hide();
          $('.tank_length').hide();
          $('.tank_width').hide();
          $('.tank_height').hide();
          $(".alter_tank_width").hide();
         $(".alter_tank_height").hide();
         $(".alter_tank_length").hide();


          //  $("#tank_diameter").val('');
          //  $("#tank_length").val('');
          //  $("#tank_width").val('');
          //  $("#tank_height").val('');
          //  $("#alter_tank_width").val('');
          //  $("#alter_tank_height").val(''); 
          //  $("#alter_tank_width").val('');
          //  $("#alter_tank_height").val(''); 



      }
       else if(fuel_tank_type =='4'){
          $('.tank_diameter').hide();
          $('.tank_length').show();
          $('.tank_width').show();
          $('.tank_height').show();
           $(".alter_tank_width").show();
          $(".alter_tank_height").show();
           $(".alter_tank_length").hide();

            // $("#tank_diameter").val('');
            //  $("#alter_tank_height").val(''); 


      }

         $(".fueltype_analog").hide(); 
          $(".fueltype_status").hide();

          //  $("#fuel_a").val('');
          //  $("#fuel_b").val('');
          //  $("#fuel_c").val(''); 


    }
    else if(fuel_model=='2')
    {

          //  $("#tank_diameter").val('');
          //  $("#tank_length").val('');
          //  $("#tank_width").val('');
          //   $("#tank_height").val('');
          //  $("#alter_tank_width").val('');
          //  $("#alter_tank_height").val(''); 
          //  $("#alter_tank_width").val('');
          //  $("#alter_tank_height").val(''); 



         $('.tank_diameter').hide();
         $('.tank_length').hide();
          $('.tank_width').hide();
          $('.tank_height').hide();
          $(".alter_tank_width").hide();
         $(".alter_tank_height").hide();
         $(".alter_tank_length").hide();

          $(".fueltype_analog").hide(); 
          $(".fueltype_status").hide();

          

          //   $("#fuel_a").val('');
          //  $("#fuel_b").val('');
          //  $("#fuel_c").val(''); 
    }

      else if(fuel_model=='3')
    {
       // alert('hi');
      $('.tank_diameter').hide();
       $('.tank_length').hide();
          $('.tank_width').hide();
          $('.tank_height').hide();
          $(".alter_tank_width").hide();
         $(".alter_tank_height").hide();
         $(".alter_tank_length").hide();
         $(".fueltype_analog").show(); 
          $(".fueltype_status").show();

        //  $("#tank_diameter").val('');
        //    $("#tank_length").val('');
        //    $("#tank_width").val('');
        //    $("#tank_height").val('');
        //    $("#alter_tank_width").val('');
        //    $("#alter_tank_height").val(''); 
        //    $("#alter_tank_width").val('');
        //    $("#alter_tank_height").val(''); 



    }

    else if(fuel_model=='4')
    {
       // alert('hi');
        $('.tank_diameter').hide();
        $('.tank_length').hide();
          $('.tank_width').hide();
          $('.tank_height').hide();
          $(".alter_tank_width").hide();
         $(".alter_tank_height").hide();
         $(".alter_tank_length").hide();
         $(".fueltype_analog").show(); 
          $(".fueltype_status").show();

          // $("#tank_diameter").val('');
          //  $("#tank_length").val('');
          //  $("#tank_width").val('');
          //  $("#alter_tank_width").val('');
          //  $("#alter_tank_height").val(''); 
          //  $("#alter_tank_width").val('');
          //  $("#alter_tank_height").val(''); 

    }
    }