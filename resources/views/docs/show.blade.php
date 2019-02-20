@extends('layouts.master')

@section('breadcrumbs', Breadcrumbs::render('docs.show', $doctype, $docs))

@section('content')
	<div class="row">
		@if (!empty($rights))
			@if (count($prev_docs) > 0)
				<div class="col-md-9">
					<div class="panel">
						<div class="panel-heading">
							<h3 class="panel-title">Все оферты с ОГРН/ОГРИП {{ $ogrn }}</h3>
						</div>
						<div class="panel-body no-padding">
							<table class="table table-striped">
								<thead>
								<tr>
									<th>Номер</th>
									<th>Статус</th>
									<th>Шаблон</th>
									<th>Дата создания</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								@if (!empty($prev_docs['1']))
									@foreach ($prev_docs['1'] as $k => $v)
										<tr>
											<td>
												<a href="/docs/1/{{ $k }}?page={{ $request->page }}&filter_prefix={{ $request->filter_prefix }}&filter_status={{ $request->filter_status }}&filter_ogrn={{ $request->filter_ogrn }}&filter_inn={{ $request->filter_inn }}&filter_orgname={{ $request->filter_orgname }}">{{ $v['prefix_number'] }}</a>
											</td>
											<td>
												@if (count($statuses) > 0)
													@foreach ($statuses as $status)
														@if ($status->id == $v['status_id'])
															<span class="label"
																  style="background-color:#{{ $status->color }};">
																	{{ $status->name }}
																</span>
															@break
														@endif
													@endforeach
												@endif
											</td>
											<td>{{ $v['template'] }}</td>
											<td>{{ $v['created_at'] }}</td>
											<td>
												<a href="/docs/1/{{ $k }}?page={{ $request->page }}&filter_prefix={{ $request->filter_prefix }}&filter_status={{ $request->filter_status }}&filter_ogrn={{ $request->filter_ogrn }}&filter_inn={{ $request->filter_inn }}&filter_orgname={{ $request->filter_orgname }}"
												   class="btn btn-default btn-sm">Показать</a>
											</td>
										</tr>
									@endforeach
								@endif
								</tbody>
							</table>
						</div>
					</div>

					<div class="panel">
						<div class="panel-heading">
							<h3 class="panel-title">Все заявления с ОГРН/ОГРИП {{ $ogrn }}</h3>
						</div>
						<div class="panel-body no-padding">
							<table class="table table-striped">
								<thead>
								<tr>
									<th>Номер</th>
									<th>Статус</th>
									<th>Шаблон</th>
									<th>Дата создания</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								@if (!empty($prev_docs['2']))
									@foreach ($prev_docs['2'] as $k => $v)
										<tr>
											<td>
												<a href="/docs/2/{{ $k }}?page={{ $request->page }}&filter_prefix={{ $request->filter_prefix }}&filter_status={{ $request->filter_status }}&filter_ogrn={{ $request->filter_ogrn }}&filter_inn={{ $request->filter_inn }}&filter_orgname={{ $request->filter_orgname }}">{{ $v['prefix_number'] }}</a>
											</td>
											<td>
												@if (count($statuses) > 0)
													@foreach ($statuses as $status)
														@if($status->id == $v['status_id'])
															<span class="label"
																  style="background-color:#{{ $status->color }};">
																	{{ $status->name }}
																</span>
															@break
														@endif
													@endforeach
												@endif
											</td>
											<td>{{ $v['template'] }}</td>
											<td>{{ $v['created_at'] }}</td>
											<td>
												<a href="/docs/2/{{ $k }}?page={{ $request->page }}&filter_prefix={{ $request->filter_prefix }}&filter_status={{ $request->filter_status }}&filter_ogrn={{ $request->filter_ogrn }}&filter_inn={{ $request->filter_inn }}&filter_orgname={{ $request->filter_orgname }}"
												   class="btn btn-default btn-sm">Показать</a>
											</td>
										</tr>
									@endforeach
								@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
			@endif

			<div class="col-md-9">
				<div class="panel">
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
					<div class="panel-heading">
						<h3 class="panel-title">Документ "{{ $docs['prefix_number'] }}"</h3>
					</div>
					<div class="panel-body">
						<form method="POST"
							  action="/docs/{{ $doctypes_id }}/{{ $docs['id'] }}?page={{ $request->page }}">
							{{ csrf_field() }}
							{{ method_field('PUT') }}
							<input type="hidden" name="doc_id" value="{{ $docs['id'] }}">
							<input type="hidden" name="filter_prefix" value="{{ $request->filter_prefix }}">
							<input type="hidden" name="filter_status" value="{{ $request->filter_status }}">
							<input type="hidden" name="filter_ogrn" value="{{ $request->filter_ogrn }}">
							<input type="hidden" name="filter_inn" value="{{ $request->filter_inn }}">
							<input type="hidden" name="filter_orgname" value="{{ $request->filter_orgname }}">

							<fieldset class="well the-fieldset">
								<legend class="the-legend bold">Номер</legend>
								<span class="field_value">{{ $docs['prefix_number'] }}</span>
								<div style="float:right;">
									<button type="button" class="btn btn-info btn-xs copy_btn"><i
												class="fa fa-copy"></i></button>
								</div>
							</fieldset>
							<fieldset class="well the-fieldset">
								<legend class="the-legend bold">Статус</legend>
								@if (count($statuses) > 0)
									@foreach ($statuses as $status)
										@if($status->id == $docs['status_id'])
											<span class="field_value">{{ $status->name }}</span>
											@break
										@endif
									@endforeach
								@endif
								<div style="float:right;">
									<button type="button" class="btn btn-info btn-xs copy_btn"><i
												class="fa fa-copy"></i></button>
								</div>
							</fieldset>
							<fieldset class="well the-fieldset">
								<legend class="the-legend bold">Шаблон</legend>
								@if (count($templates) > 0)
									@foreach ($templates as $k => $v)
										@if ($v['id'] == $docs['templates_id'])
											<span class="field_value">{{ $v['name'] }}</span>
											@break
										@endif
									@endforeach
								@endif
								<div style="float:right;">
									<button type="button" class="btn btn-info btn-xs copy_btn"><i
												class="fa fa-copy"></i></button>
								</div>
							</fieldset>
							@if (count($doc_values_arr[$docs['id']]) > 0)
								@foreach ($doc_values_arr[$docs['id']] as $k => $v)
									@if (!$v['templates_fields_id'] && !$v['alias'])
										@continue
									@endif
									@if (in_array($k, array('21')))
										@continue
									@endif
									<fieldset class="well the-fieldset">
										<legend class="the-legend bold">{{ $v['name'] }}</legend>
										@switch($v['type'])
											@case('select')
											{{--@if ($v['link'] == 'okopfs')
												@if (count($okopfs) > 0)
													@foreach ($okopfs as $okopf)
														@if($okopf->id == $v['value'])
															<span class="field_value">{{ $okopf->name }}</span>
														@endif
													@endforeach
												@endif
											@elseif ($v['link'] == 'countries')
												@if (count($countries) > 0)
													@foreach ($countries as $country)
														@if($country->id == $v['value'])
															<span class="field_value">{{ $country->name }}</span>
														@endif
													@endforeach
												@endif
											@else--}}
											<span class="field_value">{{ $v['value'] }}</span>
											{{--@endif--}}
											<div style="float:right;">
												<button type="button" class="btn btn-info btn-xs copy_btn"><i
															class="fa fa-copy"></i></button>
											</div>
											@break
											@case('input')
											<?php
											if ($k == 4 && mb_substr($v['value'], 0, 2) == '00') {
												$v['value'] = mb_substr($v['value'], 2);
											}
											?>
											<span class="field_value">{{ $v['value'] }}</span>
											<div style="float:right;">
												<button type="button" class="btn btn-info btn-xs copy_btn"><i
															class="fa fa-copy"></i></button>
											</div>
											@break
											@case('textarea')
											<span class="field_value">{{ $v['value'] }}</span>
											<div style="float:right;">
												<button type="button" class="btn btn-info btn-xs copy_btn"><i
															class="fa fa-copy"></i></button>
											</div>
											@break
											@case('text')
											<div style="max-height:300px;overflow-x:scroll;overflow-y:scroll;padding-right:10px;">
												<span class="field_value">{!! $v['value'] !!}</span>
											</div>
											@break
											@case('checkbox')
											<span class="field_value">
					        						@if ($v['value'] == '1')
													Да
												@elseif (!$v['value'])
													Нет
												@else
													{{ $v['value'] }}
												@endif
				        						</span>
											@break
											@case('file')
											{{--<a href="{{ Storage::url($v['value']) }}" target="_blank">--}}
											<a href="/docs/file/{{ $docs['id'] }}/{{ $k }}">
												Скачать
											</a>
											@break
										@endswitch

										@if ($doctypes_id == '1' && !in_array($k, array('1','30')))
											@if (!empty($rights[1]) || !empty($rights[2]))
												@if (count($fieldsstatuses) > 0)
													<div class="radio-inline-group">
														@foreach ($fieldsstatuses as $status)
															<label>
																<input type="radio"
																	   id="status_id{{ $k }}_{{ $status->id }}"
																	   name="status_id{{ $k }}"
																	   value="{{ $status->id }}"
																	   data-field_name="{{ $v['name'] }}"
																	   class="status_class @if(in_array($status->id, array('2'))) agreed @endif"
																	   @if($status->id == $v['status_id']) checked @endif>
																<span class="label @if($status->id == $v['status_id'] && $status->id == '1') label-warning @elseif($status->id == $v['status_id'] && $status->id == '2') label-success @elseif($status->id == $v['status_id'] && $status->id == '3') label-danger @else label-default @endif">{{ $status->name }}</span>
															</label>
														@endforeach
													</div>
												@endif
											@endif
										@endif
									</fieldset>
								@endforeach
							@endif
							<fieldset class="well the-fieldset">
								<legend class="the-legend bold">XML-файл</legend>
								{{--<a href="{{ Storage::url($docs['file']) }}" target="_blank">--}}
								<a href="/docs/file/{{ $docs['id'] }}/xml">
									Скачать
								</a>
							</fieldset>
							<fieldset class="well the-fieldset">
								<legend class="the-legend bold">XML-файл в Base64</legend>
								<a href="/docs/file/{{ $docs['id'] }}/xmlbase64">
									Скачать
								</a>
							</fieldset>
							<fieldset class="well the-fieldset">
								<legend class="the-legend bold">Подпись</legend>
								{{--<a href="{{ Storage::url($docs['file_sign']) }}" target="_blank">--}}
								<a href="/docs/file/{{ $docs['id'] }}/signature">
									Скачать
								</a>
							</fieldset>
							<fieldset class="well the-fieldset">
								<legend class="the-legend bold">СНИЛС из ЭЦП</legend>
								<span class="field_value">{{ $docs['snils'] }}</span>
							</fieldset>
							<fieldset class="well the-fieldset">
								<legend class="the-legend bold">ФИО из ЭЦП</legend>
								<span class="field_value">{{ $docs['fullname'] }}</span>
							</fieldset>
							<fieldset class="well the-fieldset">
								<legend class="the-legend bold">Должность из ЭЦП</legend>
								<span class="field_value">{{ $docs['position'] }}</span>
							</fieldset>
							<fieldset class="well the-fieldset">
								<legend class="the-legend bold">Комментарий</legend>
								@if (!empty($rights[1]) || !empty($rights[2]))
									<textarea name="comment" class="form-control"
											  style="height:200px;">{{ $docs['comment'] }}</textarea>
								@else
									{{ $docs['comment'] }}
								@endif
							</fieldset>
							@if (count($doc_fields) > 0)
								@foreach ($doc_fields->all() as $doc_field)
									@if (empty($docs_fields_roles_rights[$doc_field->id]))
										@continue
									@endif
									@if (!empty($docs_fields_roles_rights[$doc_field->id]))
										@if(!$docs_fields_roles_rights[$doc_field->id])
											@continue
										@endif
									@endif

									<fieldset class="well the-fieldset">
										<legend class="the-legend bold">{{ $doc_field->name }}</legend>
										@if ($docs_fields_roles_rights[$doc_field->id] == 1)
											@if (!empty($doc_values_arr[$docs['id']][$doc_field->id]['value']))
												@if($doc_field->type == 'select')
													@if ($doc_field->link == 'pays')
														@if (count($pays) > 0)
															@foreach ($pays as $pay)
																@if ($pay->id == $doc_values_arr[$docs['id']][$doc_field->id]['value'])
																	{{ $pay->name }}
																	@break
																@endif
															@endforeach
														@endif
													@endif
													@if ($doc_field->link == 'junks')
														@if (count($junks) > 0)
															@foreach ($junks as $junk)
																@if ($junk->id == $doc_values_arr[$docs['id']][$doc_field->id]['value'])
																	{{ $junk->name }}
																	@break
																@endif
															@endforeach
														@endif
													@endif
													@if ($doc_field->link == 'owners')
														@if (count($owners) > 0)
															@foreach ($owners as $owner)
																@if ($owner->id == $doc_values_arr[$docs['id']][$doc_field->id]['value'])
																	{{ $owner->name }}
																	@break
																@endif
															@endforeach
														@endif
													@endif
												@else
													{{ $doc_values_arr[$docs['id']][$doc_field->id]['value'] }}
												@endif
											@endif
										@elseif (!empty($docs_fields_roles_rights[$doc_field->id]))
											@if ($docs_fields_roles_rights[$doc_field->id] == 2)
												@switch($doc_field->type)
													@case('select')
													<select name="doc_field{{ $doc_field->id }}" class="form-control">
														<option value="0" selected="selected">---</option>
														@if ($doc_field->link == 'pays')
															@if (count($pays) > 0)
																@foreach ($pays as $pay)
																	<option value="{{ $pay->id }}"
																			@if (old('doc_field'.$doc_field->id) == $pay->id) selected
																			@elseif (!empty($doc_values_arr[$docs['id']][$doc_field->id]['value'])) @if ($pay->id == $doc_values_arr[$docs['id']][$doc_field->id]['value']) selected="selected" @endif @endif>{{ $pay->name }}</option>
																@endforeach
															@endif
														@endif
														@if ($doc_field->link == 'junks')
															@if (count($junks) > 0)
																@foreach ($junks as $junk)
																	<option value="{{ $junk->id }}"
																			@if (old('doc_field'.$doc_field->id) == $junk->id) selected
																			@elseif (!empty($doc_values_arr[$docs['id']][$doc_field->id]['value'])) @if ($junk->id == $doc_values_arr[$docs['id']][$doc_field->id]['value']) selected="selected" @endif @endif>{{ $junk->name }}</option>
																@endforeach
															@endif
														@endif
														@if ($doc_field->link == 'owners')
															@if (count($owners) > 0)
																@foreach ($owners as $owner)
																	<option value="{{ $owner->id }}"
																			@if (old('doc_field'.$doc_field->id) == $owner->id) selected
																			@elseif (!empty($doc_values_arr[$docs['id']][$doc_field->id]['value'])) @if ($owner->id == $doc_values_arr[$docs['id']][$doc_field->id]['value']) selected="selected" @endif @endif>{{ $owner->name }}</option>
																@endforeach
															@endif
														@endif
													</select>
													@break
													@case('input')
													<input type="text" name="doc_field{{ $doc_field->id }}"
														   value="@if (old('doc_field'.$doc_field->id)){{ old('doc_field'.$doc_field->id) }} @elseif (!empty($doc_values_arr[$docs['id']][$doc_field->id]['value'])) {{ $doc_values_arr[$docs['id']][$doc_field->id]['value'] }} @endif"
														   class="form-control">
													@break
													@case('textarea')
													<textarea name="doc_field{{ $doc_field->id }}" class="form-control"
															  style="height:70px;background-color:#fff;">@if(old('doc_field'.$doc_field->id)){{old('doc_field'.$doc_field->id)}} @elseif (!empty($doc_values_arr[$docs['id']][$doc_field->id]['value'])) {{ $doc_values_arr[$docs['id']][$doc_field->id]['value'] }} @endif</textarea>
													@break
												@endswitch
											@endif
										@endif
									</fieldset>
								@endforeach
							@endif
							<br>
							<div>
								<div style="float:left;">
									@if (in_array($docs['status_id'], array(1)))
										@if (!empty($rights[1]))
											<button type="button" data-status_id="7" data-text="проверку"
													class="btn btn-info doc_btn"
													style="background-color:#f0ad4e;border-color:#d49131;">Проверено
											</button>
										@endif
										@if (!empty($rights[1]))
											<button type="button" data-status_id="4" class="btn btn-info doc_btn"
													style="background-color:#d9534f;border-color:#b82520;">Отказано
											</button>
										@endif
									@elseif (in_array($docs['status_id'], array(3)))
										@if (!empty($rights[3]))
											<button type="button" data-status_id="8" class="btn btn-info doc_btn"
													style="background-color:#5bc0de;border-color:#2a9fc2;">Создан ПЮЛ
											</button>
										@endif
										@if (!empty($rights[8]))
											<button type="button" data-status_id="10" class="btn btn-info doc_btn"
													style="background-color:#4e52f0;border-color:#3034ce;">Исполнено
											</button>
											<button type="button" data-status_id="4" class="btn btn-info doc_btn"
													style="background-color:#d9534f;border-color:#b82520;">Отказано
											</button>
										@endif
									@elseif (in_array($docs['status_id'], array(7)))
										@if (!empty($rights[2]))
											<button type="button" data-status_id="3" data-text="согласование"
													class="btn btn-info doc_btn"
													style="background-color:#5cb85c;border-color:#2b9e2b;">Согласовано
											</button>
										@endif
										{{--@if (!empty($rights[1]))
											<button type="button" data-status_id="4" class="btn btn-info doc_btn" style="background-color:#d9534f;border-color:#b82520;">Отказано</button>
										@endif--}}
										@if (!empty($rights[2]))
											<button type="button" data-status_id="1" class="btn btn-info doc_btn"
													style="background-color:#d9534f;border-color:#b82520;">Отказано
											</button>
										@endif
									@elseif (in_array($docs['status_id'], array(8)))
										@if (!empty($rights[4]))
											<button type="button" data-status_id="9" class="btn btn-info doc_btn"
													style="background-color:#f04eee;border-color:#c730c5;">Отправлена
												карточка договора
											</button>
										@endif
									@endif
								</div>
								<div style="float:right;">
									@if (!empty($rights[1]) || !empty($rights[2]) || !empty($rights[4]))
										<button type="button" data-status_id="save" class="btn btn-primary doc_btn">
											Сохранить
										</button>
									@endif
									@if (!empty($rights[1]) || !empty($rights[2]) || !empty($rights[3]) || !empty($rights[4]) || !empty($rights[5]) || !empty($rights[8]))
										<button type="button" class="btn btn-default"
												onClick="window.location.href='/docs/{{ $doctypes_id }}?page={{ $request->page }}&filter_prefix={{ $request->filter_prefix }}&filter_status={{ $request->filter_status }}&filter_ogrn={{ $request->filter_ogrn }}&filter_inn={{ $request->filter_inn }}&filter_orgname={{ $request->filter_orgname }}';">
											Отмена
										</button>
									@endif
								</div>
							</div>
							<input type="hidden" id="status_id" name="status_id">

							<!-- Checked Modal -->
							<div class="modal fade" id="modal" role="dialog">
								<div class="modal-dialog modal-lg">
									<!-- Modal content-->
									<div class="modal-content">
										<div style="padding:10px 20px;">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
										</div>
										<div class="modal-body" style="padding:10px 30px 25px;">
											<div class="form-group add-content"></div>
											<button type="button" id="confirm_modal_btn" data-dismiss="modal"
													class="btn btn-primary">Подтвердить
											</button>
											<button type="button" data-dismiss="modal" class="btn btn-default">Отмена
											</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		@else
			<div class="panel-body">
				У Вас нет прав на просмотр данного документа
			</div>
		@endif
	</div>
@endsection
