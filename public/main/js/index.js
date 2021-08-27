$(document).ready(function() {
  let optCabang = '';

  widgetTotalPasien({month: null, year: null});
  widgetUmur();
  loadCabang();
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
    widgetUmur(e.format());
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


  function widgetUmur(date) {
    Highcharts.chart('rawatInapWidget', {
      chart: {
          type: 'area'
      },
      title: { text: '' },
      subtitle: { text: ''},
      xAxis: { categories: ['Internet Explorer', 'Firefox', 'Edge', 'Safari']},
      yAxis: { title: { text: '' } },
      credits: { enabled: false },
      plotOptions: {
        area: {
          dataLabels:{ enabled: true },
          marker: { enabled: true, symbol: 'circle' }
        }
      },
      series: [{
          name: 'Total Pasien Widget',
          data: [{
              name: 'Internet Explorer',
              y: 11.84
          }, {
              name: 'Firefox',
              y: 10.85
          }, {
              name: 'Edge',
              y: 4.67
          }, {
              name: 'Safari',
              y: 4.18
          }]
        }]
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
