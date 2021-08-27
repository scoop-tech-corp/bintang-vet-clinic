$(document).ready(function() {
	let optKategoriJasa = '';
	let optCabang1 = '';
	let optCabang2 = '';

	let getId = null;
	let modalState = '';
	let isValidNamaJasa = false;
	let isValidSelectedKategoriJasa = false;
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
		$('.section-left-box-title').append(`<button class="btn btn-info openFormAdd m-r-10px">Tambah</button>`);
		$('.section-right-box-title').addClass('width-350px');
		$('.section-right-box-title').append(`<select id="filterCabang" style="width: 50%"></select>`);

		$('#filterCabang').select2({ placeholder: 'Cabang', allowClear: true });
		$('#filterCabang').append(`<option value=''>Cabang</option>`);
		
		loadKategoriJasa(); // load kategori barang
		loadCabang(); // load cabang
	}

	loadDaftarJasa(); // load daftar barang

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

		loadDaftarJasa();
	});

	$('#namaJasa').keyup(function () { validationForm(); });
	$('#selectedKategoriJasa').change(function () { validationForm(); });
	$('#selectedCabang').change(function () { validationForm(); });
	
	$('.openFormAdd').click(function() {
		modalState = 'add';
		$('.modal-title').text('Tambah Daftar Jasa');
		refreshForm();
		formConfigure();
	});

	$('#btnSubmitDaftarJasa').click(function() {

		if (modalState == 'add') {

			const fd = new FormData();
			fd.append('NamaLayanan', $('#namaJasa').val());
			fd.append('KategoriJasa', $('#selectedKategoriJasa').val());
			fd.append('CabangId', $('#selectedCabang').val());

			$.ajax({
				url : $('.baseUrl').val() + '/api/daftar-jasa',
				type: 'POST',
				dataType: 'JSON',
				headers: { 'Authorization': `Bearer ${token}` },
				data: fd, contentType: false, cache: false,
				processData: false,
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(resp) {

					$("#msg-box .modal-body").text('Berhasil Menambah Daftar Jasa');
					$('#msg-box').modal('show');

					setTimeout(() => {
						$('#modal-daftar-jasa').modal('toggle');
						refreshForm();
						loadDaftarJasa();
					}, 1000);
				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
					if (err.status === 422) {
						let errText = ''; $('#beErr').empty(); $('#btnSubmitDaftarJasa').attr('disabled', true);
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
			$('#modal-confirmation .box-body').text('Anda yakin untuk mengubah daftar jasa ?');
			$('#modal-confirmation').modal('show');
		}
	});

	$('#submitConfirm').click(function() {

		if (modalState == 'edit') {
			// process edit
			const datas = {
				id: getId,
				NamaLayanan: $('#namaJasa').val(),
				KategoriJasa: $('#selectedKategoriJasa').val(),
				CabangId: $('#selectedCabang').val()
			};

			$.ajax({
				url : $('.baseUrl').val() + '/api/daftar-jasa',
				type: 'PUT',
				dataType: 'JSON',
				headers: { 'Authorization': `Bearer ${token}` },
				data: datas,
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(data) {
					$('#modal-confirmation .modal-title').text('Peringatan');
					$('#modal-confirmation').modal('toggle');

					$("#msg-box .modal-body").text('Berhasil Mengubah Daftar Jasa');
					$('#msg-box').modal('show');

					setTimeout(() => {
						$('#modal-daftar-jasa').modal('toggle');
						refreshForm();
						loadDaftarJasa();
					}, 1000);

				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
					if (err.status == 401) {
						localStorage.removeItem('vet-clinic');
						location.href = $('.baseUrl').val() + '/masuk';
					}
				}
			});
		} else {
			// process delete
			$.ajax({
				url     : $('.baseUrl').val() + '/api/daftar-jasa',
				headers : { 'Authorization': `Bearer ${token}` },
				type    : 'DELETE',
				data	  : { id: getId },
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(data) {
					$('#modal-confirmation .modal-title').text('Peringatan');
					$('#modal-confirmation').modal('toggle');

					$("#msg-box .modal-body").text('Berhasil menghapus daftar jasa');
					$('#msg-box').modal('show');

					loadDaftarJasa();

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
		loadDaftarJasa();
  }

	function onSearch(keyword) {
		paramUrlSetup.keyword = keyword;
		loadDaftarJasa();
	}

	function loadDaftarJasa() {
		getId = null;
		modalState = '';
		$.ajax({
			url     : $('.baseUrl').val() + '/api/daftar-jasa',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { orderby: paramUrlSetup.orderby, column: paramUrlSetup.column, keyword: paramUrlSetup.keyword, branch_id: paramUrlSetup.branchId },
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {
				let listDaftarJasa = '';
				$('#list-daftar-jasa tr').remove();

        if(data.length) {
          $.each(data, function(idx, v) {
            listDaftarJasa += `<tr>`
              + `<td>${++idx}</td>`
              + `<td>${v.service_name}</td>`
              + `<td>${v.category_name}</td>`
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
          listDaftarJasa += `<tr class="text-center"><td colspan="7">Tidak ada data.</td></tr>`;
        }
				$('#list-daftar-jasa').append(listDaftarJasa);

				$('.openFormEdit').click(function() {
					const getObj = data.find(x => x.id == $(this).val());
					modalState = 'edit';

					$('.modal-title').text('Edit Daftar Jasa');
					refreshForm();

					formConfigure();
					getId = getObj.id;
					$('#namaJasa').val(getObj.service_name);
					$('#selectedKategoriJasa').val(getObj.service_category_id); $('#selectedKategoriJasa').trigger('change');
					$('#selectedCabang').val(getObj.branch_id); $('#selectedCabang').trigger('change');
				});
			
				$('.openFormDelete').click(function() {
					getId = $(this).val();
					modalState = 'delete';

					$('#modal-confirmation .modal-title').text('Peringatan');
					$('#modal-confirmation .box-body').text('Anda yakin ingin menghapus Daftar Jasa ini?');
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
		$('#selectedKategoriJasa').select2();
		$('#selectedCabang').select2();
		$('#modal-daftar-jasa').modal('show');
		$('#btnSubmitDaftarJasa').attr('disabled', true);
	}

	function refreshForm() {
		$('#namaJasa').val(null);
		$('#selectedKategoriJasa').val(null);
		$('#selectedCabang').val(null);
		$('#namaJasaErr1').text(''); isValidNamaJasa = true;
		$('#kategoriJasaErr1').text(''); isValidSelectedKategoriJasa = true;
		$('#cabangErr1').text(''); isValidSelectedCabang = true;
		$('#beErr').empty(); isBeErr = false;
	}

	function validationForm() {
		if (!$('#namaJasa').val()) {
			$('#namaJasaErr1').text('Nama jasa harus di isi'); isValidNamaJasa = false;
		} else { 
			$('#namaJasaErr1').text(''); isValidNamaJasa = true;
		}

		if (!$('#selectedKategoriJasa').val()) {
			$('#kategoriJasaErr1').text('Kategori jasa harus di isi'); isValidSelectedKategoriJasa = false;
		} else {
			$('#kategoriJasaErr1').text(''); isValidSelectedKategoriJasa = true;
		}

		if (!$('#selectedCabang').val()) {
			$('#cabangErr1').text('Cabang harus di isi'); isValidSelectedCabang = false;
		} else {
			$('#cabangErr1').text(''); isValidSelectedCabang = true;
		}

		$('#beErr').empty(); isBeErr = false;

		if (!isValidNamaJasa || !isValidSelectedKategoriJasa || !isValidSelectedCabang || isBeErr) {
			$('#btnSubmitDaftarJasa').attr('disabled', true);
		} else {
			$('#btnSubmitDaftarJasa').attr('disabled', false);
		}
	}

	function loadKategoriJasa() {
		$.ajax({
			url     : $('.baseUrl').val() + '/api/kategori-jasa',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {
				optKategoriJasa += `<option value=''>Pilih Kategori Jasa</option>`
	
				if (data.length) {
					for (let i = 0 ; i < data.length ; i++) {
						optKategoriJasa += `<option value=${data[i].id}>${data[i].category_name}</option>`;
					}
				}
				$('#selectedKategoriJasa').append(optKategoriJasa);
			}, complete: function() { $('#loading-screen').hide(); },
			error: function(err) {
				if (err.status == 401) {
					localStorage.removeItem('vet-clinic');
					location.href = $('.baseUrl').val() + '/masuk';
				}
			}
		});
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