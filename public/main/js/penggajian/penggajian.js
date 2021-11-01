$(document).ready(function() {
	let optKaryawan = '';
  let optCabang = '';

	let getId = null;
  let getDate = '';
	let modalState = '';

	let isValidTanggal = false;
	let isValidSelectedKaryawan = false;
	let isValidPokok = false;
	let isValidAkomodasi = false;
	let isValidOmset = false;
  let isValidInap = false;
  let isValidOperasi = false;

	let isBeErr = false;
	let paramUrlSetup = {
		orderby: '',
		column: '',
		keyword: '',
		branchId: ''
	};

	if (role.toLowerCase() != 'admin') {
		$('.columnAction').hide(); $('.columnCabang').hide();
    $('.columnNamaUser').hide();
    // $('#filterCabang').hide();
	} else {
		$('.section-left-box-title').append(`<button class="btn btn-info openFormAdd m-r-10px">Tambah</button>`);
		$('.section-right-box-title').append(`<select id="filterCabang" style="width: 50%"></select>`);

		$('#filterCabang').select2({ placeholder: 'Cabang', allowClear: true });

    // load karyawan
    loadKaryawan();

    // load cabang
    loadCabang();
	}

	// load penggajian
	loadPenggajian();

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

		loadPenggajian();
	});

  
  $('#pokok').mask("#.##0", {reverse: true, maxlength: false});
  $('#akomodasi').mask("#.##0", {reverse: true, maxlength: false});
  $('#inputInap').mask("#.##0", {reverse: true, maxlength: false});

  $('#datepicker').datepicker({
    autoclose: true,
    clearBtn: true,
    format: 'dd/mm/yyyy',
    todayHighlight: true,
    }).on('changeDate', function(e) {
      getDate = e.format();
      loadGajiKaryawan($('#selectedNamaKaryawan').val());
      validationForm();
  });
  
  $('#selectedNamaKaryawan').on('select2:select', function (e) {
    const getIdKaryawan = $(this).val();
    loadGajiKaryawan(getIdKaryawan);
    validationForm();
  });

  function loadGajiKaryawan(getIdKaryawan) {
    if (getDate && getIdKaryawan) {
      $.ajax({
        url     : $('.baseUrl').val() + '/api/penggajian/gaji-user',
        headers : { 'Authorization': `Bearer ${token}` },
        type    : 'GET',
        data	  : { id: getIdKaryawan, date: getDate },
        beforeSend: function() { $('#loading-screen').show(); },
        success: function(data) {
          $('#omset-karyawan').attr('value', data.amount_turnover);
          $('#omset-karyawan').text(data.amount_turnover !== null ? data.amount_turnover.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-');

          $('#inap-karyawan').attr('value', data.count_inpatient);
          $('#inap-karyawan').text(data.count_inpatient !== null ? data.count_inpatient.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-');

          $('#operasi-karyawan').attr('value', data.amount_surgery);
          $('#operasi-karyawan').text(data.amount_surgery !== null ? data.amount_surgery.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-');

          overallTotalCalculation();
        }, complete: function() { $('#loading-screen').hide(); },
        error: function(err) {
          if (err.status == 401) {
            localStorage.removeItem('vet-clinic');
            location.href = $('.baseUrl').val() + '/masuk';
          }
        }
      });
    }
  }

  $('#pokok').keyup(function () { validationForm(); overallTotalCalculation(); });
  $('#akomodasi').keyup(function () { validationForm(); overallTotalCalculation(); });
  $('#inputOmset').keyup(function () { validationForm(); overallTotalCalculation(); });
  $('#inputInap').keyup(function () { validationForm(); overallTotalCalculation(); });
  $('#inputOperasi').keyup(function () { validationForm(); overallTotalCalculation(); });
	
	$('.openFormAdd').click(function() {
		modalState = 'add';
		$('.modal-title').text('Tambah Penggajian');
    $('#btnSubmitPenggajian').text('Simpan & Cetak');
    
		refreshForm(); formConfigure();
	});


	$('#btnSubmitPenggajian').click(function() {

		if (modalState == 'add') {

			const fd = new FormData();
			fd.append('user_employee_id', $('#selectedNamaKaryawan').val());
			fd.append('date_payed', getDate);
			fd.append('basic_sallary', $('#pokok').val().replaceAll('.', ''));
			fd.append('accomodation', $('#akomodasi').val().replaceAll('.', ''));

			fd.append('percentage_turnover', $('#inputOmset').val());
      fd.append('amount_turnover', parseFloat($('#omset-karyawan').attr('value')));
      fd.append('total_turnover', parseFloat($('#totalOmset').attr('value')));

      fd.append('amount_inpatient', $('#inputInap').val());
      fd.append('count_inpatient', parseFloat($('#inap-karyawan').attr('value')));
      fd.append('total_inpatient', parseFloat($('#totalInap').attr('value')));

      fd.append('percentage_surgery', $('#inputOperasi').val());
      fd.append('amount_surgery', parseFloat($('#operasi-karyawan').attr('value')));
      fd.append('total_surgery', parseFloat($('#totalOperasi').attr('value')));

      fd.append('total_overall', parseFloat($('#totalKeseluruhan').attr('value')));

			$.ajax({
				url : $('.baseUrl').val() + '/api/penggajian',
				type: 'POST',
				dataType: 'JSON',
				headers: { 'Authorization': `Bearer ${token}` },
				data: fd, contentType: false, cache: false,
				processData: false,
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(resp) {

					$("#msg-box .modal-body").text('Berhasil Menambah Data');
					$('#msg-box').modal('show');

          processPrint(resp.id);

					setTimeout(() => {
						$('#modal-penggajian').modal('toggle');
						refreshForm(); loadPenggajian();
					}, 1000);
				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
					if (err.status === 422) {
						let errText = ''; $('#beErr').empty(); $('#btnSubmitPenggajian').attr('disabled', true);
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
			$('#modal-confirmation .box-body').text('Anda yakin untuk mengubah penggajian ?');
			$('#modal-confirmation').modal('show');
		}
	});

	$('#submitConfirm').click(function() {
		if (modalState == 'edit') {

			// process edit
			const datas = {
				id: getId,
				user_employee_id: $('#selectedNamaKaryawan').val(),
				date_payed: getDate,
				basic_sallary: $('#pokok').val().replaceAll('.', ''),
				accomodation: $('#akomodasi').val().replaceAll('.', ''),
				percentage_turnover: $('#inputOmset').val(),
        amount_turnover: parseFloat($('#omset-karyawan').attr('value')),
        total_turnover: parseFloat($('#totalOmset').attr('value')),
        amount_inpatient: $('#inputInap').val(),
        count_inpatient: parseFloat($('#inap-karyawan').attr('value')),
        total_inpatient: parseFloat($('#totalInap').attr('value')),
        percentage_surgery: $('#inputOperasi').val(),
        amount_surgery: parseFloat($('#operasi-karyawan').attr('value')),
        total_surgery: parseFloat($('#totalOperasi').attr('value')),
        total_overall: parseFloat($('#totalKeseluruhan').attr('value'))
			};

			$.ajax({
				url : $('.baseUrl').val() + '/api/penggajian',
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
						$('#modal-penggajian').modal('toggle');
						refreshForm(); loadPenggajian();
					}, 1000);

				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
          if (err.status === 422) {
            let errText = ''; $('#beErr').empty(); 
            $('#modal-confirmation').modal('toggle');
            $('#btnSubmitPenggajian').attr('disabled', true);
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
				url     : $('.baseUrl').val() + '/api/penggajian',
				headers : { 'Authorization': `Bearer ${token}` },
				type    : 'DELETE',
				data	  : { id: getId },
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(data) {
					$('#modal-confirmation').modal('toggle');

					$("#msg-box .modal-body").text('Berhasil menghapus data');
					$('#msg-box').modal('show');
					loadPenggajian();

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
		loadPenggajian();
  }

	function onSearch(keyword) {
		paramUrlSetup.keyword = keyword;
		loadPenggajian();
	}

	function loadPenggajian() {
		getId = null;
		modalState = '';
		$.ajax({
			url     : $('.baseUrl').val() + '/api/penggajian',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { orderby: paramUrlSetup.orderby, column: paramUrlSetup.column, keyword: paramUrlSetup.keyword, branch_id: paramUrlSetup.branchId },
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {
				let listPenggajian = '';
				$('#list-penggajian tr').remove();

        if (data.length) {
          $.each(data, function(idx, v) {
            listPenggajian += `<tr>
              <td>${++idx}</td>`
              + ((role.toLowerCase() != 'admin') ? `` : `<td>${v.fullname}</td>`)
              + `<td>${v.date_payed}</td>`
              + ((role.toLowerCase() != 'admin') ? `` : `<td>${v.branch_name}</td>`)
              + `<td>Rp ${typeof(v.basic_sallary) == 'number' ? v.basic_sallary.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '' }</td>
              <td>Rp ${typeof(v.accomodation) == 'number' ? v.accomodation.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '' }</td>
              <td>Rp ${typeof(v.total_turnover) == 'number' ? v.total_turnover.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '' }</td>
              <td>Rp ${typeof(v.total_inpatient) == 'number' ? v.total_inpatient.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '' }</td>
              <td>Rp ${typeof(v.total_surgery) == 'number' ? v.total_surgery.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '' }</td>
              <td>Rp ${typeof(v.total_overall) == 'number' ? v.total_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '' }</td>`
              +`<td>
                <button type="button" class="btn btn-info onCetak m-r-3px" value=${v.id}><i class="fa fa-print" aria-hidden="true"></i></button>`
              + ((role.toLowerCase() != 'admin') ?  
                `<button type="button" class="btn btn-info openFormDetail" value=${v.id}><i class="fa fa-eye" aria-hidden="true"></i></button>` 
                :
                `<button type="button" class="btn btn-warning openFormEdit" value=${v.id}><i class="fa fa-pencil" aria-hidden="true"></i></button>
                  <button type="button" class="btn btn-danger openFormDelete" value=${v.id}><i class="fa fa-trash-o" aria-hidden="true"></i></button>`)
              + `</td>
            </tr>`;
          });
        } else {
          listPenggajian += `<tr class="text-center"><td colspan="11">Tidak ada data.</td></tr>`;
        }
				$('#list-penggajian').append(listPenggajian);

				$('.openFormEdit').click(function() {
					const getObj = data.find(x => x.id == $(this).val());
					modalState = 'edit';
					refreshForm();
					$('.modal-title').text('Edit Penggajian');
          $('#btnSubmitPenggajian').text('Simpan');

					formConfigure();
					getId = getObj.id;

          getDate = getObj.date_payed;
          // $('#datepicker').val('21/05/2021');
          const dateArr = getObj.date_payed.split('/');
          $('#datepicker').datepicker('update', new Date(parseFloat(dateArr[2]), parseFloat(dateArr[1])-1, parseFloat(dateArr[0])));
					$('#selectedNamaKaryawan').val(getObj.user_employee_id); $('#selectedNamaKaryawan').trigger('change');
          $('#pokok').val(getObj.basic_sallary.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
          $('#akomodasi').val(getObj.accomodation.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));

          $('#inputOmset').val(getObj.percentage_turnover);
          $('#omset-karyawan').attr('value', getObj.amount_turnover);
          $('#omset-karyawan').text(getObj.amount_turnover !== null ? getObj.amount_turnover.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-');
          $('#totalOmset').attr('value', getObj.total_turnover);
          $('#totalOmset').text('Rp.' + getObj.total_turnover.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));

          $('#inputInap').val(getObj.amount_inpatient.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
          $('#inap-karyawan').attr('value', getObj.count_inpatient);
          $('#inap-karyawan').text(getObj.count_inpatient !== null ? getObj.count_inpatient.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-');
          $('#totalInap').attr('value', getObj.total_inpatient);
          $('#totalInap').text('Rp.' + getObj.total_inpatient.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));

          $('#inputOperasi').val(getObj.percentage_surgery);
          $('#operasi-karyawan').attr('value', getObj.amount_surgery);
          $('#operasi-karyawan').text(getObj.amount_surgery !== null ? getObj.amount_surgery.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-');
          $('#totalOperasi').attr('value', getObj.total_surgery);
          $('#totalOperasi').text('Rp.' + getObj.total_surgery.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));

          overallTotalCalculation();
          // validationForm();
				});
        
        $('.openFormDetail').click(function() {
          const getObj = data.find(x => x.id == $(this).val());
					modalState = 'detail';
          $('#modal-detail-penggajian').modal('show');
          $('.modal-title').text('Detail Penggajian');

          $('#tanggal-txt').text(getObj.date_payed);
          $('#namakaryawan-txt').text(getObj.fullname);
          $('#pokok-txt').text('Rp ' + ((getObj.basic_sallary > -1) ? getObj.basic_sallary.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-'));
          $('#akomodasi-txt').text('Rp ' + ((getObj.accomodation > -1) ? getObj.accomodation.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-'));

          $('#jumlah-omset-txt').text('Rp ' + ((getObj.total_turnover > -1) ? getObj.total_turnover.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-'));
          $('#jumlah-inap-txt').text('Rp ' + ((getObj.total_inpatient > -1) ? getObj.total_inpatient.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-'));
          $('#jumlah-operasi-txt').text('Rp ' + ((getObj.total_inpatient > -1) ? getObj.total_inpatient.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-'));

          $('#total-keseluruhan-txt').text('Rp ' + ((getObj.total_overall > -1) ? getObj.total_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-'));
        });

        $('.onCetak').click(function() {
          processPrint($(this).val());
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

  function loadKaryawan() {
    $.ajax({
      url     : $('.baseUrl').val() + '/api/user',
      headers : { 'Authorization': `Bearer ${token}` },
      type    : 'GET',
      beforeSend: function() { $('#loading-screen').show(); },
      success: function(data) {
        optKaryawan = `<option value=''>Pilih Nama Karyawan - Cabang</option>`;
        if (data.length) {
          for (let i = 0 ; i < data.length ; i++) {
            optKaryawan += `<option value=${data[i].id}>${data[i].fullname} - ${data[i].branch_name}</option>`;
          }
        }
        $('#selectedNamaKaryawan').append(optKaryawan);
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
		$('#selectedNamaKaryawan').select2();

		$('#modal-penggajian').modal('show');
		$('#btnSubmitPenggajian').attr('disabled', true);
	}

	function refreshForm() {
    const dateNow = new Date();
    const tanggal = (parseFloat(dateNow.getDate()) < 10) ? ('0' + dateNow.getDate()) : (dateNow.getDate());
    const bulan = ((parseFloat(dateNow.getMonth()) + 1) < 10) ? ('0' + (dateNow.getMonth() + 1)) : ((dateNow.getMonth() + 1));
		getDate = tanggal + '/' + bulan + '/' + dateNow.getFullYear();

    $('#datepicker').datepicker('update', new Date());
		$('#selectedNamaKaryawan').val(null);
    $('#pokok').val(null);
    $('#akomodasi').val(null);

    $('#inputOmset').val(null); $('#totalOmset').text('Rp -');
    $('#omset-karyawan').attr('value', null);
    $('#omset-karyawan').text('');

    $('#inputInap').val(null); $('#totalInap').text('Rp -');
    $('#inap-karyawan').attr('value', null);
    $('#inap-karyawan').text('');

    $('#inputOperasi').val(null); $('#totalOperasi').text('Rp -');
    $('#operasi-karyawan').attr('value', null);
    $('#operasi-karyawan').text('');

    $('#totalKeseluruhan').text('Rp -');

    $('#namaKaryawanErr1').text(''); isValidSelectedKaryawan = true;
    $('#pokokErr1').text(''); isValidPokok = true;
    $('#akomodasiErr1').text(''); isValidAkomodasi = true;
    $('#omsetErr1').text(''); isValidOmset = true;
    $('#inapErr1').text(''); isValidInap = true;
    $('#operasiErr1').text(''); isValidOperasi = true;
    $('#beErr').empty(); isBeErr = false;
	}

  function overallTotalCalculation() {
    let totalOmset   = 0;
    let totalInap    = 0;
    let totalOperasi = 0;

    // process Omset
    const getOmset = parseFloat($('#inputOmset').val());
    const getOmsetKaryawan = parseFloat($('#omset-karyawan').attr('value'));
    if (getOmset >= 0 && getOmsetKaryawan > -1) {
      totalOmset = Math.round((getOmset / 100) * getOmsetKaryawan);
      $('#totalOmset').attr('value', totalOmset);
      $('#totalOmset').text('Rp ' + totalOmset.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    }

    // process Inap
    const getInap = parseFloat($('#inputInap').val().replaceAll('.', ''));
    const getInapKaryawan = parseFloat($('#inap-karyawan').attr('value'));
    if (getInap >= 0 && getInapKaryawan > -1) {
      totalInap = getInap * getInapKaryawan;
      $('#totalInap').attr('value', totalInap);
      $('#totalInap').text('Rp ' + totalInap.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    }

    // process Operasi
    const getOperasi = parseFloat($('#inputOperasi').val());
    const getOperasiKaryawan = parseFloat($('#operasi-karyawan').attr('value'));
    if (getOperasi >= 0 && getOperasiKaryawan > -1) {
      totalOperasi =  Math.round((getOperasi / 100) * getOperasiKaryawan);
      $('#totalOperasi').attr('value', totalOperasi);
      $('#totalOperasi').text('Rp ' + totalOperasi.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    }

    // process total keseluruhan
    const pokok  = parseFloat($('#pokok').val().replaceAll('.', ''));
    const akomodasi  = parseFloat($('#akomodasi').val().replaceAll('.', ''));
    if (totalOmset > -1 && totalInap > -1 && totalOperasi > -1 && pokok > -1 && akomodasi > -1) {
      const total  = totalOmset + totalInap + totalOperasi + pokok + akomodasi;

      $('#totalKeseluruhan').attr('value', total);
      $('#totalKeseluruhan').text('Rp ' + total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    }

  }

  function processPrint(idPayroll) {
    let url = '/penggajian/generate/' + idPayroll;
    window.open($('.baseUrl').val() + url, '_blank');
  }

	function validationForm() {

		if (!$('#datepicker').datepicker('getDate')) {
			$('#tanggalErr1').text('Tanggal harus di isi'); isValidTanggal = false;
		} else { 
			$('#tanggalErr1').text(''); isValidTanggal = true;
		}

		if (!$('#selectedNamaKaryawan').val()) {
			$('#namaKaryawanErr1').text('Nama karyawan harus di isi'); isValidSelectedKaryawan = false;
		} else { 
			$('#namaKaryawanErr1').text(''); isValidSelectedKaryawan = true;
		}

    if (!$('#pokok').val()) {
			$('#pokokErr1').text('Pokok harus di isi'); isValidPokok = false;
		} else { 
			$('#pokokErr1').text(''); isValidPokok = true;
		}

    if (!$('#akomodasi').val()) {
			$('#akomodasiErr1').text('Akomodasi harus di isi'); isValidAkomodasi = false;
		} else { 
			$('#akomodasiErr1').text(''); isValidAkomodasi = true;
		}

    if (!$('#inputOmset').val()) {
			$('#omsetErr1').text('Omset harus di isi'); isValidOmset = false;
		} else { 
			$('#omsetErr1').text(''); isValidOmset = true;
		}

    if (!$('#inputInap').val()) {
			$('#inapErr1').text('Inap harus di isi'); isValidInap = false;
		} else { 
			$('#inapErr1').text(''); isValidInap = true;
		}

    if (!$('#inputOperasi').val()) {
			$('#operasiErr1').text('Operasi harus di isi'); isValidOperasi = false;
		} else { 
			$('#operasiErr1').text(''); isValidOperasi = true;
		}

		$('#beErr').empty(); isBeErr = false;

    const getTotalKeseluruhan = parseFloat($('#totalKeseluruhan').attr('value'));

		if (!isValidTanggal || !isValidSelectedKaryawan || !isValidPokok || !isValidAkomodasi || !isValidOmset
       || !isValidInap || !isValidOperasi || isBeErr || (getTotalKeseluruhan !== 0 && !getTotalKeseluruhan)) {
			$('#btnSubmitPenggajian').attr('disabled', true);
		} else {
			$('#btnSubmitPenggajian').attr('disabled', false);
		}
	}

});
