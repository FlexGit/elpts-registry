$(document).ready(function () {
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$(document).on('click', '#auth_modal_btn', function () {
		var certificates = $('#certificates').find(':selected');
		var ogrn = certificates.data('ogrn');
		var snils = certificates.data('snils');
		var file = 'Auth';

		window.cspsignplugin.getCertificates().then(function (data) {
			var br = 0;
			for (var i in data) {
				if (i === certificates.val()) {
					br = 1;

					window.cspsignplugin.sign(file, data[i]).then(function (data) {
						var request_data = 'ogrn=' + ogrn + '&snils=' + snils + '&file=' + encodeURIComponent(file) + '&signature=' + encodeURIComponent(data);
						$.ajax({
							url: '/users/ajaxAuthRequest',
							type: 'POST',
							data: request_data,
							success: function (data) {
								//console.log(data);

								if (data.response.msg === 'success') {
									window.location.href = '/';
								} else {
									alert('Внимание! Доступ в приложение невозможен.\n' + data.response.msg);
									window.location.href = '/denied';
								}
							}
						});
					}, function (error) {
						alert('Внимание! Доступ в приложение невозможен. Ошибка при подписании.\nВозможно, выбранный сертификат не соответствует сведениям из Вашей ЭЦП.');
						window.location.href = '/denied';
					});

					if (br) return false;
				}
			}
		});
	});
});

var gListCertificates;

function loadCertificates() {
	gListCertificates = [];
	var element = window.document.getElementById("certificates");

	var _error = function (error) {
		var msg = error.message;
		if (typeof msg == "undefined") {
			msg = error;
		}
		//console.log("Error: ", msg);
		alert(msg);
	};
	var _add = function (data) {
		var index = this.index;

		window.cspsignplugin.getCertificateProperty(this.certificate, 'NotAfter').then(function (data_after) {
			var nowDate = new Date().toJSON().slice(0, 10);
			var data_after_arr = data_after.split('.');
			var afterDate = data_after_arr[2] + data_after_arr[1] + data_after_arr[0];
			var nowDateInt = parseInt(nowDate.toString().replace(/-/g, ''));
			var afterDateInt = parseInt(afterDate);

			if (nowDateInt > afterDateInt) {
				return false;
			}

			var option = document.createElement("Option");

			var data_arr = data.split(',');
			var ogrn, snils, position, lastname, firstname = '';
			data_arr.forEach(function (item, i, data_arr) {
				var param = item.trim();

				if (!ogrn && (param.indexOf('ОГРН') === 0 || param.indexOf('OGRN') === 0)) {
					ogrn = param.substring(5);
				}
				if (!snils && (param.indexOf('СНИЛС') === 0 || param.indexOf('SNILS') === 0)) {
					snils = param.substring(6);
				}
				if (!position && (param.indexOf('Т=') === 0 || param.indexOf('T=') === 0)) {
					position = param.substring(2);
				}
				if (!lastname && param.indexOf('SN=') === 0) {
					lastname = param.substring(3);
				}
				if (!firstname && param.indexOf('G=') === 0) {
					firstname = param.substring(2);
				}
			});

			if (ogrn == null || snils == null) {
				return false;
			}

			option.text = lastname + ' ' + firstname + ' (ОГРН: ' + ogrn + ', СНИЛС: ' + snils + ', Действителен до: ' + data_after + ')';
			option.setAttribute('data-ogrn', ogrn);
			option.setAttribute('data-snils', snils);
			option.value = index;
			element.add(option);
		});
	};
	var _load = function (data) {
		for (var i in data) {
			var obj = {};
			obj.index = i;
			obj.certificate = data[i];
			obj.func = _add;
			var _success = obj.func.bind(obj);
			window.cspsignplugin.getCertificateProperty(data[i], 'subject').then(_success, _error);
			gListCertificates.push(obj);
		}
	};

	if (element) {
		if (typeof window.cspsignplugin !== "undefined") {
			for (var i = (element.options.length - 1); i >= 0; i--) {
				element.options.remove(i);
			}
			window.cspsignplugin.getCertificates().then(_load, _error);
		}
	}
}

function _loadPage() {
	if (typeof window.cspsignplugin !== "undefined") {
		loadCertificates();
		$('#auth_modal').modal();
	} else {
		alert('Внимание! Доступ в приложение невозможен.\nНе удалось инициализировать плагин "Signal-COM CSP Plugin".');
		window.location.href = '/denied';
	}
}

setTimeout(_loadPage, 2000);
