$(document).ready(function() {
	let optUserPembeli = '';

	let getId = null;
  let getDate = '';
	let modalState = '';

	let isValidTanggal = false;
	let isValidSelectedUser = false;
	let isValidNamaItem = false;
	let isValidJumlah = false;
	let isValidNominal = false;

	let isBeErr = false;
	let optCabang = '';
	let getCurrentPage = 1;

	let paramUrlSetup = {
		orderby: '',
		column: '',
		keyword: '',
		branchId: ''
	};

	const listFinalPengeluaran = [];

	if (role.toLowerCase() != 'admin') {
		$('.columnAction').hide();
	} else {
		$('.section-left-box-title').append(`<button class="btn btn-info openFormAdd m-r-10px">Tambah</button>`);
		$('.section-right-box-title').append(`<select id="filterCabang" style="width: 50%"></select>`);

		$('#filterCabang').select2({ placeholder: 'Cabang', allowClear: true });

    // load karyawan
    loadKaryawan();

    // load cabang
    loadCabang();
	}

	// load pengeluaran
	loadPengeluaran();

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

		loadPengeluaran();
	});

  
  $('#nominal').mask("#.##0", {reverse: true, maxlength: false});

  $('#tanggal').datepicker({
    autoclose: true,
    clearBtn: true,
    format: 'dd/mm/yyyy',
    todayHighlight: true,
    }).on('changeDate', function(e) {
      getDate = e.format();
      validationForm();
  });
  
  $('#selectedNamaUser').on('select2:select', function (e) {
    validationForm();
  });
	$('#namaItem').keyup(function () { validationForm(); });
  $('#jumlah').keyup(function () { validationForm(); });
	$('#nominal').keyup(function () { validationForm(); });
	
	$('.openFormAdd').click(function() {
		modalState = 'add';
		$('.modal-title').text('Tambah Pengeluaran');
		$('.table-list-final-pengeluaran').show();
		$('.btnSubmitToTableSection').show();
    
		refreshForm(); formConfigure();
	});

	$('#btnSubmitPengeluaran').click(function() {

		if (modalState == 'add') {

			const fd = new FormData();
			fd.append('date_spend', getDate);
			fd.append('user_id_spender', $('#selectedNamaUser').val());

			fd.append('items', JSON.stringify(listFinalPengeluaran));

			$.ajax({
				url : $('.baseUrl').val() + '/api/pengeluaran',
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
						$('#modal-pengeluaran').modal('toggle');
						refreshForm(); loadPengeluaran();
					}, 1000);
				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
					if (err.status === 422) {
						let errText = ''; $('#beErr').empty(); $('#btnSubmitPengeluaran').attr('disabled', true);
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
			$('#modal-confirmation .box-body').text('Anda yakin untuk mengubah data ini ?');
			$('#modal-confirmation').modal('show');
		}
	});

	$('#btnSubmitToTable').click(function() {
		const quantity = parseFloat($('#jumlah').val());
		const amount   = parseFloat($('#nominal').val().replaceAll('.', ''));
		const getAmountOverall = quantity * amount;

		listFinalPengeluaran.push({
			item_name: $('#namaItem').val(),
			quantity: parseFloat($('#jumlah').val()),
			amount: parseFloat($('#nominal').val().replaceAll('.', '')),
			amount_overall: getAmountOverall
		});

		processAppendListFinalPengeluaran();
		console.log('listFinalPengeluaran', listFinalPengeluaran);

		// reset
		$('#namaItem').val(null); $('#jumlah').val(null); $('#nominal').val(null);
		$('#btnSubmitToTable').attr('disabled', true);
		validationForm();
	});

	function processAppendListFinalPengeluaran() {
		let rowListPengeluaran = '';
    let no = 1;
    $('#list-final-pengeluaran tr').remove();

		if (listFinalPengeluaran.length) {
			listFinalPengeluaran.forEach((dt, idx) => {
				rowListPengeluaran += `<tr>`
					+ `<td>${no}</td>`
					+ `<td>${dt.item_name}</td>`
					+ `<td>${typeof(dt.amount) == 'number' ? dt.amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
					+ `<td>${dt.quantity}</td>`
					+ `<td>${typeof(dt.amount_overall) == 'number' ? dt.amount_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
					+ `<td>
							<button type="button" class="btn btn-danger btnRemoveListFinalPengeluaran" value=${idx}>
								<i class="fa fa-trash-o" aria-hidden="true"></i>
							</button>
						</td>`
					+ `</tr>`;
					++no;
			});
		} else {
			rowListPengeluaran += `<tr class="text-center"><td colspan="6">Tidak ada data.</td></tr>`;
		}
    $('#list-final-pengeluaran').append(rowListPengeluaran);

    $('.btnRemoveListFinalPengeluaran').click(function() {
      listFinalPengeluaran.splice($(this).val(), 1);
      validationForm();
      processAppendListFinalPengeluaran();
    });
	}

	$('#submitConfirm').click(function() {
		if (modalState == 'edit') {

			// process edit
			const datas = {
				id: getId,
				date_spend: getDate,
				user_id_spender: $('#selectedNamaUser').val(),
				item_name: $('#namaItem').val(),
				quantity: parseFloat($('#jumlah').val()),
				amount: parseFloat($('#nominal').val().replaceAll('.', '')),
			};

			$.ajax({
				url : $('.baseUrl').val() + '/api/pengeluaran',
				type: 'PUT',
				dataType: 'JSON',
				headers: { 'Authorization': `Bearer ${token}` },
				data: datas,
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(data) {
					$('#modal-confirmation').modal('toggle');

					$("#msg-box .modal-body").text('Berhasil Mengubah Data');
					$('#msg-box').modal('show');

					setTimeout(() => {
						$('#modal-pengeluaran').modal('toggle');
						refreshForm(); loadPengeluaran();
					}, 1000);

				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
          if (err.status === 422) {
            let errText = ''; $('#beErr').empty(); 
            $('#modal-confirmation').modal('toggle');
            $('#btnSubmitPengeluaran').attr('disabled', true);
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
				url     : $('.baseUrl').val() + '/api/pengeluaran',
				headers : { 'Authorization': `Bearer ${token}` },
				type    : 'DELETE',
				data	  : { id: getId },
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(data) {
					$('#modal-confirmation').modal('toggle');

					$("#msg-box .modal-body").text('Berhasil menghapus data');
					$('#msg-box').modal('show');
					loadPengeluaran();

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
		loadPengeluaran();
  }

	function onSearch(keyword) {
		paramUrlSetup.keyword = keyword;
		loadPengeluaran();
	}

	function loadPengeluaran() {
		getId = null;
		modalState = '';
		$.ajax({
			url     : $('.baseUrl').val() + '/api/pengeluaran',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { orderby: paramUrlSetup.orderby, column: paramUrlSetup.column, keyword: paramUrlSetup.keyword, branch_id: paramUrlSetup.branchId },
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(resp) {
				const getData = resp.data;
				let listPengeluaran = '';
				$('#list-pengeluaran tr').remove();

        if (getData.length) {
          $.each(data, function(idx, v) {
            listPengeluaran += `<tr>
							<td>${++idx}</td>
							<td>${v.date}</td>
							<td>${v.item_name}</td>
							<td>${v.total_item}</td>
							<td>${v.unit_name}</td>
							<td>${v.user_name}</td>
							<td>
								<button type="button" class="btn btn-warning openFormEdit" value=${v.id}><i class="fa fa-pencil" aria-hidden="true"></i></button>
								<button type="button" class="btn btn-danger openFormDelete" value=${v.id}><i class="fa fa-trash-o" aria-hidden="true"></i></button>
							</td>
            </tr>`;
          });
        } else {
          listPengeluaran += `<tr class="text-center"><td colspan="7">Tidak ada data.</td></tr>`;
        }
				$('#list-pengeluaran').append(listPengeluaran);

				generatePagination(getCurrentPage, resp.total_paging);

				$('.openFormEdit').click(function() {
					const getObj = data.find(x => x.id == $(this).val());
					modalState = 'edit';
					refreshForm();
					$('.modal-title').text('Edit Pengeluaran');
					$('.table-list-final-pengeluaran').hide();
					$('.btnSubmitToTableSection').hide();

					formConfigure();
					getId = getObj.id;

          getDate = getObj.date_spend;
          const dateArr = getObj.date_spend.split('/');
          $('#tanggal').datepicker('update', new Date(parseFloat(dateArr[2]), parseFloat(dateArr[1])-1, parseFloat(dateArr[0])));

					$('#selectedNamaUser').val(getObj.user_id_spender); $('#selectedNamaUser').trigger('change');
          $('#namaItem').val(getObj.item_name);
          $('#jumlah').val(getObj.quantity);
					$('#nominal').val(getObj.amount);
					// $('#nominal').val(getObj.amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
				});
			
				$('.openFormDelete').click(function() {
					getId = $(this).val();
					modalState = 'delete';
          $('#modal-confirmation .modal-title').text('Peringatan');
					$('#modal-confirmation .box-body').text('Anda yakin ingin menghapus data ini?');
					$('#modal-confirmation').modal('show');
				});

				$('.pagination > li > a').click(function() {
					const getClassName = this.className;
					const getNumber = parseFloat($(this).text());

					if ((getCurrentPage === 1 && getClassName.includes('arrow-left') 
						|| (getCurrentPage === resp.total_paging && getClassName.includes('arrow-right')))) { return; } 

					if (getClassName.includes('arrow-left')) {
						getCurrentPage = getCurrentPage - 1;
					} else if (getClassName.includes('arrow-right')) {
						getCurrentPage = getCurrentPage + 1;
					} else {
						getCurrentPage = getNumber;
					}

					loadPengeluaran();
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

  function loadKaryawan() {
    $.ajax({
      url     : $('.baseUrl').val() + '/api/user',
      headers : { 'Authorization': `Bearer ${token}` },
      type    : 'GET',
      beforeSend: function() { $('#loading-screen').show(); },
      success: function(data) {
        optUserPembeli = `<option value=''>Pilih Nama - Cabang</option>`;
        if (data.length) {
          for (let i = 0 ; i < data.length ; i++) {
            optUserPembeli += `<option value=${data[i].id}>${data[i].fullname} - ${data[i].branch_name}</option>`;
          }
        }
        $('#selectedNamaUser').append(optUserPembeli);
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
				optCabang += `<option value=''>Cabang</option>`

				if (data.length) {
					for (let i = 0 ; i < data.length ; i++) {
						optCabang += `<option value=${data[i].id}>${data[i].branch_name}</option>`;
					}
				}
				$('#filterCabang').append(optCabang);
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
		$('#selectedNamaUser').select2();

		$('#modal-pengeluaran').modal('show');
		$('#btnSubmitToTable').attr('disabled', true);
		$('#btnSubmitPengeluaran').attr('disabled', true);
	}

	function refreshForm() {
    const dateNow = new Date();

    const tanggal = (parseFloat(dateNow.getDate()) < 10) ? ('0' + dateNow.getDate()) : (dateNow.getDate());
    const bulan = ((parseFloat(dateNow.getMonth()) + 1) < 10) ? ('0' + (dateNow.getMonth() + 1)) : ((dateNow.getMonth() + 1));
		getDate = tanggal + '/' + bulan + '/' + dateNow.getFullYear();

    $('#tanggal').datepicker('update', new Date());

		$('#selectedNamaUser').val(null);
    $('#namaItem').val(null);
    $('#jumlah').val(null);
		$('#nominal').val(null);

    $('#tanggalErr1').text(''); isValidTanggal = true;
    $('#namaUserErr1').text(''); isValidSelectedUser = true;
		$('#namaItemErr1').text(''); isValidNamaItem = true;
    $('#jumlahErr1').text(''); isValidJumlah = true;
    $('#nominalErr1').text(''); isValidNominal = true;
    $('#beErr').empty(); isBeErr = false;
	}

	function validationForm() {

		if (!$('#tanggal').datepicker('getDate')) {
			$('#tanggalErr1').text('Tanggal harus di isi'); isValidTanggal = false;
		} else { 
			$('#tanggalErr1').text(''); isValidTanggal = true;
		}

		if (!$('#selectedNamaUser').val()) {
			$('#namaUserErr1').text('Nama User harus di isi'); isValidSelectedUser = false;
		} else { 
			$('#namaUserErr1').text(''); isValidSelectedUser = true;
		}

    if (!$('#namaItem').val()) {
			$('#namaItemErr1').text('Nama item harus di isi'); isValidNamaItem = false;
		} else { 
			$('#namaItemErr1').text(''); isValidNamaItem = true;
		}

    if (!$('#jumlah').val()) {
			$('#jumlahErr1').text('Jumlah harus di isi'); isValidJumlah = false;
		} else { 
			$('#jumlahErr1').text(''); isValidJumlah = true;
		}

    if (!$('#nominal').val()) {
			$('#nominalErr1').text('Nominal harus di isi'); isValidNominal = false;
		} else { 
			$('#nominalErr1').text(''); isValidNominal = true;
		}

		$('#beErr').empty(); isBeErr = false;

		if (modalState === 'add') {
			if (!isValidNamaItem || !isValidJumlah || !isValidNominal ) {
				$('#btnSubmitToTable').attr('disabled', true);
			} else {
				$('#btnSubmitToTable').attr('disabled', false);
			}

			if (!isValidTanggal || !isValidSelectedUser || !listFinalPengeluaran.length || isBeErr) {
				$('#btnSubmitPengeluaran').attr('disabled', true);
			} else {
				$('#btnSubmitPengeluaran').attr('disabled', false);
			}
		} else if (modalState === 'edit') {
			if (!isValidTanggal || !isValidSelectedUser || !isValidNamaItem || !isValidJumlah || !isValidNominal || isBeErr) {
				$('#btnSubmitPengeluaran').attr('disabled', true);
			} else {
				$('#btnSubmitPengeluaran').attr('disabled', false);
			}
		}
		
	}

});
