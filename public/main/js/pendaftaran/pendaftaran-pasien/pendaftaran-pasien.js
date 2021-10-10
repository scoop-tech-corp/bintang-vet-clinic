$(document).ready(function() {
	let optCabang = '';
  let optPasien = '';
	let optDokter = '';
	let listPasien = [];

	let getId = null;
	let modalState = '';
	let isValidKeluhan = false;
	let isValidNamaPendaftar = false;
	let isValidSelectedPasien = false;
  let isValidSelectedDokter = false;
  let isBeErr = false;
  let paramUrlSetup = {
		orderby:'',
		column: '',
		keyword: '',
		branchId: ''
	};

	if (role.toLowerCase() == 'admin') {
		loadCabang();
		$('#filterCabang').select2({ placeholder: 'Cabang', allowClear: true });
	} else {
		$('#filterCabang').hide();
	}

  loadPendaftaranPasien();

  loadPasien();

	loadDokter();


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

		loadPendaftaranPasien();
  });

  $('.openFormAdd').click(function() {
		modalState = 'add';
		$('.modal-title').text('Tambah Pendaftaran Pasien');
		refreshText(); refreshForm();
		formConfigure();
	});

	$('#btnSubmitPendaftaranPasien').click(function() {
    if (modalState == 'add') {

			const fd = new FormData();
			fd.append('patient_id', $('#selectedPasien').val());
			fd.append('doctor_user_id', $('#selectedDokter').val());
      fd.append('keluhan', $('#keluhan').val());
      fd.append('nama_pendaftar', $('#namaPendaftar').val());

			$.ajax({
				url : $('.baseUrl').val() + '/api/registrasi-pasien',
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
						$('#modal-pendaftaran-pasien').modal('toggle');
						refreshForm();
						loadPendaftaranPasien();
					}, 1000);
				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
					if (err.status === 422) {
						let errText = ''; $('#beErr').empty(); $('#btnSubmitPendaftaranPasien').attr('disabled', true);
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
				patient_id: $('#selectedPasien').val(),
				doctor_user_id: $('#selectedDokter').val(),
				keluhan: $('#keluhan').val(),
				nama_pendaftar: $('#namaPendaftar').val()
			};

			$.ajax({
				url : $('.baseUrl').val() + '/api/registrasi-pasien',
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
						$('#modal-pendaftaran-pasien').modal('toggle');
						refreshForm();
						loadPendaftaranPasien();
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
				url     : $('.baseUrl').val() + '/api/registrasi-pasien',
				headers : { 'Authorization': `Bearer ${token}` },
				type    : 'DELETE',
				data	  : { id: getId },
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(data) {
					$('#modal-confirmation .modal-title').text('Peringatan');
					$('#modal-confirmation').modal('toggle');

					$("#msg-box .modal-body").text('Berhasil menghapus data');
					$('#msg-box').modal('show');

					loadPendaftaranPasien();

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

	$('#selectedPasien').on('select2:select', function (e) {
		const getObj = listPasien.find(x => x.id == $(this).val());
		if (getObj) {
			$('#nomorPasienTxt').text(getObj.id_member); $('#jenisHewanTxt').text(getObj.pet_category);
			$('#namaHewanTxt').text(getObj.pet_name); $('#jenisKelaminTxt').text(getObj.pet_gender);
			$('#usiaHewanTahunTxt').text(`${getObj.pet_year_age} Tahun`); $('#usiaHewanBulanTxt').text(`${getObj.pet_month_age} Bulan`);
			$('#namaPemilikTxt').text(getObj.owner_name); $('#alamatPemilikTxt').text(getObj.owner_address);
			$('#nomorHpPemilikTxt').text(getObj.owner_phone_number);
		} else { refreshText(); }

		validationForm();
	});

	$('#keluhan').keyup(function () { validationForm(); });
	$('#namaPendaftar').keyup(function () { validationForm(); });
	$('#selectedDokter').on('select2:select', function (e) { validationForm(); });

	$('#filterCabang').on('select2:select', function () { onFilterCabang($(this).val()); });
  $('#filterCabang').on("select2:unselect", function () { onFilterCabang($(this).val()); });

	function onSearch(keyword) {
		paramUrlSetup.keyword = keyword;
		loadPendaftaranPasien();
	}

	function onFilterCabang(value) {
    paramUrlSetup.branchId = value;
		loadPendaftaranPasien();
  }

  function loadPendaftaranPasien() {
    getId = null; modalState = '';
		$.ajax({
			url     : $('.baseUrl').val() + '/api/registrasi-pasien',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { orderby: paramUrlSetup.orderby, column: paramUrlSetup.column, keyword: paramUrlSetup.keyword, branch_id: paramUrlSetup.branchId },
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {
				let listPendaftaranPasien = '';
				$('#list-pendaftaran-pasien tr').remove();

        if(data.length) {
          $.each(data, function(idx, v) {
            listPendaftaranPasien += `<tr>`
              + `<td>${++idx}</td>`
              + `<td>${v.id_number}</td>`
              + `<td>${v.created_at}</td>`
              + `<td>${v.id_number_patient}</td>`
              + `<td>${v.pet_name}</td>`
              + `<td>${v.complaint}</td>`
              + `<td>${v.registrant}</td>`
              + `<td>${v.username_doctor}</td>`
              + `<td>${generateBedge(v.acceptance_status)}</td>`
              + `<td>${v.created_by}</td>`
              + `<td>
                  <button type="button" class="btn btn-warning openFormEdit"
                    ${(v.acceptance_status == 1 || v.acceptance_status == 3) && role.toLowerCase() != 'admin'  ? 'disabled' : ''} value=${v.id}><i class="fa fa-pencil" aria-hidden="true"></i></button>
                  <button type="button" class="btn btn-danger openFormDelete"
                    ${(v.acceptance_status == 1 || v.acceptance_status == 3) && role.toLowerCase() != 'admin' ? 'disabled' : ''} value=${v.id}><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                </td>`
              + `</tr>`;
          });
        } else {
          listPendaftaranPasien += `<tr class="text-center"><td colspan="11">Tidak ada data.</td></tr>`;
        }
				$('#list-pendaftaran-pasien').append(listPendaftaranPasien);

				function generateBedge(status) {
					let bedge = '';
					switch (status) {
						case 1:
							bedge = '<span class="label label-success">Diterima</span>';
							break;
						case 2:
							bedge = '<span class="label label-danger">Ditolak</span>';
							break;
						case 3:
							bedge = '<span class="label label-info">Selesai</span>';
							break;
						default:
							bedge = '<span class="label label-warning">Menunggu Konfirmasi</span>';
							break;
					}
					return bedge;
				}

				$('.openFormEdit').click(function() {
					const getObj = data.find(x => x.id == $(this).val());
					if ((getObj.acceptance_status != 1 && getObj.acceptance_status != 3) || role.toLowerCase() == 'admin') {
						modalState = 'edit';

						$('.modal-title').text('Edit Pendaftaran Pasien');
						refreshForm();

						formConfigure();
						getId = getObj.id;
						$('#namaPendaftar').val(getObj.registrant); $('#keluhan').val(getObj.complaint);
						$('#selectedPasien').val(getObj.patient_id); $('#selectedPasien').trigger('change');
						$('#selectedDokter').val(getObj.user_doctor_id); $('#selectedDokter').trigger('change');
						$('#nomorPasienTxt').text(getObj.id_member); $('#jenisHewanTxt').text(getObj.pet_category);
						$('#namaHewanTxt').text(getObj.pet_name); $('#jenisKelaminTxt').text(getObj.pet_gender);
						$('#usiaHewanTahunTxt').text(`${getObj.pet_year_age} Tahun`); $('#usiaHewanBulanTxt').text(`${getObj.pet_month_age} Bulan`);
						$('#namaPemilikTxt').text(getObj.owner_name); $('#alamatPemilikTxt').text(getObj.owner_address);
						$('#nomorHpPemilikTxt').text(getObj.owner_phone_number);
					}
				});

				$('.openFormDelete').click(function() {
					getId = $(this).val();
					const getObj = data.find(x => x.id == getId);
					if ((getObj.acceptance_status != 1 && getObj.acceptance_status != 3) || role.toLowerCase() == 'admin') {
						modalState = 'delete';

						$('#modal-confirmation .modal-title').text('Peringatan');
						$('#modal-confirmation .box-body').text('Anda yakin ingin menghapus data ini?');
						$('#modal-confirmation').modal('show');
					}
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

  function loadPasien() {
    $.ajax({
			url     : $('.baseUrl').val() + '/api/pasien',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {
				optPasien += `<option value=''>Pilih Pasien (nama pasien - nama pemilik - cabang)</option>`
				listPasien = data;
				if (listPasien.length) {
					for (let i = 0 ; i < listPasien.length ; i++) {
						optPasien += `<option value=${data[i].id}>${data[i].pet_name} - ${data[i].owner_name} - ${data[i].branch_name}</option>`;
					}
				}
				$('#selectedPasien').append(optPasien);
			}, complete: function() { $('#loading-screen').hide(); },
			error: function(err) {
				if (err.status == 401) {
					localStorage.removeItem('vet-clinic');
					location.href = $('.baseUrl').val() + '/masuk';
				}
			}
		});
  }

  function loadDokter() {
		$.ajax({
			url     : $('.baseUrl').val() + '/api/user/dokter',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {
				optDokter += `<option value=''>Pilih Dokter</option>`

				if (data.length) {
					for (let i = 0 ; i < data.length ; i++) {
						optDokter += `<option value=${data[i].id}>${data[i].username} - ${data[i].branch_name}</option>`;
					}
				}
				$('#selectedDokter').append(optDokter);
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

  function refreshForm() {
    $('#selectedPasien').val(null); $('#keluhan').val(null);
    $('#namaPendaftar').val(null); $('#selectedDokter').val(null);
    $('#pasienErr1').text(''); isValidSelectedPasien = true;
    $('#keluhanErr1').text(''); isValidKeluhan = true;
    $('#namaPendaftarErr1').text(''); isValidNamaPendaftar = true;
    $('#dokterErr1').text(''); isValidSelectedDokter = true;
    $('#beErr').empty(); isBeErr = false;
	}

	function refreshText() {
		$('#nomorPasienTxt').text('-'); $('#jenisHewanTxt').text('-');
		$('#namaHewanTxt').text('-'); $('#jenisKelaminTxt').text('-');
		$('#usiaHewanTahunTxt').text('- Tahun'); $('#usiaHewanBulanTxt').text('- Bulan');
		$('#namaPemilikTxt').text('-'); $('#alamatPemilikTxt').text('-');
		$('#nomorHpPemilikTxt').text('-');
	}

	function validationForm() {
		if (!$('#selectedPasien').val()) {
			$('#pasienErr1').text('Pasien harus di isi'); isValidSelectedPasien = false;
		} else {
			$('#pasienErr1').text(''); isValidSelectedPasien = true;
		}

		if (!$('#selectedDokter').val()) {
			$('#dokterErr1').text('Dokter harus di isi'); isValidSelectedDokter = false;
		} else {
			$('#dokterErr1').text(''); isValidSelectedDokter = true;
		}

		if (!$('#keluhan').val()) {
			$('#keluhanErr1').text('Keluhan harus di isi'); isValidKeluhan = false;
		} else {
			$('#keluhanErr1').text(''); isValidKeluhan = true;
		}

		if (!$('#namaPendaftar').val()) {
			$('#namaPendaftarErr1').text('Nama Pendaftar harus di isi'); isValidNamaPendaftar = false;
		} else {
			$('#namaPendaftarErr1').text(''); isValidNamaPendaftar = true;
		}

		$('#beErr').empty(); isBeErr = false;

		if (!isValidSelectedPasien || !isValidSelectedDokter || !isValidKeluhan
				|| !isValidNamaPendaftar|| isBeErr) {
			$('#btnSubmitPendaftaranPasien').attr('disabled', true);
		} else {
			$('#btnSubmitPendaftaranPasien').attr('disabled', false);
		}
	}

  function formConfigure() {
    $('#selectedPasien').select2();
		$('#selectedDokter').select2();

		$('#modal-pendaftaran-pasien').modal('show');
		$('#btnSubmitPendaftaranPasien').attr('disabled', true);
  }

});
