$(document).ready(function() {
  let optCabang = '';

  widgetTotalPasien({month: null, year: null});
  widgetRawatInap();
  // loadCabang();
  let getAuthUser = localStorage.getItem('vet-clinic');

  if (!getAuthUser) {
    location.href = $('.baseUrl').val() + '/masuk';
  } else {
    getAuthUser = JSON.parse(getAuthUser);
    role         = getAuthUser.role.toLowerCase();

    if (role != 'admin') {
      $('.pasien').hide();
      $('.rawat-inap').hide();
    }
  }

  $('#datepicker-jumlah-pasien').datepicker({
    autoclose: true, clearBtn: true,
    format: 'mm-yyyy', todayHighlight: true,
    startView: 'months',  minViewMode: 'months'
  }).on('changeDate', function(e) {
    const getDate  = e.format();
    const getMonth = getDate.split('-')[0];
    const getYear  = getDate.split('-')[1];

    widgetTotalPasien({month: getMonth, year: getYear});
  });

  $('#datepicker-rawat-inap').datepicker({
    autoclose: true,
    clearBtn: true,
    format: 'yyyy-mm-dd',
    todayHighlight: true,
  }).on('changeDate', function(e) {
    widgetRawatInap(e.format());
  });

  $('#filterCabangRawatInap').select2({ placeholder: 'Cabang', allowClear: true });

  function widgetTotalPasien(param) {

    $.ajax({
			url     : $('.baseUrl').val() + '/api/dashboard/barchart',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { month: param.month, year: param.year},
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(resp) {
        const getData = resp;
        const tempDataSeries = [];
        const categoriesXAxis = [];

        getData.forEach(dt => {
          categoriesXAxis.push(dt.branch_name);
          tempDataSeries.push({name: dt.branch_name, y: dt.total_patient});
        });

        const finalSeries = [{name: 'Total Pasien Widget', data: tempDataSeries}];

        Highcharts.chart('totalPasienWidget', {
          chart: { type: 'column' },
          title: { text: '' },
          xAxis: { categories: categoriesXAxis },
          legend: {enabled: false},
          credits: { enabled: false },
          plotOptions: {
            column: {
              dataLabels: { enabled: true }
            }
          },
          yAxis: { title: { text: '' } },
          series: finalSeries
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


  function widgetRawatInap(date) {
    $.ajax({
			url     : $('.baseUrl').val() + '/api/dashboard/barchart-inpatient',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
			data	  : { date: date},
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(resp) {
        const getData = resp;
        const tempDataSeries = [];
        const categoriesXAxis = [];

        getData.forEach(dt => {
          categoriesXAxis.push(dt.branch_name);
          tempDataSeries.push({name: dt.branch_name, y: dt.total_patient});
        });

        const finalSeries = [{name: 'Total Pasien Widget', data: tempDataSeries}];

        Highcharts.chart('rawatInapWidget', {
          chart: { type: 'column' },
          title: { text: '' },
          xAxis: { categories: categoriesXAxis },
          legend: {enabled: false},
          credits: { enabled: false },
          plotOptions: {
            column: {
              dataLabels: { enabled: true }
            }
          },
          yAxis: { title: { text: '' } },
          series: finalSeries
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
				$('#filterCabangRawatInap').append(optCabang);
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
