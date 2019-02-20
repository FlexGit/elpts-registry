@extends('layouts.master')

@section('content')
	<div class="row">
		<div class="col-md-12">
			@if(count($doctypes))
				@foreach($doctypes as $doctype)
					<div class="panel">
		   				<div class="panel-heading">
							<h3 class="panel-title">Новые документы "{{ $doctype->name }}"</h3>
						</div>
						<div class="panel-body no-padding">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>ID #</th>
										<th>Номер</th>
										<th>Статус</th>
										{{--<th>Тип документа</th>
										<th>Организация</th>--}}
										<th>ОГРН/ОГРНИП</th>
										{{--<th>ИНН</th>--}}
										<th>Шаблон</th>
										<th>Дата создания</th>
										<th>Действие</th>
									</tr>
								</thead>
								<tbody>
									@if (count($docs[$doctype->id]))
										@foreach ($docs[$doctype->id] as $doc)
											<tr class="doc_row" data-doctypes_id="{{ $doctype->id }}" data-id="{{ $doc->id }}">
												<td>{{ $doc->id }}</td>
												<td>{{ $doc->prefix_number }}</td>
												<td>
													@if (count($statuses) > 0)
														@foreach ($statuses->all() as $status)
															@if ($status->id == $doc->status_id)
																<span class="label @if (in_array($doc->status_id, array('1', '3'))) label-info @elseif ($doc->status_id == '7') label-warning @elseif (in_array($doc->status_id, array('8','9','10'))) label-success @elseif ($doc->status_id == '4') label-danger @endif">
																	{{ $status->name }}
																</span>
															@endif
														@endforeach
													@endif
												</td>
												{{--<td>
													{{ $doctype->name }}
												</td>
												<td>
													{{ $doc_values_arr[$doc->id]['41']['value'] }}
												</td>--}}
												<td>
													{{ $doc_values_arr[$doc->id]['5']['value'] }}
												</td>
												{{--<td>
													{{ $doc_values_arr[$doc->id]['4']['value'] }}
												</td>--}}
												<td>
													@if (count($templates[$doctype->id]) > 0)
														@foreach ($templates[$doctype->id] as $template)
															@if ($template->id == $doc->templates_id) {{ $template->name }} @endif
														@endforeach
													@endif
												</td>
												<td>
													{{ $doc->created_at }}
												</td>
												<td>
													<a href="/docs/{{ $doctype->id }}/{{ $doc->id }}" class="btn btn-default btn-sm">Показать</a>
													<a href="/log/{{ $doc->id }}" class="btn btn-default btn-sm" target="_blank">Лог</a>
												</td>
											</tr>
										@endforeach
									@endif
								</tbody>
							</table>
						</div>
					</div>
				@endforeach
			@endif
		</div>
	</div>
@endsection
