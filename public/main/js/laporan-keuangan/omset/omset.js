$(document).ready(function () {
  let optCabang = "";
  const isNoBranch = role.toLowerCase() === "dokter" || role.toLowerCase() === "resepsionis";

  let paramUrlSetup = {
    periode   : "",
    branchId  : "",
    connection: "",
    startDate : "",
    endDate   : "",
    bulan     : "",
    tahun     : "",
  };

  // Populate year select (tahunan)
  const currentYear = new Date().getFullYear();
  let yearOpts = '<option value="">-- Tahun --</option>';
  for (let y = currentYear; y >= currentYear - 10; y--) {
    yearOpts += `<option value="${y}">${y}</option>`;
  }
  $("#selectTahun").html(yearOpts);

  // Bootstrap datepicker – month picker untuk bulanan
  $("#inputBulan").datepicker({
    format      : "mm-yyyy",
    startView   : "months",
    minViewMode : "months",
    autoclose   : true,
  });
  $("#inputBulan, #filter-bulanan .input-group-addon").on("click", function () {
    $("#inputBulan").datepicker("show");
  });
  $("#inputBulan").on("changeDate", function () {
    const d = $(this).datepicker("getDate");
    if (d) {
      paramUrlSetup.bulan = String(d.getMonth() + 1).padStart(2, "0");
      paramUrlSetup.tahun = String(d.getFullYear());
      tryLoad();
    }
  });

  if (isNoBranch) {
    $("#filterCabang").hide();
    $(".section-right-box-title").css("width", "unset");
  } else {
    $("#filterCabang").select2({ placeholder: "Cabang", allowClear: true });
    loadCabang();
  }

  // Tombol filter periode
  $("#periode-omset-group button").on("click", function () {
    $("#periode-omset-group button").removeClass("btn-info active").addClass("btn-default");
    $(this).removeClass("btn-default").addClass("btn-info active");
    paramUrlSetup.periode = $(this).data("periode");
    resetDateParams();
    showDateFilter(paramUrlSetup.periode);
    tryLoad();
  });

  // Filter cabang
  $("#filterCabang").on("select2:select", function () {
    onFilterCabang($(this).val());
  });
  $("#filterCabang").on("select2:unselect", function () {
    onFilterCabang($(this).val());
  });

  // Mingguan: date range
  $("#startDate, #endDate").on("change", function () {
    paramUrlSetup.startDate = $("#startDate").val();
    paramUrlSetup.endDate   = $("#endDate").val();
    tryLoad();
  });

  // Tahunan: tahun
  $("#selectTahun").on("change", function () {
    paramUrlSetup.tahun = $(this).val();
    tryLoad();
  });

  function onFilterCabang(value) {
    paramUrlSetup.branchId   = value ? value.split("-")[0] : "";
    paramUrlSetup.connection = value ? value.split("-")[1] : "";
    tryLoad();
  }

  function showDateFilter(periode) {
    $(".filter-periode-input").css("display", "none");
    if (periode === "mingguan") {
      $("#filter-mingguan").css("display", "flex");
    } else if (periode === "bulanan") {
      $("#filter-bulanan").css("display", "flex");
    } else if (periode === "tahunan") {
      $("#filter-tahunan").css("display", "flex");
    }
  }

  function resetDateParams() {
    paramUrlSetup.startDate = "";
    paramUrlSetup.endDate   = "";
    paramUrlSetup.bulan     = "";
    paramUrlSetup.tahun     = "";
    $("#startDate").val("");
    $("#endDate").val("");
    $("#inputBulan").val("").datepicker("update", "");
    $("#selectTahun").val("");
  }

  function isDateReady() {
    const p = paramUrlSetup.periode;
    if (p === "mingguan")    return !!(paramUrlSetup.startDate && paramUrlSetup.endDate);
    if (p === "bulanan")     return !!(paramUrlSetup.bulan && paramUrlSetup.tahun);
    if (p === "tahunan")     return !!paramUrlSetup.tahun;
    if (p === "sejak_dibuka") return true;
    return false;
  }

  function canLoad() {
    if (!isDateReady()) return false;
    if (!isNoBranch && !paramUrlSetup.branchId) return false;
    return true;
  }

  function tryLoad() {
    if (canLoad()) loadAll();
  }

  function buildParam() {
    const p    = paramUrlSetup.periode;
    const base = {
      periode   : p,
      branch_id : paramUrlSetup.branchId,
      connection: paramUrlSetup.connection,
    };
    if (p === "mingguan") {
      base.start_date = paramUrlSetup.startDate;
      base.end_date   = paramUrlSetup.endDate;
    } else if (p === "bulanan") {
      base.bulan = paramUrlSetup.bulan;
      base.tahun = paramUrlSetup.tahun;
    } else if (p === "tahunan") {
      base.tahun = paramUrlSetup.tahun;
    }
    return base;
  }

  function loadAll() {
    loadLaporanKeuanganOmset();
    widgetRekapOmset();
  }

  function loadLaporanKeuanganOmset() {
    $.ajax({
      url    : $(".baseUrl").val() + "/api/laporan-keuangan/rekap-all",
      headers: { Authorization: `Bearer ${token}` },
      type   : "GET",
      data   : buildParam(),
      beforeSend: function () { $("#loading-screen").show(); },
      success: function (resp) {
        const getData = resp.datas;
        const headers = resp.branches;
        let bodyHTML  = "";
        let headHTML  = "";

        $("#list-laporan-keuangan-omset tr").remove();
        $("#head-laporan-keuangan-omset tr").remove();

        if (paramUrlSetup.branchId) {
          headHTML =
            `<tr>` +
            `<th>No</th>` +
            `<th>Periode</th>` +
            `<th>Total Omset (Rp)</th>` +
            `</tr>`;

          if (getData.length) {
            $.each(getData, function (idx, v) {
              bodyHTML +=
                `<tr>` +
                `<td>${++idx}</td>` +
                `<td>${v.dates}</td>` +
                `<td>${Number(v.total_omset || 0).toLocaleString("id-ID")}</td>` +
                `</tr>`;
            });
          } else {
            bodyHTML = `<tr class="text-center"><td colspan="3">Tidak ada data.</td></tr>`;
          }
        } else {
          let thHTML = "";
          headers.forEach((branch) => {
            thHTML += `<th>${branch.branch_name}</th>`;
          });

          headHTML =
            `<tr>` +
            `<th>No</th>` +
            `<th>Periode</th>` +
            `<th>Total Omset (Rp)</th>` +
            thHTML +
            `</tr>`;

          if (getData.length) {
            const fixedKeys  = ["dates", "total_omset"];
            const branchKeys = Object.keys(getData[0]).filter((k) => !fixedKeys.includes(k));

            $.each(getData, function (idx, v) {
              let row = `<tr>`;
              row += `<td>${++idx}</td>`;
              row += `<td>${v.dates}</td>`;
              row += `<td>${Number(v.total_omset || 0).toLocaleString("id-ID")}</td>`;
              branchKeys.forEach((key) => {
                row += `<td>${Number(v[key] || 0).toLocaleString("id-ID")}</td>`;
              });
              row += `</tr>`;
              bodyHTML += row;
            });
          } else {
            bodyHTML = `<tr class="text-center"><td colspan="3">Tidak ada data.</td></tr>`;
          }
        }

        $("#head-laporan-keuangan-omset").append(headHTML);
        $("#list-laporan-keuangan-omset").append(bodyHTML);
      },
      complete: function () { $("#loading-screen").hide(); },
      error: function (err) {
        if (err.status === 401) {
          localStorage.removeItem("vet-clinic");
          location.href = $(".baseUrl").val() + "/masuk";
        }
      },
    });
  }

  function loadCabang() {
    $.ajax({
      url    : $(".baseUrl").val() + "/api/cabang/all",
      headers: { Authorization: `Bearer ${token}` },
      type   : "GET",
      beforeSend: function () { $("#loading-screen").show(); },
      success: function (data) {
        optCabang += `<option value=''>Cabang</option>`;
        if (data.length) {
          data.forEach(function (item) {
            optCabang += `<option value="${item.id}-${item.connection}">${item.branch_name}</option>`;
          });
        }
        $("#filterCabang").append(optCabang);
      },
      complete: function () { $("#loading-screen").hide(); },
      error: function (err) {
        if (err.status === 401) {
          localStorage.removeItem("vet-clinic");
          location.href = $(".baseUrl").val() + "/masuk";
        }
      },
    });
  }

  function widgetRekapOmset() {
    $.ajax({
      url    : $(".baseUrl").val() + "/api/laporan-keuangan/rekap-all-chart",
      headers: { Authorization: `Bearer ${token}` },
      type   : "GET",
      data   : buildParam(),
      beforeSend: function () { $("#loading-screen").show(); },
      success: function (resp) {
        const tempDataSeries  = [];
        const categoriesXAxis = [];

        resp.forEach((dt) => {
          categoriesXAxis.push(dt.dates);
          tempDataSeries.push({ name: dt.dates, y: dt.total_omset });
        });

        Highcharts.chart("rekapWidgetOmset", {
          chart  : { type: "column" },
          title  : { text: "" },
          xAxis  : { categories: categoriesXAxis },
          yAxis  : { title: { text: "Nominal (Rp)" } },
          legend : { enabled: false },
          credits: { enabled: false },
          plotOptions: {
            column: { dataLabels: { enabled: true } },
          },
          series: [{ name: "Omset", data: tempDataSeries }],
        });
      },
      complete: function () { $("#loading-screen").hide(); },
      error: function (err) {
        if (err.status === 401) {
          localStorage.removeItem("vet-clinic");
          location.href = $(".baseUrl").val() + "/masuk";
        }
      },
    });
  }
});
