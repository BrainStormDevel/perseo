<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{$title} - {$l_mod_title}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  {$css}

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="{$host}/modules/{$ModuleUrlName}/resources/css/infobox.css">
  <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    {$l_welcome} <b>{$title}</b>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">{$l_login}</p>

    <form>
      <div class="form-group has-feedback">
        <input id="username" type="username" class="form-control" placeholder="Username">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input id="password" type="password" class="form-control" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
	  <div id="msgbox">
       <p id="msg"></p>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input id="rememberme" type="checkbox"> {$l_remember}
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button id="login" type="submit" class="btn btn-primary btn-block btn-flat">{$l_sign_in}</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
{if $fsecret > 0 or $gsecret > 0}
    <div class="social-auth-links text-center">
      <p>- {$l_or} -</p>
	  {if $fsecret > 0}
      <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> {$l_sign_facebook}</a>
	  {/if}
	  {if $gsecret > 0}
      <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> {$l_sign_google}</a>
	  {/if}
    </div>
    <!-- /.social-auth-links -->
{/if}	

    <a href="#">{$l_forgot_pass}</a><br>
     <div class="form-group">
        <label>{$l_language}</label><br>
	<select id="lang" class="selectpicker" data-style="btn-info" data-width="auto">
      <option data-icon="flag flag-icon-it" value="IT" {if $lang eq "IT"}selected{/if}>Italiano</option>
      <option data-icon="flag flag-icon-en" value="EN" {if $lang eq "EN"}selected{/if}>English</option>
	</select>
    </div> 	
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
{$js}
<script>
  $(function () {
    $('#msgbox').hide();
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%'
    });
  });
  $("#login").click(function(e) {
  	$.ajax({
	url: '{$host}/{$ModuleUrlName}/checklogin/',
	type: 'post',
	data: { "{$CSRFname}": "{$CSRFToken}", "username": $('#username').val(), "password": $('#password').val(), "rememberme": $('#rememberme').is(':checked')?0:1 },
	success: function(response) {
		obj = JSON.parse(response);
		if (obj.code == 1) {
			$('#msgbox').attr('class', 'isa_error');
			var str = '<i class="fa fa-times-circle"></i>' + obj.msg;
		}
		else {
			$('#msgbox').attr('class', 'isa_success');
			var str = '<i class="fa fa-check"></i>' + obj.msg;	
		}
		$('#msg').html( str );
		$('#msgbox').show();
		setTimeout("window.location.replace('{$host}/{$ModuleUrlName}/');",1000);
	}
	});
	e.preventDefault();
  });
</script>
</body>
</html>