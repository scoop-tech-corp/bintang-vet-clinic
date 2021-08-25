$(document).ready(function() {
	let optCabang1 = '';
	let optCabang2 = '';

	let getId = null;
	let modalState = '';
	let isValidNamaKelompok = false;
	let isValidSelectedCabang = false;
	let isBeErr = false;

	let paramUrlSetup = {
		orderby:'',
		column: '',
		keyword: '',
		branchId: ''
	};

	if (role.toLowerCase() != 'admin') {
    $('.columnAction').hide(); $('#filterCabang').hide();
  } else {
		$('.section-left-box-title').append(
			`<button class="btn btn-info openFormAdd m-r-10px">Tambah</button>
			<button class="btn btn-info openFormUpload">Upload Sekaligus</button>`);

		$('.section-right-box-title').addClass('width-350px');
		$('.section-right-box-title').append(`<select id="filterCabang" style="width: 50%"></select>`);

		$('#filterCabang').select2({ placeholder: 'Cabang', allowClear: true });
		$('#filterCabang').append(`<option value=''>Cabang</option>`);
		
		loadCabang(); // load cabang
	}

	loadKelompokObat(); // load kelompok obat	

	$('.input-search-section .fa').click(function() {
		onSearch($('.input-search-section input').val());
	});

	$('.input-search-section input').keypress(function(e) {
		if (e.which == 13) { onSearch($(this).val()); }
	});
	
	$('.onOrdering').click(function() {
		const column = $(this).attr('data');
		const orderBy = $(this).attr('orderby');
		$('.onOrdering[data="'+column+'"]').children().remove();

		if (orderBy == 'none' || orderBy == 'asc') {
			$(this).attr('orderby', 'desc');
			$(this).append('<span class="fa fa-sort-desc"></span>');

		} else if(orderBy == 'desc') {
			$(this).attr('orderby', 'asc');
			$(this).append('<span class="fa fa-sort-asc"></span>');
		}

		paramUrlSetup.orderby = $(this).attr('orderby');
		paramUrlSetup.column = column;

		loadKelompokObat();
	});

	$('#namaKelompok').keyup(function () { validationForm(); });
	$('#selectedCabang').change(function () { validationForm(); });
	
	$('.openFormAdd').click(function() {
		modalState = 'add';
		$('.modal-title').text('Tambah Kelompok Obat');
		refreshForm(); formConfigure();
	});

	$('.openFormUpload').click(function() {
		$('#modal-upload-kelompok-obat .modal-title').text('Upload Kelompok Obat Sekaligus');
		$('#modal-upload-kelompok-obat').modal('show');
		$('.validate-error').html('');
	});

	$('.btn-download-template').click(function() {
		$.ajax({
			url     : $('.baseUrl').val() + '/api/kelompok-obat/download-template',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			xhrFields: { responseType: 'blob' },
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data, status, xhr) {
				let disposition = xhr.getResponseHeader('content-disposition');
				let matches = /"([^"]*)"/.exec(disposition);
				let filename = (matches != null && matches[1] ? matches[1] : 'file.xlsx');
				let blob = new Blob([data],{type:'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
				let downloadUrl = URL.createObjectURL(blob);
				let a = document.createElement("a");

				a.href = downloadUrl;
				a.download = filename
				document.body.appendChild(a);
				a.click();

			}, complete: function() { $('#loading-screen').hide(); },
			error: function(err) {
				if (err.status == 401) {
					localStorage.removeItem('vet-clinic');
					location.href = $('.baseUrl').val() + '/masuk';
				}
			}
		});

	});

	$("#fileupload").fileupload({
		url: $('.baseUrl').val() + '/api/kelompok-obat/upload-template',
		headers : { 'Authorization': `Bearer ${token}` },
		dropZone: '#dropZone',
		dataType: 'json',
		autoUpload: false,
	}).on('fileuploadadd', function (e, data) {
		let fileTypeAllowed = /.\.(xlsx|xls)$/i;
		let fileName = data.originalFiles[0]['name'];
		let fileSize = data.originalFiles[0]['size'];
		
		if (!fileTypeAllowed.test(fileName)) {
			$('.validate-error').html('File harus berformat .xlsx atau .xls');
		} else {
			$('.validate-error').html('');
			data.submit();
		}
	}).on('fileuploaddone', function(e, data) {
		$('#modal-confirmation').hide();

		$("#msg-box .modal-body").text('Berhasil Upload Barang');
		$('#msg-box').modal('show');
		setTimeout(() => {
			$('#modal-upload-kelompok-obat').modal('toggle');
			loadKelompokObat();
		}, 1000);
	}).on('fileuploadfail', function(e, data) {
		const getResponsError = data._response.jqXHR.responseJSON.errors.hasOwnProperty('file') ? data._response.jqXHR.responseJSON.errors.file 
			: data._response.jqXHR.responseJSON.errors;

		let errText = '';
		$.each(getResponsError, function(idx, v) {
			errText += v + ((idx !== getResponsError.length - 1) ? '<br/>' : '');
		});
		$('.validate-error').append(errText)
	}).on('fileuploadprogressall', function(e,data) {
	});

	$('#btnSubmitKelompokObat').click(function() {

		if (modalState == 'add') {

			const fd = new FormData();
			fd.append('NamaGrup', $('#namaKelompok').val());
			fd.append('Cabang', $('#selectedCabang').val());

			$.ajax({
				url : $('.baseUrl').val() + '/api/kelompok-obat',
				type: 'POST',
				dataType: 'JSON',
				headers: { 'Authorization': `Bearer ${token}` },
				data: fd, contentType: false, cache: false,
				processData: false,
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(resp) {

					$("#msg-box .modal-body").text('Berhasil Menambah Data');
					$('#msg-box').modal('show');

					setTimeout(() => {
						$('#modal-kelompok-obat').modal('toggle');
						refreshForm(); loadKelompokObat();
					}, 1000);
				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
					if (err.status === 422) {
						let errText = ''; $('#beErr').empty(); $('#btnSubmitKelompokObat').attr('disabled', true);
						$.each(err.responseJSON.errors, function(idx, v) {
							errText += v + ((idx !== err.responseJSON.errors.length - 1) ? '<br/>' : '');
						});
						$('#beErr').append(errText); isBeErr = true;
					} else if (err.status == 401) {
						localStorage.removeItem('vet-clinic');
						location.href = $('.baseUrl').val() + '/masuk';
					}
				}
			});

		} else {
			// edit
			$('#modal-confirmation .modal-title').text('Peringatan');
			$('#modal-confirmation .box-body').text('Anda yakin untuk mengubah data ini ?');
			$('#modal-confirmation').modal('show');
		}
	});

	$('#submitConfirm').click(function() {

		if (modalState == 'edit') {
			// process edit
			const datas = {
				id: getId,
				NamaGrup: $('#namaKelompok').val(),
				Cabang: $('#selectedCabang').val()
			};

			$.ajax({
				url : $('.baseUrl').val() + '/api/kelompok-obat',
				type: 'PUT',
				dataType: 'JSON',
				headers: { 'Authorization': `Bearer ${token}` },
				data: datas,
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(data) {
					$('#modal-confirmation .modal-title').text('Peringatan');
					$('#modal-confirmation').modal('toggle');

					$("#msg-box .modal-body").text('Berhasil Mengubah Data');
					$('#msg-box').modal('show');

					setTimeout(() => {
						$('#modal-kelompok-obat').modal('toggle');
						refreshForm(); loadKelompokObat();
					}, 1000);

				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
					if (err.status === 422) {
						$('#modal-confirmation .modal-title').text('Peringatan');
						$('#modal-confirmation').modal('toggle');

						let errText = ''; $('#beErr').empty(); $('#btnSubmitKelompokObat').attr('disabled', true);
						$.each(err.responseJSON.errors, function(idx, v) {
							errText += v + ((idx !== err.responseJSON.errors.length - 1) ? '<br/>' : '');
						});
						$('#beErr').append(errText); isBeErr = true;
					} else if (err.status == 401) {
						localStorage.removeItem('vet-clinic');
						location.href = $('.baseUrl').val() + '/masuk';
					}
				}
			});
		} else {
			// process delete
			$.ajax({
				url     : $('.baseUrl').val() + '/api/kelompok-obat',
				headers : { 'Authorization': `Bearer ${token}` },
				type    : 'DELETE',
				data	  : { id: getId },
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(data) {
					$('#modal-confirmation .modal-title').text('Peringatan');
					$('#modal-confirmation').modal('toggle');

					$("#msg-box .modal-body").text('Berhasil menghapus data');
					$('#msg-box').modal('show');

					loadKelompokObat();

				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
					if (err.status == 401) {
						localStorage.removeItem('vet-clinic');
						location.href = $('.baseUrl').val() + '/masuk';
					}
				}
			});
		}
	});

	$('#filterCabang').on('select2:select', function () { onFilterCabang($(this).val()); });
  $('#filterCabang').on("select2:unselect", function () { onFilterCabang($(this).val()); });

  function onFilterCabang(value) {
    paramUrlSetup.branchId = value;
		loadKelompokObat();
  }

	function onSearch(keyword) {
		paramUrlSetup.keyword = keyword;
		loadKelompokObat();
	}

	function loadKelompokObat() {
		getId = null;
		modalState = '';
		$.ajax({
			url     : $('.baseUrl').val() + '/api/kelompok-obat',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { orderby: paramUrlSetup.orderby, column: paramUrlSetup.column, keyword: paramUrlSetup.keyword, branch_id: paramUrlSetup.branchId },
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {
				let listKelompokObat = '';
				$('#list-kelompok-obat tr').remove();

        if (data.length) {
          $.each(data, function(idx, v) {
            listKelompokObat += `<tr>`
              + `<td>${++idx}</td>`
              + `<td>${v.group_name}</td>`
              + `<td>${v.branch_name}</td>`
              + `<td>${v.created_by}</td>`
              + `<td>${v.created_at}</td>`
              + ((role.toLowerCase() != 'admin') ? `` : `<td>
                  <button type="button" class="btn btn-warning openFormEdit" value=${v.id}><i class="fa fa-pencil" aria-hidden="true"></i></button>
                  <button type="button" class="btn btn-danger openFormDelete" value=${v.id}><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                </td>`)
              + `</tr>`;
          });
        } else {
          listKelompokObat += `<tr class="text-center"><td colspan="6">Tidak ada data.</td></tr>`;
        }
				$('#list-kelompok-obat').append(listKelompokObat);

				$('.openFormEdit').click(function() {
					const getObj = data.find(x => x.id == $(this).val());
					modalState = 'edit';

					$('.modal-title').text('Edit Kelompok Obat');
					refreshForm();

					formConfigure();
					getId = getObj.id;
					$('#namaKelompok').val(getObj.group_name);
					$('#selectedCabang').val(getObj.branch_id); $('#selectedCabang').trigger('change');
				});
			
				$('.openFormDelete').click(function() {
					getId = $(this).val();
					modalState = 'delete';

					$('#modal-confirmation .modal-title').text('Peringatan');
					$('#modal-confirmation .box-body').text('Anda yakin ingin menghapus data ini?');
					$('#modal-confirmation').modal('show');
				});

			}, complete: function() { $('#loading-screen').hide(); },
			error: function(err) {
				if (err.status == 401) {
					localStorage.removeItem('vet-clinic');
					location.href = $('.baseUrl').val() + '/masuk';
				}
			}
		});
	}

	function formConfigure() {
		$('#selectedCabang').select2();
		$('#modal-kelompok-obat').modal('show');
		$('#btnSubmitKelompokObat').attr('disabled', true);
	}

	function refreshForm() {
		$('#namaKelompok').val(null);
		$('#selectedCabang').val(null);
		$('#namaKelompokErr1').text(''); isValidNamaKelompok = true;
		$('#cabangErr1').text(''); isValidSelectedCabang = true;
		$('#beErr').empty(); isBeErr = false;
	}

	function validationForm() {
		if (!$('#namaKelompok').val()) {
			$('#namaKelompokErr1').text('Nama kelompok harus di isi'); isValidNamaKelompok = false;
		} else { 
			$('#namaKelompokErr1').text(''); isValidNamaKelompok = true;
		}

		if (!$('#selectedCabang').val()) {
			$('#cabangErr1').text('Cabang harus di isi'); isValidSelectedCabang = false;
		} else {
			$('#cabangErr1').text(''); isValidSelectedCabang = true;
		}

		$('#beErr').empty(); isBeErr = false;

		if (!isValidNamaKelompok || !isValidSelectedCabang || isBeErr) {
			$('#btnSubmitKelompokObat').attr('disabled', true);
		} else {
			$('#btnSubmitKelompokObat').attr('disabled', false);
		}
	}

	function loadCabang() {
		$.ajax({
			url     : $('.baseUrl').val() + '/api/cabang',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {
				optCabang1 += `<option value=''>Pilih Cabang</option>`;
				optCabang2 += `<option value=''>Cabang</option>`;
	
				if (data.length) {
					for (let i = 0 ; i < data.length ; i++) {
						optCabang1 += `<option value=${data[i].id}>${data[i].branch_name}</option>`;
						optCabang2 += `<option value=${data[i].id}>${data[i].branch_name}</option>`;
					}
				}
				$('#selectedCabang').append(optCabang1); $('#filterCabang').append(optCabang2);
			}, complete: function() { $('#loading-screen').hide(); },
			error: function(err) {
				if (err.status == 401) {
					localStorage.removeItem('vet-clinic');
					location.href = $('.baseUrl').val() + '/masuk';
				}
			}
		});
	}

});