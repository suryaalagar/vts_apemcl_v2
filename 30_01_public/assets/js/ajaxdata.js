$('#loginform').submit(function(e){
	
 e.preventDefault();
 $("#excistuser").empty();

 alert("Hi");

    var uname = $('#u_name').val();
	var password = $('#password').val();	
	var siteurl = $('#siteurl').val();
	alert(siteurl);

	$.ajax({
		type: "POST",
		datatype: "json",
		// url: siteurl+'Login/excistuser',
		url: <?php echo base_url(); ?>,
		data: {	
			'uname': uname,
			'password':password
		},
		success: function(data) {
			
			var obj = JSON.parse(data);
			//alert(data);
			console.log(obj['md5password']);
			if(uname == obj['username'] && password == obj['password']) {						
						$.ajax({
							type: "POST",
							datatype: "json",
							url: siteurl+'Login/checkuser',
							data: {
								'uname': obj['username'],
								'password':obj['md5password']
							},
							success: function(data) { 
							alert(data);
							var logurl = siteurl+'Dashboard'; 	
							alert('Successfully Login');						
						  	$(location).attr('href', logurl) 
							},
							error: function() {
								console.log('Error While Request Location List..');							
							}
					}); 
			}
			else
			{
				$("#excistuser").empty().html('<b><span id="excistuser" style="color:red;">Username And Password Not Exist...!</span></b>');
			}
		
		},
		error: function() {
			console.log('Error While Request Location List..');		
	}

});

});
