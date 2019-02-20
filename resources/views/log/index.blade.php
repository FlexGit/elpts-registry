@extends('layouts.master')

@section('breadcrumbs', Breadcrumbs::render('log.index'))

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="panel">
   				<div class="panel-heading">
					<h3 class="panel-title">Фильтры</h3>
				</div>
				<div class="panel-body no-padding">
					<form method="POST" action="/log">
						{{ csrf_field() }}
						<table class="table">
							<thead>
								<tr>
									<th>Документ</th>
									<th>Дата начала</th>
									<th>Дата окончания</th>
									<th>Событие</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<input type="text" name="filter_doc" value="@if (!empty($request->filter_doc)){{ $request->filter_doc }}@endif" class="form-control">
									</td>
									<td>
										<div class="input-group date" id="filter_date_from">
											<input type="text" name="filter_date_from" value="@if (!empty($request->filter_date_from)){{ $request->filter_date_from }}@endif" class="form-control">
											<span class="input-group-addon">
                        						<span class="glyphicon glyphicon-calendar"></span>
                    						</span>
										</div>
									</td>
									<td>
										<div class="input-group date" id="filter_date_to">
											<input type="text" name="filter_date_to" value="@if (!empty($request->filter_date_to)){{ $request->filter_date_to }}@endif" class="form-control">
											<span class="input-group-addon">
                        						<span class="glyphicon glyphicon-calendar"></span>
                    						</span>
										</div>
									</td>
									<td>
										<select name="filter_operation" class="form-control">
											<option value="">---</option>
											@foreach ($operation_type_arr as $operation)
												@if (count($filter_operations_arr[$operation]) > 0)
									        		<optgroup label="{{ $operation }}">
											        	@foreach ($filter_operations_arr[$operation] as $k => $v)
															<option value="{{ $k }}" @if (!empty($request->filter_operation) && $request->filter_operation == $k) selected @endif>{{ $v }}</option>
											      		@endforeach
													</optgroup>
												@endif
											@endforeach
										</select>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="5" style="text-align:center;">
										<br>
										<button class="btn btn-primary">Поиск</button>
										<button type="button" class="btn btn-primary" onClick="window.location.href='/log';">Сбросить</button>
									</td>
								</tr>
							</tfoot>
						</table>
					</form>
				</div>
			</div>

			<div class="panel">
   				<div class="panel-heading">
					<h3 class="panel-title">Лог</h3>
				</div>
				<div class="panel-body no-padding">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>ID #</th>
								<th>Дата</th>
								<th>Оператор/ОГРН пользователя</th>
								<th>Событие</th>
								<th>Значение</th>
								<th>Документ</th>
							</tr>
						</thead>
						<tbody>
							@if (count($logs))
								@foreach ($logs->all() as $log)
									<tr class="doc_row">
										<td>{{ $log->id }}</td>
										<td nowrap>{{ $log->created_at }}</td>
										<td nowrap>{{ $log->user_name }}</td>
										<td>{{ $operations_arr[$log->operation_id]['name'] }}</td>
										<td>{{ $log->value }}</td>
										<td nowrap>{{ $log->prefix_number }}</td>
									</tr>
								@endforeach
							@endif
						</tbody>
					</table>
					{{ $logs->appends(['filter_date_from' => $request->filter_date_from, 'filter_date_to' => $request->filter_date_to, 'filter_operation' => $request->filter_operation])->links() }}
				</div>
			</div>
		</div>
	</div>
@endsection
