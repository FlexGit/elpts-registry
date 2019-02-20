<div id="sidebar-nav" class="sidebar">
	<div class="sidebar-scroll">
		<nav>
			<ul class="nav">
				<li><a href="/" class="@if (Request::segment(1) == '') active @endif"><i class="lnr lnr-home"></i> <span>Главная</span></a></li>
				<li>
					<a href="#subRegistry" data-toggle="collapse" class="@if (in_array(Request::segment(1), array('docs')) && in_array(Request::segment(1), array('1','2'))) active @else collapsed @endif"><i class="lnr lnr-database"></i> <span>Реестры</span> <i class="icon-submenu lnr lnr-chevron-left"></i></a>
					<div id="subRegistry" class="@if (in_array(Request::segment(1), array('docs')) && in_array(Request::segment(2), array('1','2','3'))) collapse in @else collapse @endif">
						<ul class="nav">
							<li><a href="/docs/1" class="@if (Request::segment(1) == 'docs' && Request::segment(2) == '1') active @endif"><i class="lnr lnr-license"></i> <span>Оферты</span></a></li>
							<li><a href="/docs/2" class="@if (Request::segment(1) == 'docs' && Request::segment(2) == '2') active @endif"><i class="lnr lnr-license"></i> <span>Заявления</span></a></li>
						</ul>
					</div>
				</li>
				<li><a href="/log" class="@if (Request::segment(1) == 'log') active @endif"><i class="lnr lnr-layers"></i> <span>Лог</span></a></li>
			</ul>
		</nav>
	</div>
</div>
