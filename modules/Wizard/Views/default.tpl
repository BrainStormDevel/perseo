<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ vars.l_install_title }} {{ vars.ProdName }} {{ vars.ProdVer }}</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link rel="stylesheet" href="{{ host }}/modules/{{ ModuleName }}/resources/css/style.css">
  <link rel="stylesheet" href="{{ host }}/modules/{{ ModuleName }}/resources/css/loader.css">
  <link rel="stylesheet" href="{{ host }}/modules/{{ ModuleName }}/resources/css/flags.css">
</head>
<body>
<input type="hidden" name="{{ csrf.nameKey }}" value="{{ csrf.name }}">
<input type="hidden" name="{{ csrf.valueKey }}" value="{{ csrf.value }}">
    <div class='container'>

			<section id="wizard">
			  <div class="page-header">
	            <h1>{{ vars.l_welcome }} {{ vars.ProdName }} {{ vars.ProdVer }}</h1>
	          </div>

				<div id="tabinstall" class="tabbable tabs-left">
					<ul>
					  	<li><a href="#tabinstall-tab1" data-toggle="tab">{{ vars.l_start }}</a></li>
						<li><a href="#tabinstall-tab2" data-toggle="tab">{{ vars.l_second }}</a></li>
						<li><a href="#tabinstall-tab3" data-toggle="tab">{{ vars.l_third }}</a></li>
						<li><a href="#tabinstall-tab4" data-toggle="tab">{{ vars.l_forth }}</a></li>
						<li><a href="#tabinstall-tab5" data-toggle="tab">{{ vars.l_fifth }}</a></li>
						<li><a href="#tabinstall-tab6" data-toggle="tab">{{ vars.l_sixth }}</a></li>
					</ul>
                    <div id="bar" class="progress progress-info progress-striped">
                      <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                    </div>
					<div class="tab-content">
					    <div class="tab-pane" id="tabinstall-tab1">
					      <p>{{ vars.l_wizard_1 }}</p>
						  <p>
							<div class="input-group">
								<span class="input-group-addon">{{ vars.l_language }}</span>
								<select id="lang" class="selectpicker" data-style="btn-info" data-width="auto">
									<option data-icon="flag flag-it" value="it" {% if lang == 'it' %}selected{% endif %}>Italiano</option>
									<option data-icon="flag flag-en" value="en" {% if lang == 'en' %}selected{% endif %}>English</option>
								</select>
							</div>						
						  </p>
						  <p>{{ vars.l_check_perm }} {{ vars.folder }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{% if write == 'ok' %}<i class="fa fa-check-circle-o fa-3x text-success"></i>&nbsp;&nbsp;{{ vars.l_check_perm_ok }}{% else %}<i class="fa fa-times-circle-o fa-3x text-danger"></i>&nbsp;&nbsp;{{ vars.l_check_perm_no}}{% endif %}
						  </div>
					    <div class="tab-pane" id="tabinstall-tab2">
					      <p>{{ vars.l_wizard_2_1 }}</p>
						  <p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_dbtype }}</span>
						  <select id="dbtype" class="selectpicker form-control" title="{$l_dbtype_ph}">
							<option value="mysql" selected>mysql</option>
							<option value="mssql">mssql</option>
							<option value="sqlite">sqlite</option>
						  </select>
						  </div>
						  </p>
						  <p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_dbhost }}</span><input type='text' name='dbhost' id='dbhost' class='form-control' placeholder='{{ vars.l_dbhost_ph }}'>
						  </div>
						  </p>
						  <p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_dbname }}</span><input type='text' name='dbname' id='dbname' class='form-control' placeholder='{{ vars.l_dbname_ph }}'>
						  </div>
						  </p>
						  <p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_dbuser }}</span><input type='text' name='dbuser' id='dbuser' class='form-control' placeholder='{{ vars.l_dbuser_ph }}'>
						  </div>
						  </p>
						  <p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_dbpass }}</span><input type='text' name='dbpass' id='dbpass' class='form-control' placeholder='{{ vars.l_dbpass_ph }}'>
						  </div>
						  </p>
						  <p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_dbencoding }}</span>
						  <select id="dbenc" class="selectpicker form-control" title="{{ vars.l_dbencoding_ph }}">
							<option value="ucs2">ucs2</option>
							<option value="big5">big5</option>
							<option value="latin1">latin1</option>
							<option value="latin2">latin2</option>
							<option value="latin3">latin3</option>
							<option value="latin4">latin4</option>
							<option value="latin5">latin5</option>
							<option value="latin6">latin6</option>
							<option value="latin7">latin7</option>
							<option value="binary">binary</option>
							<option value="utf8" selected>utf8</option>
							<option value="utf8mb4">utf8mb4</option>
						  </select>
						  </div>
						  </p>
					    </div>
						<div class="tab-pane" id="tabinstall-tab3">
						 <p>{{ vars.l_wizard_3_1 }}</p>
						 <p><div id="loader" class="loader"></div></p>
						 <p><div id="result"></div></p>
					    </div>
						<div class="tab-pane" id="tabinstall-tab4">
						<p>{{ vars.l_wizard_4_1 }}</p>						
						<p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_title }}</span><input type="text" name="title" id="title" class="form-control" placeholder="{{ vars.l_title_ph }}">
						  </div>
						</p>
						<p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_prefix_tb }}</span><input type="text" name="prefix" id="prefix" class="form-control" placeholder="{{ vars.l_prefix_tb_ph }}">
						  </div>
						</p>
						<p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_prefix_salt }}</span><input type="text" name="salt" id="salt" class="form-control" placeholder="{{ vars.l_prefix_salt_ph }}"><span class="input-group-btn"><button type="button" id="saltbtn" class="btn btn-default">{$l_generate}</button></span></div>
						</p>
						<p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_encoding_page }}</span><input type="text" name="encoding" id="encoding" class="form-control" placeholder="{{ vars.l_encoding_page_ph }}" value="utf-8"></div>
						</p>
						<p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_adm_cookname }}</span><input type="text" name="adm_cookname" id="adm_cookname" class="form-control" placeholder="{{ vars.l_adm_cookname_ph }}" value="ADM"></div>
						</p>
						<p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_usr_cookname }}</span><input type="text" name="usr_cookname" id="usr_cookname" class="form-control" placeholder="{{ vars.l_usr_cookname_ph }}" value="USR"></div>
						</p>
						<p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_cookie_expire }}</span><input type="text" name="cookie_expire" id="cookie_expire" class="form-control" placeholder="{{ vars.l_cookie_expire_ph }}" value="3600"></div>
						</p>
						<p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_cookie_max_expire }}</span><input type="text" name="cookie_max_expire" id="cookie_max_expire" class="form-control" placeholder="{{ vars.l_cookie_max_expire_ph }}" value="7889238"></div>
						</p>
						<p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_cookie_path }}</span><input type="text" name="cookie_path" id="cookie_path" class="form-control" placeholder="{{ vars.l_cookie_path_ph }}" value="{{ vars.cookiepath }}"></div>
						</p>						
					    </div>
						<div class="tab-pane" id="tabinstall-tab5">
						<p>{{ vars.l_wizard_5_1 }}</p>
						<p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_username }}</span><input type="text" name="username" id="username" class="form-control" placeholder="{{ vars.l_username_ph }}">
						  </div>
						</p>
						<p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_email }}</span><input type="text" name="email" id="email" class="form-control" placeholder="{{ vars.l_email_ph }}">
						  </div>
						</p>						
						<p>
						  <div class="input-group"><span class="input-group-addon">{{ vars.l_password }}</span><input type="text" name="pass" id="pass" class="form-control" placeholder="{{ vars.l_password_ph }}"><span class="input-group-btn"><button type="button" id="randpass" class="btn btn-default">{$l_generate}</button></span><span class="input-group-addon"><meter value="0" id="PassValue" low="35" max="100"></meter></span></div>
						</p>						
					    </div>
						<div class="tab-pane" id="tabinstall-tab6">
						<p>{{ vars.l_wizard_6_1 }}</p>
						<p><div id="endresult"></div></p>
					    </div>						
						<ul class="pager wizard">
							<li id="prev" class="previous"><a href="javascript:;">{{ vars.l_previous }}</a></li>
						  	<li id="next" class="next"><a href="javascript:;">{{ vars.l_next }}</a></li>
							<li id="install" class="next finish" style="display:none;"><a href="javascript:;">{{ vars.l_install }}</a></li>
						</ul>
					</div>
				</div>

			</section>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap-wizard/1.2/jquery.bootstrap.wizard.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.complexify.js/0.5.1/jquery.complexify.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/2.2.0/js.cookie.min.js"></script>
	<script type="text/javascript" src="{{ host }}/modules/{{ ModuleName }}/resources/js/functions.js" charset="UTF-8"></script>
	<script type="text/javascript" src="{{ host }}/modules/{{ ModuleName }}/resources/js/jquery.jquery-password-generator-plugin.min.js" charset="UTF-8"></script>
	<script>
	$(document).ready(function() {			
            $("#pass").complexify({}, function (valid, complexity) {
                document.getElementById("PassValue").value = complexity;
            });	
			$("#lang").on('change', function(e) {
				Cookies.set('lang', $(this).val(), { expires: 30 });
				window.location.reload(false);
			});
			salt = makerand("abcdef0123456789", 64);
			tbl = makerand("abcdefghijklmnopqrstuvwxyz", 5) + "_";
			$('#salt').val(salt);
			$('#prefix').val(tbl);
			$("#saltbtn").click(function() {
				salt = makerand("abcdef0123456789", 64);
				$('#salt').val(salt);
			});
			$("#randpass").click(function() {
				password = $.passGen({ 'length' : 16, 'special' : true });
				$('#pass').val(password);
				$('#pass').focus();
			});			
			$('#tabinstall').bootstrapWizard({ 'tabClass': 'nav nav-tabs', 'debug': false, onShow: function(tab, navigation, index) {
					}, onNext: function(tab, navigation, index) {
					if(index==2) {
					if(!$('#dbhost').val()) {
						alert('{{ vars.l_dbhost_ph }}');
						$('#dbhost').focus();
						return false;
					}
					if(!$('#dbname').val()) {
						alert('{{ vars.l_dbname_ph }}');
						$('#dbname').focus();
						return false;
					}
					if(!$('#dbuser').val()) {
						alert('{{ vars.l_dbuser_ph }}');
						$('#dbuser').focus();
						return false;
					}
					if(!$('#dbpass').val()) {
						alert('{{ vars.l_dbpass_ph }}');
						$('#dbpass').focus();
						return false;
					}
					dbtype = $('#dbtype option:selected').text();
					dbhost = $('#dbhost').val();
					dbname = $('#dbname').val();
					dbuser = $('#dbuser').val();
					dbpass = $('#dbpass').val();
					dbenc = $('#dbenc option:selected').text();
					}
					else if(index==3) {
						if (!test) return false;
					}
					else if(index==4) {
					if(!$('#title').val()) {
						alert('{$l_title_ph}');
						$('#title').focus();
						return false;
					}
					}
					else if(index==5) {
					if(!$('#username').val()) {
						alert('{$l_username_ph}');
						$('#username').focus();
						return false;
					}
					if(!$('#pass').val()) {
						alert('{$l_password_ph}');
						$('#pass').focus();
						return false;
					}
					if(!$('#email').val()) {
						alert('{$l_email_ph}');
						$('#email').focus();
						return false;
					}					
					}					
					}, onTabClick: function(tab, navigation, index) {
						return false;
					}, onTabShow: function(tab, navigation, index) {
						if (index==2) {
						    $('#result').empty();
							$('#loader').show();
							setTimeout(function(){
							$.ajax({
								url: '{{ host }}/wizard/test/',
								type: 'post',
								data: { "{{ csrf.nameKey }}": $( "input[name='{{ csrf.nameKey }}']" ).val(), "{{ csrf.valueKey }}": $( "input[name='{{ csrf.valueKey }}']" ).val(), "dbhost": dbhost, "dbname": dbname, "dbuser": dbuser, "dbpass": dbpass, "dbencoding": dbenc},
								success: function(response) {
									alert(response);

								}
							})
							},1000);
						}
						var $total = navigation.find('li').length;
						var $current = index+1;
						var $percent = ($current/$total) * 100;
                        $('#bar .progress-bar').css({ width:$percent+'%' });

						// If it's the last tab then hide the last button and show the finish instead
						if($current >= $total) {
							$('#tabinstall').find('.pager .next').hide();
							$('#tabinstall').find('.pager .finish').show();
							$('#tabinstall').find('.pager .finish').removeClass('disabled');
						} else {
							$('#tabinstall').find('.pager .next').show();
							$('#tabinstall').find('.pager .finish').hide();
						}

					}});
				$("#install").click(function() {
					language = $('#lang option:selected').val();
					$.ajax({
						url: '{{ host }}/{{ ModuleName }}/Install/',
						type: 'post',
						data: { "{$CSRFname}": $('#CSRFToken').val(), "lang": language, "dbhost": dbhost, "dbname": dbname, "dbuser": dbuser, "dbpass": dbpass, "dbencoding": dbenc, "title": $("#title").val(), "prefix": $("#prefix").val(), "salt": $("#salt").val(), "encoding": $("#encoding").val(), "cookadm": $("#adm_cookname").val(), "cookusr": $("#usr_cookname").val(), "cookexp": $("#cookie_expire").val(), "cookmaxexp": $("#cookie_max_expire").val(), "cookpath": $("#cookie_path").val(), "admin": $("#username").val(), "email": $("#email").val(), "password": $("#pass").val()},
						success: function(response) {
							var result = JSON.parse(response);
							$('#CSRFname').val(result.CSRFname);
							$('#CSRFToken').val(result.CSRFToken);
							if (result.code == 0) {
							$('#endresult').append('<center><i class="fa fa-check-circle-o fa-5x text-success"></i><br>{{ vars.l_install_ok }}</center>');
							setTimeout(function(){ location.href="{$main_host}/" } , 3000);
							}
							else {
							$('#result').append('<center><i class="fa fa-times-circle-o fa-5x text-danger"></i><br>{{ vars.l_install_ko }}&nbsp;&nbsp;' + result.msg + '</center>');
							}
						}
					});
				});		
		});	
	</script>
</body>
</html>