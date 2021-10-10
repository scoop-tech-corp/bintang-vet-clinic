$(document).ready(function() {

  let optCabang = '';
  let paramUrlSetup = {
		orderby:'',
		column: '',
    date_from: '',
    date_to: '',
    branchId: ''
  };

  if (role.toLowerCase() == 'resepsionis') {
		window.location.href = $('.baseUrl').val() + `/unauthorized`;
	} else {
    if (role.toLowerCase() == 'dokter') {
      $('#filterCabang').hide();
      $('.section-right-box-title').css('width', 'unset');
      $('.section-right-box-title .btn-download-excel').css('margin-right', 'unset');
  	} else {
      $('#filterCabang').select2({ placeholder: 'Cabang', allowClear: true });
      loadCabang();
    }

    const openDatePicker = (window.innerWidth < 768) ? 'left' : 'right';

    $('#datepicker').daterangepicker({
      autoUpdateInput: false,
      opens: openDatePicker,
      applyClass: 'btn-info',
      // showDropdowns: true,
      dateLimit: { days: 7 },
      drops: 'auto',
      locale: {format: 'YYYY-MM-DD', cancelLabel: 'Clear'}
    });
  }

  loadLaporanKeuanganMingguan();

  $('#filterCabang').on('select2:select', function () { onFilterCabang($(this).val()); });
  $('#filterCabang').on("select2:unselect", function () { onFilterCabang($(this).val()); });

  $('input[id="datepicker"]').on('apply.daterangepicker', function(ev, picker) {
    const getStartDate = picker.startDate.format('YYYY-MM-DD');
    const getEndDate   = picker.endDate.format('YYYY-MM-DD');
    $(this).val(getStartDate + ' - ' + getEndDate);

    paramUrlSetup.date_from = getStartDate; paramUrlSetup.date_to   = getEndDate;
    loadLaporanKeuanganMingguan();
  });

  $('input[id="datepicker"]').on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
    paramUrlSetup.date_from = ''; paramUrlSetup.date_to   = '';
    loadLaporanKeuanganMingguan();
  });

  $('.btn-download-excel').click(function() {
    const getBranchId = (role.toLowerCase() == 'dokter') ? branchId : paramUrlSetup.branchId;

    if (getBranchId) {
      $.ajax({
        url     : $('.baseUrl').val() + '/api/laporan-keuangan/mingguan/download',
        headers : { 'Authorization': `Bearer ${token}` },
        type    : 'GET',
        data	  : { orderby: paramUrlSetup.orderby, column: paramUrlSetup.column, date_from: paramUrlSetup.date_from, date_to: paramUrlSetup.date_to, branch_id: getBranchId },
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
    } else {
      $("#msg-box .modal-body").text('Pilih cabang dahulu!');
      $('#msg-box').modal('show');
    }

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

		loadLaporanKeuanganMingguan();
  });

  function onFilterCabang(value) {
    paramUrlSetup.branchId = value;
		loadLaporanKeuanganMingguan();
  }

  function loadLaporanKeuanganMingguan() {
    $.ajax({
			url     : $('.baseUrl').val() + '/api/laporan-keuangan/mingguan',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { orderby: paramUrlSetup.orderby, column: paramUrlSetup.column, date_from: paramUrlSetup.date_from, date_to: paramUrlSetup.date_to, branch_id: paramUrlSetup.branchId },
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(resp) {

        const getData = resp.data;
				let listLaporanKeuanganMingguan = '';

				$('#list-laporan-keuangan-mingguan tr').remove();

				if (getData.length) {
					$.each(getData, function(idx, v) {
						listLaporanKeuanganMingguan += `<tr>`
							+ `<td>${++idx}</td>`
							+ `<td>${v.registration_number}</td>`
              + `<td>${v.created_at}</td>`
							+ `<td>${v.patient_number}</td>`
							+ `<td>${v.pet_category}</td>`
							+ `<td>${v.pet_name}</td>`
							+ `<td>${v.complaint}</td>`
							+ `<td>${(v.status_outpatient_inpatient == 1) ? 'Rawat Inap' : 'Rawat Jalan'}</td>`
							+ `<td>${typeof(v.price_overall) == 'number' ? v.price_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
							+ `<td>${typeof(v.capital_price) == 'number' ? v.capital_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
							+ `<td>${typeof(v.doctor_fee) == 'number' ? v.doctor_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
							+ `<td>${typeof(v.petshop_fee)== 'number' ? v.petshop_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
							+ `<td>${v.created_by}</td>`
							+ `<td>
									<button type="button" class="btn btn-info openDetail" value=${v.list_of_payment_id} title="Detail"><i class="fa fa-eye" aria-hidden="true"></i></button>
								</td>`
							+ `</tr>`;
					});
				} else { listLaporanKeuanganMingguan += `<tr class="text-center"><td colspan="14">Tidak ada data.</td></tr>`; }
				$('#list-laporan-keuangan-mingguan').append(listLaporanKeuanganMingguan);

				const priceOverall = (resp.price_overall > -1) ? resp.price_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-';
				const capitalPrice = (resp.capital_price > -1) ? resp.capital_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-';
				const docterFee    = (resp.doctor_fee > -1) ? resp.doctor_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-';
				const petshopFee   = (resp.petshop_fee > -1) ? resp.petshop_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '-';

        $('#total-keseluruhan-txt').text(`Rp. ${priceOverall}`);
        $('#harga-modal-txt').text(`Rp. ${capitalPrice}`);
        $('#fee-dokter-txt').text(`Rp. ${docterFee}`);
        $('#fee-petshop-txt').text(`Rp. ${petshopFee}`);

        $('.openDetail').click(function() {
					window.location.href = $('.baseUrl').val() + `/laporan-keuangan-mingguan/detail/${$(this).val()}?dateFrom=${paramUrlSetup.date_from}&dateTo=${paramUrlSetup.date_to}`;
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

});
