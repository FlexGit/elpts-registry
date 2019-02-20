<nav class="navbar navbar-default navbar-fixed-top">
	<div class="brand" style="padding:15px 15px 15px 17px;">
		<a href="/" style="font-size:23px;font-weight:bold;">
			{{ config('app.name') }}
		</a>
		<div style="font-size:11px;">{{ config('app.vendor') }}</div>
	</div>
	<div class="container-fluid">
		<div class="navbar-btn">
			<button type="button" class="btn-toggle-fullwidth"><i class="lnr lnr-arrow-left-circle"></i></button>
		</div>
		<div id="navbar-menu">
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						{{--<i class="lnr lnr-user"></i>--}}<span>{{ session('elpts_registry_user_name') }}</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</nav>
