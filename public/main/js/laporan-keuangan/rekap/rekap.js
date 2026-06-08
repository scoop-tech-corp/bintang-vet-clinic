$(document).ready(function () {
  let optCabang = "";
  let optPeriode = "";
  let suppressDateChange = false;
  let getCurrentPage = 1;
  let paramUrlSetup = {
    orderby: "",
    column: "",
    monthFrom: "",
    yearFrom: "",
    monthTo: "",
    yearTo: "",
    branchId: "",
    periodeId: "",
  };

  // if (role.toLowerCase() == 'resepsionis') {
  // 	window.location.href = $('.baseUrl').val() + `/unauthorized`;
  // } else {
  if (role.toLowerCase() == "dokter" || role.toLowerCase() == "resepsionis") {
    $("#filterCabang").hide();
    $("#filterPeriode").hide();
    $(".section-right-box-title").css("width", "unset");
    $(".section-right-box-title .btn-download-excel").css(
      "margin-right",
      "unset"
    );
  } else {
    $("#filterCabang").select2({ placeholder: "Cabang", allowClear: true });
    loadCabang();

    $("#filterPeriode").select2({ placeholder: "Periode", allowClear: true });
    loadPeriode();
  }

  const datepickerOpts = {
    autoclose: true,
    clearBtn: true,
    format: 'mm-yyyy',
    todayHighlight: true,
    startView: 'months',
    minViewMode: 'months',
  };

  $('#datepickerFrom').datepicker(datepickerOpts).on('changeDate', function(e) {
    if (suppressDateChange) return;
    const parts = e.format().split('-');
    paramUrlSetup.monthFrom = parts[0];
    paramUrlSetup.yearFrom  = parts[1];
    if (paramUrlSetup.monthTo && paramUrlSetup.yearTo) {
      widgetRekap();
      loadLaporanKeuanganRekap();
    }
  });

  $('#datepickerTo').datepicker(datepickerOpts).on('changeDate', function(e) {
    if (suppressDateChange) return;
    const parts = e.format().split('-');
    paramUrlSetup.monthTo = parts[0];
    paramUrlSetup.yearTo  = parts[1];
    if (paramUrlSetup.monthFrom && paramUrlSetup.yearFrom) {
      widgetRekap();
      loadLaporanKeuanganRekap();
    }
  });

  // Isi opsi tahun untuk mode Tahunan (5 tahun terakhir)
  const currentYear = new Date().getFullYear();
  $('#filterYear').append('<option value="">-- Pilih Tahun --</option>');
  for (let y = currentYear; y >= currentYear - 4; y--) {
    $('#filterYear').append(`<option value="${y}">${y}</option>`);
  }
  $('#filterYear').on('change', function () {
    const year = $(this).val();
    if (!year) return;
    paramUrlSetup.year  = year;
    paramUrlSetup.month = '';
    widgetRekap();
    loadLaporanKeuanganRekap();
  });

  // Tampilkan placeholder — API tidak dipanggil sampai user memilih Periode
  $('#list-laporan-keuangan-rekap').html(
    '<tr class="text-center"><td colspan="7" style="padding:24px; color:#888;">Silahkan pilih <strong>Periode</strong> untuk menampilkan data.</td></tr>'
  );

  $("#filterCabang").on("select2:select", function () {
    onFilterCabang($(this).val());
  });
  $("#filterCabang").on("select2:unselect", function () {
    onFilterCabang($(this).val());
  });

  $("#filterPeriode").on("select2:select", function () {
    onFilterPeriode($(this).val());
  });
  $("#filterPeriode").on("select2:unselect", function () {
    onFilterPeriode($(this).val());
  });

  $(".btn-download-excel").click(function () {
    const getBranchId = paramUrlSetup.branchId;
    const getMonth = paramUrlSetup.month;
    const getYear = paramUrlSetup.year;

    if (getBranchId && getMonth && getYear) {
      $.ajax({
        url: $(".baseUrl").val() + "/api/laporan-keuangan/rekap/download",
        headers: { Authorization: `Bearer ${token}` },
        type: "GET",
        data: {
          month: paramUrlSetup.month,
          year: paramUrlSetup.year,
          branch_id: getBranchId,
        },
        xhrFields: { responseType: "blob" },
        beforeSend: function () {
          $("#loading-screen").show();
        },
        success: function (data, status, xhr) {
          let disposition = xhr.getResponseHeader("content-disposition");
          let matches = /"([^"]*)"/.exec(disposition);
          let filename =
            matches != null && matches[1] ? matches[1] : "file.xlsx";
          let blob = new Blob([data], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          });
          let downloadUrl = URL.createObjectURL(blob);
          let a = document.createElement("a");

          a.href = downloadUrl;
          a.download = filename;
          document.body.appendChild(a);
          a.click();
        },
        complete: function () {
          $("#loading-screen").hide();
        },
        error: function (err) {
          if (err.status == 401) {
            localStorage.removeItem("vet-clinic");
            location.href = $(".baseUrl").val() + "/masuk";
          }
        },
      });
    } else {
      $("#msg-box .modal-body").text("Pilih cabang dan tanggal dahulu!");
      $("#msg-box").modal("show");
    }
  });

  $(".onOrdering").click(function () {
    const column = $(this).attr("data");
    const orderBy = $(this).attr("orderby");
    $('.onOrdering[data="' + column + '"]')
      .children()
      .remove();

    if (orderBy == "none" || orderBy == "asc") {
      $(this).attr("orderby", "desc");
      $(this).append('<span class="fa fa-sort-desc"></span>');
    } else if (orderBy == "desc") {
      $(this).attr("orderby", "asc");
      $(this).append('<span class="fa fa-sort-asc"></span>');
    }

    paramUrlSetup.orderby = $(this).attr("orderby");
    paramUrlSetup.column = column;

    loadLaporanKeuanganRekap();
  });

  function onFilterCabang(value) {
    paramUrlSetup.branchId = value;
    // Hanya panggil API kalau Periode sudah dipilih
    if (paramUrlSetup.periodeId) {
      widgetRekap();
      loadLaporanKeuanganRekap();
    }
  }

  function onFilterPeriode(value) {
    paramUrlSetup.periodeId = value;
    paramUrlSetup.monthFrom = '';
    paramUrlSetup.yearFrom  = '';
    paramUrlSetup.monthTo   = '';
    paramUrlSetup.yearTo    = '';

    if (value == 1) {
      // Bulanan — tampilkan range datepicker dari-sampai
      $('#dateFilterLabel').text('Pilih Rentang Bulan');
      $('#monthYearPicker').css('display', 'flex');
      $('#filterYear').hide();
      $('#dateFilterSection').show();
      suppressDateChange = true;
      $('#datepickerFrom').datepicker('clearDates');
      $('#datepickerTo').datepicker('clearDates');
      suppressDateChange = false;
    } else if (value == 2) {
      // Tahunan — tampilkan year select, reset ke placeholder
      $('#dateFilterLabel').text('Pilih Tahun');
      $('#monthYearPicker').hide();
      $('#filterYear').val('').show();
      $('#dateFilterSection').show();
    } else if (value == 3) {
      // Sejak Awal Klinik Buka — tidak butuh input tanggal, langsung load
      $('#dateFilterSection').hide();
      widgetRekap();
      loadLaporanKeuanganRekap();
    } else {
      // Periode dikosongkan
      $('#dateFilterSection').hide();
      $('#list-laporan-keuangan-rekap').html(
        '<tr class="text-center"><td colspan="7" style="padding:24px; color:#888;">Silahkan pilih <strong>Periode</strong> untuk menampilkan data.</td></tr>'
      );
    }
  }

  function loadLaporanKeuanganRekap() {
    $.ajax({
      url: $(".baseUrl").val() + "/api/laporan-keuangan/rekap/table",
      headers: { Authorization: `Bearer ${token}` },
      type: "GET",
      data: {
        orderby: paramUrlSetup.orderby,
        column: paramUrlSetup.column,
        month_from: paramUrlSetup.monthFrom,
        year_from: paramUrlSetup.yearFrom,
        month_to: paramUrlSetup.monthTo,
        year_to: paramUrlSetup.yearTo,
        branch_id: paramUrlSetup.branchId,
        periode: paramUrlSetup.periodeId,
        page: getCurrentPage,
      },
      beforeSend: function () {
        $("#loading-screen").show();
      },
      success: function (resp) {
        //console.log(resp);

        const getData = resp;
        let loadLaporanKeuanganRekap = "";

        $("#list-laporan-keuangan-rekap tr").remove();

        if (getData.length) {
          $.each(getData, function (idx, v) {
            loadLaporanKeuanganRekap +=
              `<tr>` +
              `<td>${++idx}</td>` +
              `<td>${v.dates}</td>` +
              `<td>${
                Number(v.total_omset || 0).toLocaleString('id-ID')
                // typeof v.total_omset == "number"
                //   ? v.total_omset.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                //   : ""
              }</td>` +
              `<td>${
                Number(v.discount || 0).toLocaleString('id-ID')
                // typeof v.discount == "number"
                //   ? v.discount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                //   : ""
              }</td>` +
              `<td>${
                Number(v.expenses || 0).toLocaleString('id-ID')
                // typeof v.expenses == "number"
                //   ? v.expenses.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                //   : ""
              }</td>` +
              `<td>${
                Number(v.sallary || 0).toLocaleString('id-ID')
                // typeof v.sallary == "number"
                //   ? v.sallary.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                //   : ""
              }</td>` +
              `<td>${
                Number(v.netto || 0).toLocaleString('id-ID')
                // typeof v.netto == "number"
                //   ? v.netto.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                //   : ""
              }</td>` +
              `</tr>`;
          });
        } else {
          loadLaporanKeuanganRekap += `<tr class="text-center"><td colspan="7">Tidak ada data.</td></tr>`;
        }

        $("#list-laporan-keuangan-rekap").append(loadLaporanKeuanganRekap);

        generatePagination(getCurrentPage, resp.total_paging);

        $(".pagination > li > a").click(function () {
          const getClassName = this.className;
          const getNumber = parseFloat($(this).text());

          if (
            (getCurrentPage === 1 && getClassName.includes("arrow-left")) ||
            (getCurrentPage === resp.total_paging &&
              getClassName.includes("arrow-right"))
          ) {
            return;
          }

          if (getClassName.includes("arrow-left")) {
            getCurrentPage = getCurrentPage - 1;
          } else if (getClassName.includes("arrow-right")) {
            getCurrentPage = getCurrentPage + 1;
          } else {
            getCurrentPage = getNumber;
          }

          loadLaporanKeuanganRekap();
        });
      },
      complete: function () {
        $("#loading-screen").hide();
      },
      error: function (err) {
        if (err.status == 401) {
          localStorage.removeItem("vet-clinic");
          location.href = $(".baseUrl").val() + "/masuk";
        } else {
          $("#list-laporan-keuangan-rekap").html(
            `<tr class="text-center"><td colspan="7" style="color:red;">Gagal memuat data (${err.status}). Coba ulangi.</td></tr>`
          );
        }
      },
    });
  }

  function loadCabang() {
    $.ajax({
      url: $(".baseUrl").val() + "/api/cabang",
      headers: { Authorization: `Bearer ${token}` },
      type: "GET",
      beforeSend: function () {
        $("#loading-screen").show();
      },
      success: function (data) {
        optCabang += `<option value=''>Cabang</option>`;

        if (data.length) {
          for (let i = 0; i < data.length; i++) {
            optCabang += `<option value=${data[i].id}>${data[i].branch_name}</option>`;
          }
        }
        $("#filterCabang").append(optCabang);
      },
      complete: function () {
        $("#loading-screen").hide();
      },
      error: function (err) {
        if (err.status == 401) {
          localStorage.removeItem("vet-clinic");
          location.href = $(".baseUrl").val() + "/masuk";
        }
      },
    });
  }

  function loadPeriode() {
    $.ajax({
      url: $(".baseUrl").val() + "/api/laporan-keuangan/rekap/listperiode",
      headers: { Authorization: `Bearer ${token}` },
      type: "GET",
      beforeSend: function () {
        $("#loading-screen").show();
      },
      success: function (data) {
        optPeriode += `<option value=''>Periode</option>`;

        if (data.length) {
          for (let i = 0; i < data.length; i++) {
            optPeriode += `<option value=${data[i].id}>${data[i].periode}</option>`;
          }
        }
        $("#filterPeriode").append(optPeriode);
      },
      complete: function () {
        $("#loading-screen").hide();
      },
      error: function (err) {
        if (err.status == 401) {
          localStorage.removeItem("vet-clinic");
          location.href = $(".baseUrl").val() + "/masuk";
        }
      },
    });
  }

  function widgetRekap() {
    $.ajax({
      url: $(".baseUrl").val() + "/api/laporan-keuangan/rekap/chart",
      headers: { Authorization: `Bearer ${token}` },
      type: "GET",
      data: {
        periode: paramUrlSetup.periodeId,
        branch_id: paramUrlSetup.branchId,
        month_from: paramUrlSetup.monthFrom,
        year_from: paramUrlSetup.yearFrom,
        month_to: paramUrlSetup.monthTo,
        year_to: paramUrlSetup.yearTo,
      },
      beforeSend: function () {
        $("#loading-screen").show();
      },
      success: function (resp) {
        const categories = [];
        const series = {
          omset     : [],
          discount  : [],
          expenses  : [],
          sallary   : [],
          netto     : [],
        };

        resp.forEach((dt) => {
          categories.push(dt.periode);
          series.omset   .push(dt.total_omset || 0);
          series.discount.push(dt.discount    || 0);
          series.expenses.push(dt.expenses    || 0);
          series.sallary .push(dt.sallary     || 0);
          series.netto   .push(dt.netto       || 0);
        });

        Highcharts.chart("rekapWidget", {
          chart  : { type: "line" },
          title  : { text: "" },
          credits: { enabled: false },
          xAxis  : { categories: categories },
          yAxis  : { title: { text: "Nominal (Rp)" } },
          legend : { enabled: true },
          plotOptions: { column: { dataLabels: { enabled: false } } },
          series: [
            { name: "Total Omset"  , data: series.omset    },
            { name: "Diskon"       , data: series.discount },
            { name: "Pengeluaran"  , data: series.expenses },
            { name: "Penggajian"   , data: series.sallary  },
            { name: "Netto"        , data: series.netto    },
          ],
        });
      },
      complete: function () {
        $("#loading-screen").hide();
      },
      error: function (err) {
        if (err.status == 401) {
          localStorage.removeItem("vet-clinic");
          location.href = $(".baseUrl").val() + "/masuk";
        }
      },
    });
  }
});
