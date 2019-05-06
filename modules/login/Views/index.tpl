{% spaceless %}
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ titlesite }} - {{ lang.l_mod_title }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/ionicons/4.0.0-19/css/ionicons.min.css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.10/css/AdminLTE.min.css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/square/blue.css" />
  <link rel="stylesheet" href="{{ host }}/modules/{{ vars.ModuleName }}/resources/css/flags.css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="{{ host }}/modules/{{ vars.ModuleName }}/resources/css/infobox.css">
  <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    {{ lang.l_welcome }} <b>{{ titlesite }}</b>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">{{ lang.l_login }}</p>

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
              <input id="rememberme" type="checkbox">&nbsp;{{ lang.l_remember }}
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button id="login" type="submit" class="btn btn-primary btn-block btn-flat">{{ lang.l_sign_in }}</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
{% if faceapp or googlekey %}	
    <div class="social-auth-links text-center">
      <p>- {{ lang.l_or }} -</p>
	  {% if faceapp %}
      <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i>{{ lang.l_sign_facebook }}</a>
	  {% endif %}
	  {% if googlekey %}
      <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i>{{ lang.l_sign_google }}</a>
	  {% endif %}
    </div>
    <!-- /.social-auth-links -->
{% endif %}	

    <a href="#">{{ lang.l_forgot_pass }}</a><br>
     <div class="form-group">
        <label>{{ lang.l_language }}</label><br>
		<select id="lang" class="selectpicker" data-style="btn-info" data-width="auto">
			<option data-icon="flag flag-it" value="it" {% if vars.language == 'it' %}selected{% endif %}>Italiano</option>
			<option data-icon="flag flag-en" value="en" {% if vars.language == 'en' %}selected{% endif %}>English</option>
		</select>
    </div> 	
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/js-cookie/2.2.0/js.cookie.min.js"></script>
<script src="{{ host }}/modules/{{ vars.ModuleName }}/resources/js/securetoken.min.js"></script>{% endspaceless %}
<script>
  $(function () {
	$("#lang").on('change', function(e) {
		Cookies.set('lang', $(this).val(), { expires: 30 });
		window.location.reload(false);
	});  
    $('#msgbox').hide();
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%'
    });
  });
  $("#login").click(function(e) {
  	$.ajax({
	url: '{{ host }}/{{ vars.ModuleName }}/{{ name }}/',
	type: 'post',
	data: { "{{ csrf.nameKey }}": "{{ csrf.name }}", "{{ csrf.valueKey }}": "{{ csrf.value }}", "username": $('#username').val(), "password": $('#password').val(), "type": "{{ name }}s", "rememberme": $('#rememberme').is(':checked')?0:1 },
	success: function(response) {
		obj = JSON.parse(response);
		if (obj.msg == "USR_PASS_ERR") { msg = "{{ lang.l_login_err }}"; }
		else { msg = obj.msg; }
		if (obj.code == 1) {
			$('#msgbox').attr('class', 'isa_error');
			var str = '<i class="fa fa-times-circle"></i>' + msg;
			setTimeout("window.location.replace('{{ host }}/{{ vars.ModuleName }}/{{ name }}');",1000);
		}
		else {
			$('#msgbox').attr('class', 'isa_success');
			var str = '<i class="fa fa-check"></i>' + msg;
			setTimeout("window.location.replace('{{ host }}/{{ name }}/');",1000);
		}
		$('#msg').html( str );
		$('#msgbox').show();
	}
	});
	e.preventDefault();
  });
</script></body></html>