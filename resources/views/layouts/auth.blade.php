<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
	<title>
        {{ config('app.name') }}
	</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<link href="{{ config('constants.assets_path') }}/assets/vendor/bootstrap/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body style="background:url('{{ config('constants.assets_path') }}/images/preloader.gif') no-repeat;background-attachment:fixed;background-position: center center;">
	<div class="modal fade" id="auth_modal" role="dialog">
	   	<div class="modal-dialog modal-lg">
	   		<form name="auth_modal" action="#">
	     		<!-- Modal content-->
	     		<div class="modal-content">
					<div style="padding:10px 20px;">
		       			<button type="button" class="close" data-dismiss="modal">&times;</button>
		       		</div>
		       		<div class="modal-body" style="padding:10px 30px 25px;">
		       			<div class="form-group">
		       				<h4>Сертификаты</h4>
		       				<select id="certificates" name="certificates" class="form-control"></select>
		       			</div>
		       			<button type="button" id="auth_modal_btn" data-dismiss="modal" class="btn btn-primary">Войти</button>
			    		<button type="button" id="auth_cancel_modal_btn" data-dismiss="modal" class="btn btn-default">Отмена</button>
		       		</div>
	     		</div>
	     	</form>
		</div>
	</div>

	<!-- JS -->
	<script type="text/javascript" src="{{ config('constants.assets_path') }}/assets/vendor/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="{{ config('constants.assets_path') }}/assets/vendor/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="{{ config('constants.assets_path') }}/js/cspsignplugin.js"></script>
	<script type="text/javascript" src="{{ config('constants.assets_path') }}/js/auth.js"></script>
	<!-- END JS -->
</body>
</html>
