$(document).ready(function() {

  widgetTotalPasien({month: null, year: null});
  widgetJenisKelamin();
  widgetUmur();
  let getAuthUser = localStorage.getItem('vet-clinic');

  if (!getAuthUser) {
    location.href = $('.baseUrl').val() + '/masuk';
  } else {
    getAuthUser = JSON.parse(getAuthUser);
    role         = getAuthUser.role.toLowerCase();

    if (role != 'admin') {
      $('.pasien').hide();
      $('.kelamin').hide();
      $('.umur').hide();
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

  function widgetJenisKelamin() {
    Highcharts.chart('jenisKelaminWidget', {
      chart: { type: 'pie' },
      title: { text: '' },
      plotOptions: {
        pie: { cursor: 'pointer',
        dataLabels: {
          enabled: true,
          format: '<b>{point.name}</b>: {point.y}'
        } }
      },
      credits: { enabled: false },
      series: [{
          name: 'Brands',
          colorByPoint: true,
          data: [{
              name: 'Chrome',
              y: 61.41,
          }, {
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
          }, {
              name: 'Sogou Explorer',
              y: 1.64
          }, {
              name: 'Opera',
              y: 1.6
          }, {
              name: 'QQ',
              y: 1.2
          }, {
              name: 'Other',
              y: 2.61
          }]
      }]
    });
  }

  function widgetUmur() {
    Highcharts.chart('umurWidget', {
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

});
