@extends('layouts.master')

@section('breadcrumbs', Breadcrumbs::render('docs.index', $doctype))

@section('content')
	<div class="row">
		<div class="col-md-12">
			@if (Session::has('success') && !empty(Session::get('success')))
				<div class="alert alert-success">
					{{ Session::get('success') }}
				</div>
			@endif
			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title">Фильтры</h3>
				</div>
				<div class="panel-body no-padding">
					<form method="POST" action="/docs/{{ $doctypes_id }}">
						{{ csrf_field() }}
						<table class="table">
							<thead>
							<tr>
								<th>Префикс</th>
								<th>Статус</th>
								<th>ОГРН/ОГРНИП</th>
								<th>ИНН</th>
								<th>Краткое наименование организации</th>
								<th>Дата начала</th>
								<th>Дата окончания</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td>
									<select name="filter_prefix" class="form-control">
										<option value="">---</option>
										@if (count($prefixes) > 0)
											@foreach ($prefixes->all() as $prefix)
												<option value="{{ $prefix->id }}"
														@if ($request->filter_prefix == $prefix->id) selected @endif>{{ $prefix->name }}</option>
											@endforeach
										@endif
									</select>
								</td>
								<td>
									<select name="filter_status" class="form-control">
										<option value="0" @if (!$request->filter_status) selected @endif>---</option>
										@if (count($statuses) > 0)
											@foreach ($statuses->all() as $status)
												<option value="{{ $status->id }}"
														@if ($request->filter_status == $status->id) selected
														@elseif (!isset($request->filter_status) && $status->id == 1) selected @endif>{{ $status->name }}</option>
											@endforeach
										@endif
									</select>
								</td>
								<td>
									<input type="text" name="filter_ogrn"
										   value="@if ($request->filter_ogrn){{ $request->filter_ogrn }}@endif"
										   class="form-control">
								</td>
								<td>
									<input type="text" name="filter_inn"
										   value="@if ($request->filter_inn){{ $request->filter_inn }}@endif"
										   class="form-control">
								</td>
								<td>
									<input type="text" name="filter_orgname"
										   value="@if ($request->filter_orgname){{ $request->filter_orgname }}@endif"
										   class="form-control">
								</td>
								<td>
									<div class="input-group date" id="filter_date_from" style="min-width:100px;">
										<input type="text" name="filter_date_from"
											   value="@if (!empty($request->filter_date_from)){{ $request->filter_date_from }}@endif"
											   class="form-control">
										<span class="input-group-addon">
                        						<span class="glyphicon glyphicon-calendar"></span>
                    						</span>
									</div>
								</td>
								<td style="padding-left:8px;">
									<div class="input-group date" id="filter_date_to" style="min-width:100px;">
										<input type="text" name="filter_date_to"
											   value="@if (!empty($request->filter_date_to)){{ $request->filter_date_to }}@endif"
											   class="form-control">
										<span class="input-group-addon">
                        						<span class="glyphicon glyphicon-calendar"></span>
                    						</span>
									</div>
								</td>
							</tr>
							</tbody>
							<tfoot>
							<tr>
								<td colspan="7" style="text-align:center;">
									<br>
									<button class="btn btn-primary">Поиск</button>
									<button type="button" class="btn btn-primary"
											onClick="window.location.href='/docs/{{ $doctypes_id }}';">Сбросить
									</button>
								</td>
							</tr>
							</tfoot>
						</table>
					</form>
				</div>
			</div>

			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title">Реестр документов "{{ $doctype->name }}"</h3>
					<div class="right">
						<div class="col-md-6 text-right">
							<form method="POST" action="/docs/{{ $doctypes_id }}">
								{{ csrf_field() }}
								<input type="hidden" name="method" value="export">
								<input type="hidden" name="filter_prefix" value="{{ $request->filter_prefix }}">
								<input type="hidden" name="filter_status" value="{{ $request->filter_status }}">
								<input type="hidden" name="filter_ogrn" value="{{ $request->filter_ogrn }}">
								<input type="hidden" name="filter_inn" value="{{ $request->filter_inn }}">
								<input type="hidden" name="filter_orgname" value="{{ $request->filter_orgname }}">
								<input type="hidden" name="filter_date_from" value="{{ $request->filter_date_from }}">
								<input type="hidden" name="filter_date_to" value="{{ $request->filter_date_to }}">
								<button class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Экпорт в Excel
								</button>
							</form>
						</div>
					</div>
				</div>
				<div class="panel-body no-padding">
					<table class="table table-striped">
						<thead>
						<tr>
							<th>№ п/п</th>
							{{--<th>ID #</th>--}}
							<th>Номер</th>
							<th>Статус</th>
							<th>ОГРН/ОГРНИП</th>
							<th>ИНН</th>
							<th>Краткое наименование организации</th>
							<th>Шаблон</th>
							<th>Дата заключения договора</th>
							{{--<th>Дата изменения</th>--}}
							<th>Действие</th>
						</tr>
						</thead>
						<tbody>
						@php
							$i=1;
						@endphp
						@if (count($docs))
							@foreach ($docs as $doc)
								<tr class="doc_row" data-doctypes_id="{{ $doctypes_id }}" data-id="{{ $doc->id }}">
									<td>
										@php
											echo $docs_quantity * ($page - 1) + $i;
											$i++;
										@endphp
									</td>
									{{--<td>{{ $doc->id }}</td>--}}
									<td>
										<a href="/docs/{{ $doctypes_id }}/{{ $doc->id }}?page={{ $request->page }}&filter_prefix={{ $request->filter_prefix }}&filter_status={{ $request->filter_status }}&filter_ogrn={{ $request->filter_ogrn }}&filter_inn={{ $request->filter_inn }}&filter_orgname={{ $request->filter_orgname }}&filter_date_from={{ $request->filter_date_from }}&filter_date_to={{ $request->filter_date_to }}">{{ $doc->prefix_number }}</a>
									</td>
									<td>
										@if (count($statuses) > 0)
											@foreach ($statuses->all() as $status)
												@if ($status->id == $doc->status_id)
													<span class="label" style="background-color:#{{ $status->color }};">
															{{ $status->name }}
														</span>
												@endif
											@endforeach
										@endif
									</td>
									<td>
										{{--@if (count($doctypes) > 0)
											@foreach ($doctypes->all() as $doctype)
												@if ($doctype->id == $doc->doctypes_id)
													{{ $doctype->name }}
													@break
												@endif
											@endforeach
										@endif--}}
										{{ $doc_values_arr[$doc->id]['5']['value'] }}
									</td>
									<td>
										@if (!empty($doc_values_arr[$doc->id]['4']['value']))
											<?php
											if (mb_substr($doc_values_arr[ $doc->id ]['4']['value'], 0, 2) == '00') {
												$doc_values_arr[ $doc->id ]['4']['value'] = mb_substr($doc_values_arr[ $doc->id ]['4']['value'], 2);
											}
											?>
											{{ $doc_values_arr[$doc->id]['4']['value'] }}
										@endif
									</td>
									<td>
										@if (!empty($doc_values_arr[$doc->id]['41']['value']))
											{{ $doc_values_arr[$doc->id]['41']['value'] }}
										@endif
									</td>
									<td>
										@if (count($templates) > 0)
											@foreach ($templates->all() as $template)
												@if ($template->id == $doc->templates_id)
													{{ $template->name }}
													@break
												@endif
											@endforeach
										@endif
									</td>
									<td>
										{{ $doc->status_3_created_at }}
									</td>
									{{--<td>
										{{ $doc->updated_at }}
									</td>--}}
									<td nowrap>
										<a href="/docs/{{ $doctypes_id }}/{{ $doc->id }}?page={{ $request->page }}&filter_prefix={{ $request->filter_prefix }}&filter_status={{ $request->filter_status }}&filter_ogrn={{ $request->filter_ogrn }}&filter_inn={{ $request->filter_inn }}&filter_orgname={{ $request->filter_orgname }}&filter_date_from={{ $request->filter_date_from }}&filter_date_to={{ $request->filter_date_to }}"
										   class="btn btn-default btn-sm">Показать</a>
										<a href="/log/{{ $doc->id }}" class="btn btn-default btn-sm"
										   target="_blank">Лог</a>
									</td>
								</tr>
							@endforeach
						@endif
						</tbody>
						<tfoot>
						<tr>
							<td colspan="20">Всего найдено: {{ $docs->total() }}</td>
						</tr>
						</tfoot>
					</table>
					{{ $docs->appends(['filter_prefix' => $request->filter_prefix, 'filter_status' => $request->filter_status, 'filter_ogrn' => $request->filter_ogrn, 'filter_inn' => $request->filter_inn, 'filter_orgname' => $request->filter_orgname, 'filter_date_from' => $request->filter_date_from, 'filter_date_to' => $request->filter_date_to])->links() }}
				</div>
			</div>
		</div>
	</div>
@endsection
