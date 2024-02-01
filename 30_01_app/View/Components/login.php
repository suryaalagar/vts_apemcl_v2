<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
  <!-- BEGIN: Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="sproutwings is real time gps tracking system in all over india .our products are personal tracker,asset tracker,vechicle tracker,personal tracker,fleet tracker etc..,">
    <meta name="keywords" content="">
    <meta name="author" content="TRACKING WINGS">
    <title><?php
     if($dealer_details_l->dealer_company!=""){
        echo $dealer_details_l->dealer_company;
     }
     else
     {
        echo "Twings School";
     }
    ?>
        
    </title>
    <link rel="apple-touch-icon" href="<?= base_url(); ?>app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url(); ?>app-assets/images/ico/favicon.png">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/forms/icheck/icheck.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/vendors/css/forms/icheck/custom.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/bootstrap-extended.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/colors.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/components.min.css">
    <!-- END: Theme CSS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/core/menu/menu-types/vertical-menu-modern.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/core/colors/palette-gradient.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>app-assets/css/pages/login-register.min.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/style.css">
    <!-- END: Custom CSS-->

  </head>
  <!-- END: Head-->
  <!-- bg-full-screen-image -->

  <!-- BEGIN: Body-->
  <body class="vertical-layout vertical-menu-modern 1-column blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
    <!-- BEGIN: Content-->
    <div class="app-content content">
      <div class="content-overlay"></div>
      <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body"><section class="row flexbox-container">
    <div class="col-12 d-flex align-items-center justify-content-center">
        <div class="col-lg-4 col-md-8 col-10 box-shadow-2 p-0">
            <div class="card border-grey border-lighten-3 px-1 py-1 m-0">
                <div class="card-header border-0">
                    <div class="card-title text-center">
                    <?php
                            $filename = FCPATH ."/uploads/company_logo/".$dealer_details_l->dealer_logo;
                            if($dealer_details_l->dealer_logo!=""  && (file_exists($filename))){ ?>

                                <div class="p-1"><img src="<?php echo base_url();?>uploads/company_logo/<?php echo $dealer_details_l->dealer_logo;?>" class="center" alt="<?php echo $dealer_details_l->dealer_company;?>" height="200px" width="200px">
                                <!-- <div class="p-1"><img src="<?php echo base_url();?>assets/dist/img/fav_icon.png" alt="Twings Logo" ></div> -->
                        
                        <?php } else { ?>
                            <div class="p-1">
                            <img src="<?= base_url(); ?>app-assets/images/logo/new_logo.png" class = "img-responsive" height="200px" alt="branding logo">
                            </div>
                            
                        <?php } ?>


                        <!-- <img src="<?= base_url(); ?>app-assets/images/logo/new_logo.png" class = "img-responsive" height="200px" alt="branding logo"> -->
                    </div>
                </div>
                <div class="card-content">
                    
<!--                    <p class="card-subtitle line-on-side text-muted text-center font-small-3 mx-2 my-1"><span>OR Using
                            Account Details</span></p>-->
                    <div class="card-body">
                        <form class="form-horizontal" id="loginForm" action="<?php echo site_url();?>/login/login" name = "myForm" method="post" onsubmit="return check();">
                            <span style="color: red"> <?php echo $this->session->flashdata('msg'); ?> </span>
                            <fieldset class="form-group position-relative has-icon-left">
                                <input type="text" name="username" class="form-control" id="username" placeholder="Your Username"
                                    required>
                                <div class="form-control-position">
                                    <i class="feather icon-user"></i>
                                </div>
                            </fieldset>
                            <fieldset class="form-group position-relative has-icon-left">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                                <div class="form-control-position">
                                    <i class="fa fa-key"></i>
                                </div>
                            </fieldset>
                            <div class="form-group row" style="display:none;">
                                <div class="col-sm-6 col-12 text-center text-sm-left pr-0">
                                    <fieldset>
                                        <input type="checkbox" id="remember-me" class="chk-remember">
                                        <label for="remember-me"> Remember Me</label>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-12 float-sm-left text-center text-sm-right"><a
                                        href="<?= base_url(); ?>login/forgotpassword" class="card-link">Forgot Password?</a></div>
                            </div>
                            <button type="submit" class="btn btn-outline-primary btn-block"><i
                                    class="feather icon-unlock"></i> Login</button>
                        </form>
                    </div>
                    <!--<p class="card-subtitle line-on-side text-muted text-center font-small-3 mx-2 my-1"><span>New to Stack ?</span></p>-->
<!--                            <div class="text-center">
                                <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-facebook"><span
                                        class="fa fa-facebook"></span></a>
                                <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-twitter"><span
                                        class="fa fa-twitter"></span></a>
                                <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-linkedin"><span
                                        class="fa fa-linkedin font-medium-4"></span></a>
                                <a href="#" class="btn btn-social-icon mr-1 mb-1 btn-outline-github"><span
                                        class="fa fa-github font-medium-4"></span></a>
                            </div>-->
                </div>
            </div>
        </div>
    </div>
</section>
        </div>
      </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="<?= base_url(); ?>app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="<?= base_url(); ?>app-assets/vendors/js/forms/validation/jqBootstrapValidation.js"></script>
    <script src="<?= base_url(); ?>app-assets/vendors/js/forms/icheck/icheck.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="<?= base_url(); ?>app-assets/js/core/app-menu.min.js"></script>
    <script src="<?= base_url(); ?>app-assets/js/core/app.min.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="<?= base_url(); ?>app-assets/js/scripts/forms/form-login-register.min.js"></script>
    <!-- END: Page JS-->
<script type="text/javascript">
    setTimeout(function() {
    $('#successMessage').fadeOut('fast');
}, 30000); 
	
        
	function check()
	{
		if( document.myForm.username.value == "" ) {
            alert( "Enter Username" );
            document.myForm.username.focus() ;
            return false;
         }

         if( document.myForm.password.value == "" ) {
            alert( "Enter Password" );
            document.myForm.password.focus() ;
            return false;
         }

	}
</script>
  </body>
  <!-- END: Body-->
</html>