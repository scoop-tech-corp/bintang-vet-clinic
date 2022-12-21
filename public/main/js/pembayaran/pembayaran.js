$(document).ready(function() {

  let listBarang = [];
  let listSelectedBarang = [];
	let optCabang = '';
	let getCurrentPage = 1;
  let optMetodePembayaran = '';
  let isValidMetodePembayaran = false;

  let paramUrlSetup = {
		orderby:'',
		column: '',
    keyword: '',
    branchId: ''
  };
  let getId = null;

	// if (role.toLowerCase() == 'dokter') {
	// 	window.location.href = $('.baseUrl').val() + `/unauthorized`;
	// } else {
		// if (role.toLowerCase() != 'admin') {
    //   $('#filterCabang').hide();
    //   $('#filterCabangPet').hide();
    // } else {
      loadCabang();
      $('#filterCabang').select2({ placeholder: 'Cabang', allowClear: true });
      $('#filterCabangPet').select2({ placeholder: 'Cabang', allowClear: true });
   //}
	//}

  loadMetodePembayaran();
  loadPembayaran();
  
  loadPembayaranPetshop();

  $('#selectedMetodePembayaran').on('select2:select', function (e) {
    validationForm();
  });

  $('.input-search-section .fa').click(function() {
		onSearch($('.input-search-section input').val());
	});

  $('.input-search-section-petshop .fa').click(function() {
		onSearchPetShop($('.input-search-section-petshop input').val());
	});

	$('.input-search-section input').keypress(function(e) {
		if (e.which == 13) { onSearch($(this).val()); }
	});

  $('.input-search-section-petshop input').keypress(function(e) {
		if (e.which == 13) { onSearchPetShop($(this).val()); }
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

		loadPembayaran();
	});

  $('#filterCabang').on('select2:select', function () { onFilterCabang($(this).val()); });
  $('#filterCabang').on("select2:unselect", function () { onFilterCabang($(this).val()); });
  
	$('#filterCabangPet').on('select2:select', function () { onFilterCabangPetShop($(this).val()); });
  $('#filterCabangPet').on("select2:unselect", function () { onFilterCabangPetShop($(this).val()); });

  $('.openFormAdd').click(function() {
    window.location.href = $('.baseUrl').val() + '/pembayaran/tambah';
  });

	$('.openFormAddPetShop').click(function() {
    modalState = 'add';
    $('.modal-title').text('Tambah Pembayaran');

    if (role.toLowerCase() == 'kasir') {
      loadBarang(branchId);
    }

    refreshForm(); formConfigure();
  });

  $('#submitConfirm').click(function() {
    // process delete
			$.ajax({
				url     : $('.baseUrl').val() + '/api/pembayaran',
				headers : { 'Authorization': `Bearer ${token}` },
				type    : 'DELETE',
				data	  : { list_of_payment_id: getId },
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(data) {
					$('#modal-confirmation .modal-title').text('Peringatan');
					$('#modal-confirmation').modal('toggle');

					$("#msg-box .modal-body").text('Berhasil menghapus data');
					$('#msg-box').modal('show');

					loadPembayaran();

				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
					if (err.status == 401) {
						localStorage.removeItem('vet-clinic');
						location.href = $('.baseUrl').val() + '/masuk';
					}
				}
			});
  });

  $('#submitConfirmPetShop').click(function() {
    // process delete
			$.ajax({
				url     : $('.baseUrl').val() + '/api/pembayaranpetshop',
				headers : { 'Authorization': `Bearer ${token}` },
				type    : 'DELETE',
				data	  : { id: getId },
				beforeSend: function() { $('#loading-screen').show(); },
				success: function(data) {
					$('#modal-confirmation-pet-shop .modal-title').text('Peringatan');
					$('#modal-confirmation-pet-shop').modal('toggle');

					$("#msg-box .modal-body").text('Berhasil menghapus data');
					$('#msg-box').modal('show');

					loadPembayaranPetshop();

				}, complete: function() { $('#loading-screen').hide(); }
				, error: function(err) {
					if (err.status == 401) {
						localStorage.removeItem('vet-clinic');
						location.href = $('.baseUrl').val() + '/masuk';
					}
				}
			});
  });

  function onFilterCabang(value) {
    paramUrlSetup.branchId = value;
		loadPembayaran();
  }

  function onFilterCabangPetShop(value) {
    paramUrlSetup.branchId = value;
		loadPembayaranPetshop();
  }

  function onSearch(keyword) {
		paramUrlSetup.keyword = keyword;
		loadPembayaran();
	}

  function onSearchPetShop(keyword) {
		paramUrlSetup.keyword = keyword;
		loadPembayaranPetshop();
	}

  function loadPembayaran() {

    $.ajax({
			url     : $('.baseUrl').val() + '/api/pembayaran',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { orderby: paramUrlSetup.orderby, column: paramUrlSetup.column, keyword: paramUrlSetup.keyword, branch_id: paramUrlSetup.branchId, page: getCurrentPage },
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(resp) {
				const getData = resp.data;

				let listPembayaran = '';
				$('#list-pembayaran tr').remove();

        if (getData.length) {
          $.each(getData, function(idx, v) {
            listPembayaran += `<tr>`
              + `<td>${++idx}</td>`
              + `<td>${v.registration_number}</td>`
              + `<td>${v.created_at}</td>`
              + `<td>${v.patient_number}</td>`
              + `<td>${v.pet_category}</td>`
              + `<td>${v.pet_name}</td>`
              + `<td>${v.complaint}</td>`
              + `<td>${(v.status_outpatient_inpatient == 1) ? 'Rawat Inap' : 'Rawat Jalan'}</td>`
              + `<td>${v.created_by}</td>`
              + `<td>
                  <button type="button" class="btn btn-info openDetail" value=${v.list_of_payment_id} title="Detail"><i class="fa fa-eye" aria-hidden="true"></i></button>
                  <button type="button" class="btn btn-warning openFormEdit" value=${v.list_of_payment_id}><i class="fa fa-pencil" aria-hidden="true"></i></button>
                  <button type="button" class="btn btn-danger openFormDelete"
                    ${role.toLowerCase() != 'admin' ? 'disabled' : ''} value=${v.list_of_payment_id}><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                </td>`
              + `</tr>`;
          });
        } else {
          listPembayaran += `<tr class="text-center"><td colspan="10">Tidak ada data.</td></tr>`;
        }

				$('#list-pembayaran').append(listPembayaran);

				generatePagination(getCurrentPage, resp.total_paging);

        $('.openDetail').click(function() {
					window.location.href = $('.baseUrl').val() + `/pembayaran/detail/${$(this).val()}`;
        });

				$('.openFormEdit').click(function() {
					window.location.href = $('.baseUrl').val() + `/pembayaran/edit/${$(this).val()}`;
				});

				$('.openFormDelete').click(function() {
          getId = $(this).val();

					if (role.toLowerCase() == 'admin') {
            $('#modal-confirmation .modal-title').text('Peringatan');
						$('#modal-confirmation .box-body').text('Anda yakin ingin menghapus data ini?');
						$('#modal-confirmation').modal('show');
          }
				});

        $('.openFormDeletePetShop').click(function() {
          getId = $(this).val();
          
					if (role.toLowerCase() == 'admin') {
            $('#modal-confirmation-pet-shop .modal-title').text('Peringatan');
						$('#modal-confirmation-pet-shop .box-body').text('Anda yakin ingin menghapus data ini?');
						$('#modal-confirmation-pet-shop').modal('show');
          }
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

					loadPembayaran()
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

  function loadPembayaranPetshop(){
    $.ajax({
			url     : $('.baseUrl').val() + '/api/pembayaranpetshop',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { orderby: paramUrlSetup.orderby, column: paramUrlSetup.column, keyword: paramUrlSetup.keyword, branch_id: paramUrlSetup.branchId, page: getCurrentPage },
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(resp) {
				const getData = resp.data;

				let listPembayaranPetShop = '';
				$('#list-pembayaran-petshop tr').remove();

        if (getData.length) {
          $.each(getData, function(idx, v) {
            listPembayaranPetShop += `<tr>`
              + `<td>${++idx}</td>`
              + `<td>${v.created_at}</td>`
              + `<td>${v.branch_name}</td>`
              + `<td>${v.payment_number}</td>`
              + `<td>${v.item_name}</td>`
              + `<td>${v.total_item}</td>`
              + `<td>Rp ${
                typeof v.each_price == "number"
                  ? v.each_price
                      .toString()
                      .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                  : ""
              }</td>`
              + `<td>Rp ${
                typeof v.overall_price == "number"
                  ? v.overall_price
                      .toString()
                      .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                  : ""
              }</td>`
              + `<td>${v.created_by}</td>`
              + `<td>
                  <button type="button" class="btn btn-danger openFormDeletePetShop"
                    ${role.toLowerCase() != 'admin' ? 'disabled' : ''} value=${v.id}><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                </td>`
              + `</tr>`;
          });
        } else {
          listPembayaranPetShop += `<tr class="text-center"><td colspan="10">Tidak ada data.</td></tr>`;
        }

				$('#list-pembayaran-petshop').append(listPembayaranPetShop);

				generatePagination(getCurrentPage, resp.total_paging);

				$('.openFormDeletePetShop').click(function() {
          getId = $(this).val();

					if (role.toLowerCase() == 'admin') {
            $('#modal-confirmation-pet-shop .modal-title').text('Peringatan');
						$('#modal-confirmation-pet-shop .box-body').text('Anda yakin ingin menghapus data ini?');
						$('#modal-confirmation-pet-shop').modal('show');
          }
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

					loadPembayaranPetshop()
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

  function loadMetodePembayaran(){
    $.ajax({
      url     : $('.baseUrl').val() + '/api/metode-pembayaran',
      headers : { 'Authorization': `Bearer ${token}` },
      type    : 'GET',
      beforeSend: function() { $('#loading-screen').show(); },
      success: function(data) {
        const getData = data;
        optMetodePembayaran += `<option value=""></option>`;

				if (getData.length) {
					for (let i = 0 ; i < getData.length ; i++) {
						optMetodePembayaran += `<option value=${getData[i].id}>${getData[i].payment_name}</option>`;
					}
				}
				$('#selectedMetodePembayaran').append(optMetodePembayaran);

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
				$('#filterCabangPet').append(optCabang);
				$('#selectedCabang').append(optCabang);
			}, complete: function() { $('#loading-screen').hide(); },
			error: function(err) {
				if (err.status == 401) {
					localStorage.removeItem('vet-clinic');
					location.href = $('.baseUrl').val() + '/masuk';
				}
			}
		});
	}

	function loadBarang(getCabangId) {
    optBarang = `<option value=''>Pilih Barang</option>`;

    $.ajax({
      url: $('.baseUrl').val() + '/api/pembayaranpetshop/filteritem',
      headers: { 'Authorization': `Bearer ${token}` },
      type: 'GET',
      data: { branch_id: getCabangId },
      beforeSend: function () { $('#loading-screen').show(); },
      success: function (data) {
        $('#selectedBarang option').remove();
  
        if (data.length) {
          for (let i = 0 ; i < data.length ; i++) {
            optBarang += `<option value=${data[i].id}>${data[i].item_name}</option>`;
            listBarang.push(data[i]);
          }
        }
        $('#selectedCabang').prop('disabled', true);
        $('.showDropdownBarang').show();
        $('#selectedBarang').append(optBarang);

        validationForm();
      }, complete: function() { $('#loading-screen').hide(); },
      error: function(err) {
        if (err.status == 401) {
          localStorage.removeItem('vet-shop');
          location.href = $('.baseUrl').val() + '/masuk';
        }
      }
    });
  }

  function validationForm() {
    if (!$('#selectedCabang').val() && role.toLowerCase() == 'admin') {
      $('#cabangErr1').text('Cabang harus di isi'); isValidSelectedCabang = false;
    } else {
      $('#cabangErr1').text(''); isValidSelectedCabang = true;
    }

    if (!$('#selectedMetodePembayaran').val()) {
      $('#metodePembayaranErr1').text('Metode pembayaran harus di isi'); isValidMetodePembayaran = false;
    } else {
      $('#metodePembayaranErr1').text(''); isValidMetodePembayaran = true;
    }

    if (!listSelectedBarang.length) {
      $('#barangErr1').text('Barang harus di pilih'); isValidListSelectedBarang = false;
    } else {
      $('#barangErr1').text(''); isValidListSelectedBarang = true;
    }

    $('#beErr').empty(); isBeErr = false;

    $('#btnSubmitPembayaran').attr('disabled', (!isValidSelectedCabang || !isValidMetodePembayaran || !isValidListSelectedBarang || isBeErr) ? true : false);
  }

	function refreshForm() {
		$('#selectedCabang').val(null);
    $('#selectedBarang').val(null);
    listSelectedBarang = [];
    drawTableSelectedBarang();

    $('#selectedMetodePembayaran').val(null);
    $('#metodePembayaranErr1').text(''); isValidMetodePembayaran = true;

    $('#selectedCabang').prop('disabled', false);

    if(role.toLowerCase() == 'admin') {
      $('.showDropdownBarang').hide();
    }

    $('#beErr').empty(); isBeErr = false;

    $('#cabangErr1').text(''); isValidSelectedCabang = true;
    $('#barangErr1').text(''); isValidListSelectedBarang = true;
  }

	function formConfigure() {
    $('#selectedCabang').select2();
    $('#selectedBarang').select2();

    $('#selectedMetodePembayaran').select2({placeholder: 'Pilih Metode Pembayaran'});
		$('#modal-tambah-pembayaran').modal('show');
		$('#btnSubmitPembayaran').attr('disabled', true);
  }

	function drawTableSelectedBarang() {
    let listSelectedBarangTxt = '';
    $('#list-selected-barang tr').remove();

    if (listSelectedBarang.length) {
      listSelectedBarang.forEach((barang, idx) => {
        listSelectedBarangTxt += `<tr>`
          + `<td>${idx + 1}</td>`
          + `<td>${barang.item_name}</td>`
          + `<td><input type="number" min="0" class="qty-input-barang" index=${idx} value=${barang.total_item}></td>`
          + `<td>Rp ${barang.selling_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')}</td>`
          + `<td>Rp <span id="overallPrice-${idx}">
              ${typeof(barang.price_overall) == 'number' ?
                barang.price_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
                : ''}</span>
            </td>`
          +`<td>
              <button type="button" class="btn btn-danger btnDeleteSelectedBarang" value=${idx}>
                <i class="fa fa-trash-o" aria-hidden="true"></i>
              </button>
            </td>`
          + `</tr>`;
      });
    } else {
      listSelectedBarangTxt += `<tr class="text-center"><td colspan="7">Tidak ada data.</td></tr>`;
    }

    $('#list-selected-barang').append(listSelectedBarangTxt);

    $('.qty-input-barang').on('input', function(e) {
      const idx        = $(this).attr('index');
      const value      = parseFloat($(this).val());
      const eachItem   = parseFloat(listSelectedBarang[idx].selling_price);
      let overallPrice = value * eachItem;

      listSelectedBarang[idx].total_item = value;
      listSelectedBarang[idx].price_overall = overallPrice;
      validationForm();

      $('#overallPrice-'+idx).text(overallPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    });

    $('.btnDeleteSelectedBarang').click(function() {
      const getObj = listSelectedBarang[$(this).val()];
      listBarang.push(getObj);
      drawDropdownListBarang();

      listSelectedBarang.splice($(this).val(), 1);
      drawTableSelectedBarang();
      validationForm();
    });
  }

  $('#selectedCabang').on('select2:select', function (e) {
    const getCabangId = $(this).val();
    if (getCabangId) { loadBarang(getCabangId); }
  });

  $('#selectedBarang').on('select2:select', function (e) {
    const getBarangId = $(this).val();
    let getObj = listBarang.find(barang => barang.id == getBarangId);
    listSelectedBarang.push({
      id: getObj.id,
      item_name: getObj.item_name,
      total_item: 0,
      selling_price: getObj.selling_price,
      price_overall: 0
    });
    drawTableSelectedBarang();

    // deleted list barang
    let getIdxBarang = listBarang.findIndex(barang => barang.id == getBarangId);
    listBarang.splice(getIdxBarang, 1);
    drawDropdownListBarang();

    validationForm();
  });

  function drawDropdownListBarang() {
    optBarang = `<option value=''>Pilih Barang</option>`;
    $('#selectedBarang option').remove();

    if (listBarang.length) {
      for (let i = 0 ; i < listBarang.length ; i++) {
        optBarang += `<option value=${listBarang[i].id}>${listBarang[i].item_name}</option>`;
      }
    }
    $('#selectedBarang').append(optBarang);
  }

  $('#btnSubmitPembayaran').click(function() {
    let finalSelectedBarang = [];
    const fd = new FormData();
    const getBranchId = (role.toLowerCase() == 'admin') ? $('#selectedCabang').val() : branchId;

    listSelectedBarang.forEach(barang => {
      finalSelectedBarang.push({
        price_item_pet_shop_id: barang.id,
        total_item: barang.total_item
      })
    });

    fd.append('branch_id', getBranchId); // for kasir id cabang from login data
    fd.append('price_item_pet_shops', JSON.stringify(finalSelectedBarang));
    fd.append('payment_method_id', $('#selectedMetodePembayaran').val());

    $.ajax({
      url: $('.baseUrl').val() + '/api/pembayaranpetshop',
      type: 'POST',
      dataType: 'JSON',
      headers: { 'Authorization': `Bearer ${token}` },
      data: fd, contentType: false, cache: false,
      processData: false,
      beforeSend: function () { $('#loading-screen').show(); },
      success: function (resp) {

        $("#msg-box .modal-body").text('Berhasil Menambah Data');
        $('#msg-box').modal('show');

        const getMasterPaymentId = resp.master_payment_petshop_id;

        processPrint(getMasterPaymentId);

        setTimeout(() => {
          $('#modal-tambah-pembayaran').modal('toggle');
          refreshForm(); loadPembayaranPetshop();
        }, 1000);
      }, complete: function () { $('#loading-screen').hide(); }
      , error: function (err) {
        if (err.status === 422) {
          let errText = ''; $('#beErr').empty(); $('#btnSubmitPembayaran').attr('disabled', true);
          $.each(err.responseJSON.errors, function (idx, v) {
            errText += v + ((idx !== err.responseJSON.errors.length - 1) ? '<br/>' : '');
          });
          $('#beErr').append(errText); isBeErr = true;
        } else if (err.status == 401) {
          localStorage.removeItem('vet-shop');
          location.href = $('.baseUrl').val() + '/masuk';
        }
      }
    });

  });

  function processPrint(master_payment_id) {
    let url = '/pembayaranpetshop/printreceipt/' + master_payment_id;
    window.open($('.baseUrl').val() + url, '_blank');
  }

});
