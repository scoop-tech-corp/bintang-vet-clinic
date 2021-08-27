$(document).ready(function() {
  let getId = null;
  let modalState = '';
  let optCabang = '';
  let optKategoriJasa = '';
  let optJenisPelayanan = '';

  let isValidSelectedKategoriJasa = false;
  let isValidSelectedCabang = false;
  let isValidSelectedJenisPelayanan = false;

  let isValidHargaJual = false;
  let isValidHargaModal = false;
  let isValidFeeDokter = false;
  let isValidFeePetshop = false;
  let customErr1 = false;
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
    $('#selectedCabang').append(`<option value=''>Pilih Cabang</option>`);
    $('#selectedKategoriJasa').append(`<option value=''>Pilih Kategori Jasa</option>`);
    $('#selectedJenisPelayanan').append(`<option value=''>Pilih Jenis Pelayanan</option>`);

    $('.section-left-box-title').append(`<button class="btn btn-info openFormAdd m-r-10px">Tambah</button>`);
		$('.section-right-box-title').append(`<select id="filterCabang" style="width: 50%"></select>`);

    $('#filterCabang').select2({ placeholder: 'Cabang', allowClear: true });
    $('#filterCabang').append(`<option value=''>Cabang</option>`);
    loadCabang();
  }

  loadHargaJasa();

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

		loadHargaJasa();
  });

  $('.openFormAdd').click(function() {
		modalState = 'add';
    $('.modal-title').text('Tambah Pembagian Harga Jasa');
    $('#selectedKategoriJasa').attr('disabled', true);
    $('#selectedJenisPelayanan').attr('disabled', true);

    refreshForm();
    formConfigure();
  });
  
  $('#btnSubmitHargaJasa').click(function() {
    if (modalState == 'add') {

			const fd = new FormData();
			fd.append('ListOfServiceId', $('#selectedJenisPelayanan').val());
			fd.append('HargaJual', $('#hargaJual').val().replaceAll('.', ''));
      fd.append('HargaModal', $('#hargaModal').val().replaceAll('.', ''));
      fd.append('FeeDokter', $('#feeDokter').val().replaceAll('.', ''));
      fd.append('FeePetShop', $('#feePetshop').val().replaceAll('.', ''));

			$.ajax({
				url : $('.baseUrl').val() + '/api/pembagian-harga-jasa',
				type: 'POST',
				dataType: 'JSON',
				headers: { 'Authorization': `Bearer ${token}` },
				data: fd, contentType: false, cache: false,
				processData: false,
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(resp) {

					$("#msg-box .modal-body").text('Berhasil Menambah Pembagian Harga Jasa');
					$('#msg-box').modal('show');

					setTimeout(() => {
						$('#modal-harga-jasa').modal('toggle');
						refreshForm();
						loadHargaJasa();
					}, 1000);
				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
					if (err.status === 422) {
						let errText = ''; $('#beErr').empty(); $('#btnSubmitHargaJasa').attr('disabled', true);
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
        ListOfServiceId: $('#selectedJenisPelayanan').val(),
        HargaJual: $('#hargaJual').val().replaceAll('.', ''),
        HargaModal: $('#hargaModal').val().replaceAll('.', ''),
        FeeDokter: $('#feeDokter').val().replaceAll('.', ''),
        FeePetShop: $('#feePetshop').val().replaceAll('.', '')
      };

      $.ajax({
        url : $('.baseUrl').val() + '/api/pembagian-harga-jasa',
        type: 'PUT',
        dataType: 'JSON',
        headers: { 'Authorization': `Bearer ${token}` },
        data: datas,
        beforeSend: function() { $('#loading-screen').show(); },
        success: function(data) {
          $('#modal-confirmation .modal-title').text('Peringatan');
          $('#modal-confirmation').modal('toggle');

          $("#msg-box .modal-body").text('Berhasil Mengubah harga jasa');
          $('#msg-box').modal('show');

          setTimeout(() => {
            $('#modal-harga-jasa').modal('toggle');
            refreshForm();
            loadHargaJasa();
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
        url     : $('.baseUrl').val() + '/api/pembagian-harga-jasa',
        headers : { 'Authorization': `Bearer ${token}` },
        type    : 'DELETE',
        data	  : { id: getId },
        beforeSend: function() { $('#loading-screen').show(); },
        success: function(data) {
          $('#modal-confirmation .modal-title').text('Peringatan');
          $('#modal-confirmation').modal('toggle');

          $("#msg-box .modal-body").text('Berhasil menghapus data');
          $('#msg-box').modal('show');

          loadHargaJasa();

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

  $('#selectedCabang').on('select2:select', function (e) {
    if ($(this).val()) {
      loadKategoriJasa($(this).val());
    } else {
      optKategoriJasa = `<option value=''>Pilih Kategori Jasa</option>`;
      $('#selectedKategoriJasa option').remove();
      $('#selectedKategoriJasa').val(null);
      $('#selectedKategoriJasa').append(optKategoriJasa);
      $('#selectedKategoriJasa').attr('disabled', true);
    }

    optJenisPelayanan = `<option value=''>Pilih Jenis Pelayanan</option>`;
    $('#selectedJenisPelayanan').attr('disabled', true);
    $('#selectedJenisPelayanan option').remove();
    $('#selectedJenisPelayanan').val(null);
    $('#selectedJenisPelayanan').append(optJenisPelayanan);

    validationHargaJual(); validationForm();
  });

  $('#selectedKategoriJasa').on('select2:select', function (e) {
    if ($(this).val()) {
      loadJenisPelayanan($('#selectedCabang').val(), $(this).val());
    } else {
      optJenisPelayanan = `<option value=''>Pilih Jenis Pelayanan</option>`;
      $('#selectedJenisPelayanan').attr('disabled', true);
      $('#selectedJenisPelayanan option').remove();
      $('#selectedJenisPelayanan').val(null);
      $('#selectedJenisPelayanan').append(optJenisPelayanan);
    }

    validationHargaJual(); validationForm();
  });

  $('#selectedJenisPelayanan').on('select2:select', function (e) { validationHargaJual(); validationForm(); });

  $('#hargaJual').mask("#.##0", {reverse: true, maxlength: false});
  $('#hargaModal').mask("#.##0", {reverse: true, maxlength: false});
  $('#feeDokter').mask("#.##0", {reverse: true, maxlength: false});
  $('#feePetshop').mask("#.##0", {reverse: true, maxlength: false});

  $('#hargaJual').keyup(function () { validationHargaJual(); validationForm(); });
  $('#hargaModal').keyup(function () { validationHargaJual(); validationForm(); });
  $('#feeDokter').keyup(function () { validationHargaJual(); validationForm(); });
  $('#feePetshop').keyup(function () { validationHargaJual(); validationForm(); });

  $('#filterCabang').on('select2:select', function () { onFilterCabang($(this).val()); });
  $('#filterCabang').on("select2:unselect", function () { onFilterCabang($(this).val()); });

  function onFilterCabang(value) {
    paramUrlSetup.branchId = value;
		loadHargaJasa();
  }

  function onSearch(keyword) {
		paramUrlSetup.keyword = keyword;
		loadHargaJasa();
	}

  function loadHargaJasa() {

    getId = null; modalState = '';
    $.ajax({
			url     : $('.baseUrl').val() + '/api/pembagian-harga-jasa',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { orderby: paramUrlSetup.orderby, column: paramUrlSetup.column, keyword: paramUrlSetup.keyword, branch_id: paramUrlSetup.branchId },
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {
				let listHargaJasa = '';
				$('#list-harga-jasa tr').remove();

        if (data.length) {
          $.each(data, function(idx, v) {
            listHargaJasa += `<tr>`
              + `<td>${++idx}</td>`
              + `<td>${v.service_name}</td>`
              + `<td>${v.category_name}</td>`
              + `<td>${v.branch_name}</td>`
              + `<td>Rp ${typeof(v.selling_price) == 'number' ? v.selling_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
              + `<td>Rp ${typeof(v.capital_price) == 'number' ? v.capital_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
              + `<td>Rp ${typeof(v.doctor_fee) == 'number' ? v.doctor_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
              + `<td>Rp ${typeof(v.petshop_fee) == 'number' ? v.petshop_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
              + `<td>${v.created_by}</td>`
              + `<td>${v.created_at}</td>`
              + ((role.toLowerCase() != 'admin') ? `` : `<td>
                  <button type="button" class="btn btn-warning openFormEdit" value=${v.id}><i class="fa fa-pencil" aria-hidden="true"></i></button>
                  <button type="button" class="btn btn-danger openFormDelete" value=${v.id}><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                </td>`)
              + `</tr>`;
          });
        } else {
          listHargaJasa += `<tr class="text-center"><td colspan="11">Tidak ada data.</td></tr>`;
        }
				$('#list-harga-jasa').append(listHargaJasa);

				$('.openFormEdit').click(function() {
					const getObj = data.find(x => x.id == $(this).val());
					modalState = 'edit';

					$('.modal-title').text('Edit Pembagian Harga Jasa');
          refreshForm();

          loadKategoriJasa(getObj.branch_id, getObj.service_categories_id);
          loadJenisPelayanan(getObj.branch_id, getObj.service_categories_id, getObj.list_of_service_id);
          formConfigure();

					getId = getObj.id;
          $('#hargaJual').val(getObj.selling_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
          $('#hargaModal').val(getObj.capital_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
          $('#feeDokter').val(getObj.doctor_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
          $('#feePetshop').val(getObj.petshop_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
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

  function loadCabang() {
		$.ajax({
			url     : $('.baseUrl').val() + '/api/cabang',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {	
				if (data.length) {
					for (let i = 0 ; i < data.length ; i++) {
						optCabang += `<option value=${data[i].id}>${data[i].branch_name}</option>`;
					}
				}
        $('#selectedCabang').append(optCabang); $('#filterCabang').append(optCabang);
			}, complete: function() { $('#loading-screen').hide(); },
			error: function(err) {
				if (err.status == 401) {
					localStorage.removeItem('vet-clinic');
					location.href = $('.baseUrl').val() + '/masuk';
				}
			}
		});
  }

  function loadKategoriJasa(idCabang, serviceCategoryId = null) {
    $.ajax({
			url     : $('.baseUrl').val() + '/api/pembagian-harga-jasa/kategori-jasa',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { branch_id: idCabang },
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {
        optKategoriJasa = `<option value=''>Pilih Kategori Jasa</option>`;
        $('#selectedKategoriJasa option').remove();

        if (data.length) {
          $('#selectedKategoriJasa').attr('disabled', false);
					for (let i = 0 ; i < data.length ; i++) {
						optKategoriJasa += `<option value=${data[i].service_category_id}>${data[i].category_name}</option>`;
					}
				} else {
          $('#selectedKategoriJasa').attr('disabled', true);
          optKategoriJasa = `<option value='' selected="selected">Data tidak ada</option>`;
        }
        $('#selectedKategoriJasa').append(optKategoriJasa);

        if (modalState == 'edit') {
          $('#selectedKategoriJasa').val(serviceCategoryId); $('#selectedKategoriJasa').trigger('change');
        }

			}, complete: function() { $('#loading-screen').hide(); },
			error: function(err) {
				if (err.status == 401) {
					localStorage.removeItem('vet-clinic');
					location.href = $('.baseUrl').val() + '/masuk';
				}
			}
		});
  }

  function loadJenisPelayanan(idCabang, kategoriJasaId, serviceNameId = null) {
    $.ajax({
			url     : $('.baseUrl').val() + '/api/pembagian-harga-jasa/nama-jasa',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { branch_id: idCabang, service_category_id: kategoriJasaId},
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {
        optJenisPelayanan = `<option value=''>Pilih Jenis Pelayanan</option>`;
        $('#selectedJenisPelayanan option').remove();

        if (data.length) {
          $('#selectedJenisPelayanan').attr('disabled', false);
					for (let i = 0 ; i < data.length ; i++) {
						optJenisPelayanan += `<option value=${data[i].id}>${data[i].service_name}</option>`;
					}
				} else {
          $('#selectedJenisPelayanan').attr('disabled', true);
          optJenisPelayanan = `<option value='' selected="selected">Data tidak ada</option>`;
        }
        $('#selectedJenisPelayanan').append(optJenisPelayanan);

        if (modalState == 'edit') {
          $('#selectedJenisPelayanan').val(serviceNameId); $('#selectedJenisPelayanan').trigger('change');
        }
			}, complete: function() { $('#loading-screen').hide(); },
			error: function(err) {
				if (err.status == 401) {
					localStorage.removeItem('vet-clinic');
					location.href = $('.baseUrl').val() + '/masuk';
				}
			}
		});
  }
  
  function validationForm() {
		if (!$('#selectedCabang').val()) {
			$('#cabangErr1').text('Cabang jasa harus di isi'); isValidSelectedCabang = false;
		} else { 
			$('#cabangErr1').text(''); isValidSelectedCabang = true;
		}

		if (!$('#selectedKategoriJasa').val()) {
			$('#kategoriJasaErr1').text('Kategori jasa harus di isi'); isValidSelectedKategoriJasa = false;
		} else {
			$('#kategoriJasaErr1').text(''); isValidSelectedKategoriJasa = true;
		}

		if (!$('#selectedJenisPelayanan').val()) {
			$('#jenisPelayananErr1').text('Jenis pelayanan harus di isi'); isValidSelectedJenisPelayanan = false;
		} else {
			$('#jenisPelayananErr1').text(''); isValidSelectedJenisPelayanan = true;
    }

    if ($('#hargaJual').val() == '') {
			$('#hargaJualErr1').text('Harga jual harus di isi'); isValidHargaJual = false;
		} else {
			$('#hargaJualErr1').text(''); isValidHargaJual = true;
    }

    if ($('#hargaModal').val() == '') {
			$('#hargaModalErr1').text('Harga modal harus di isi'); isValidHargaModal = false;
		} else {
			$('#hargaModalErr1').text(''); isValidHargaModal = true;
    }

    if ($('#feeDokter').val() == '') {
			$('#feeDokterErr1').text('Fee dokter harus di isi'); isValidFeeDokter = false;
		} else {
			$('#feeDokterErr1').text(''); isValidFeeDokter = true;
    }

    if ($('#feePetshop').val() == '') {
			$('#feePetshopErr1').text('Fee petshop harus di isi'); isValidFeePetshop = false;
		} else {
			$('#feePetshopErr1').text(''); isValidFeePetshop = true;
    }
    
    $('#beErr').empty(); isBeErr = false;
    validationBtnSubmitHargaJasa();
	}

  function refreshForm() {
		$('#selectedCabang').val(null);
		$('#selectedKategoriJasa').val(null);
    $('#selectedJenisPelayanan').val(null);

    $('#hargaJual').val(null); $('#hargaModal').val(null);
    $('#feeDokter').val(null); $('#feePetshop').val(null);
    
    $('#customErr1').empty(); customErr1 = false;
    $('#beErr').empty(); isBeErr = false;

    $('#cabangErr1').text(''); isValidSelectedCabang = true;
    $('#kategoriJasaErr1').text(''); isValidSelectedKategoriJasa = true;
    $('#jenisPelayananErr1').text(''); isValidSelectedJenisPelayanan = true;
    $('#hargaJualErr1').text(''); isValidHargaJual = true;
    $('#hargaModalErr1').text(''); isValidHargaModal = true;
    $('#feeDokterErr1').text(''); isValidFeeDokter = true;
    $('#feePetshopErr1').text(''); isValidFeePetshop = true;
  }
  
  function formConfigure() {
    $('#selectedCabang').select2();
		$('#selectedKategoriJasa').select2();
    $('#selectedJenisPelayanan').select2();

		$('#modal-harga-jasa').modal('show');
		$('#btnSubmitHargaJasa').attr('disabled', true);
  }

  function validationHargaJual() {
    let hargaJual  = $('#hargaJual').val();
    let hargaModal = $('#hargaModal').val(); 
    let feeDokter  = $('#feeDokter').val();
    let feePetshop = $('#feePetshop').val();

    hargaJual  = hargaJual.replaceAll('.', '');
    hargaModal = hargaModal.replaceAll('.', '');
    feeDokter  = feeDokter.replaceAll('.', '');
    feePetshop = feePetshop.replaceAll('.', '');

    const totalHargaJual = parseInt(hargaModal) + parseInt(feeDokter) + parseInt(feePetshop);

    if (parseInt(hargaJual) !== totalHargaJual) {
      $('#customErr1').text('Total harga modal, fee dokter, dan fee petshop tidak sama dengan harga jual'); 
      customErr1 = false;
		} else { 
			$('#customErr1').text(''); customErr1 = true;
		}
  }
  
  function validationBtnSubmitHargaJasa() {
    if (!isValidSelectedCabang || !isValidSelectedKategoriJasa || !isValidSelectedJenisPelayanan 
      || !isValidHargaJual || !isValidHargaModal || !isValidFeeDokter || !isValidFeePetshop 
      || !customErr1 || isBeErr) {
			$('#btnSubmitHargaJasa').attr('disabled', true);
		} else {
			$('#btnSubmitHargaJasa').attr('disabled', false);
		}
  }

});
