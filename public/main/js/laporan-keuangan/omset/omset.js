$(document).ready(function () {
  let optCabang = "";
  let getCurrentPage = 1;
  let paramUrlSetup = {
    date_from: "",
    date_to: "",
    branchId: "",
    connection: "",
  };

  widgetRekapOmset({ date_from: null, date_to: null });

  // if (role.toLowerCase() == 'resepsionis') {
  // 	window.location.href = $('.baseUrl').val() + `/unauthorized`;
  // } else {
  if (role.toLowerCase() == "dokter" || role.toLowerCase() == "resepsionis") {
    $("#filterCabang").hide();
    $(".section-right-box-title").css("width", "unset");
    $(".section-right-box-title .btn-download-excel").css(
      "margin-right",
      "unset"
    );
  } else {
    $("#filterCabang").select2({ placeholder: "Cabang", allowClear: true });
    loadCabang();
  }

  const openDatePicker = window.innerWidth < 768 ? "left" : "right";

  $("#datepicker").daterangepicker({
    autoUpdateInput: false,
    opens: openDatePicker,
    applyClass: "btn-info",
    // showDropdowns: true,
    dateLimit: { days: 31 },
    drops: "auto",
    locale: { format: "YYYY-MM-DD", cancelLabel: "Clear" },
  });

  loadLaporanKeuanganOmset();

  $("#filterCabang").on("select2:select", function () {
    onFilterCabang($(this).val());
  });
  $("#filterCabang").on("select2:unselect", function () {
    onFilterCabang($(this).val());
  });

  $('input[id="datepicker"]').on(
    "apply.daterangepicker",
    function (ev, picker) {
      const getStartDate = picker.startDate.format("YYYY-MM-DD");
      const getEndDate = picker.endDate.format("YYYY-MM-DD");
      $(this).val(getStartDate + " - " + getEndDate);

      paramUrlSetup.date_from = getStartDate;
      paramUrlSetup.date_to = getEndDate;
      loadLaporanKeuanganOmset();
      widgetRekapOmset();
    }
  );

  $('input[id="datepicker"]').on(
    "cancel.daterangepicker",
    function (ev, picker) {
      $(this).val("");
      paramUrlSetup.date_from = "";
      paramUrlSetup.date_to = "";
      loadLaporanKeuanganOmset();
      widgetRekapOmset();
    }
  );

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

    loadLaporanKeuanganOmset();
  });

  function onFilterCabang(value) {
    let text = value;
    paramUrlSetup.branchId = text.split("-")[0];
    paramUrlSetup.connection = text.split("-")[1];
    widgetRekapOmset();
    loadLaporanKeuanganOmset();
  }

  function loadLaporanKeuanganOmset() {
    $.ajax({
      url: $(".baseUrl").val() + "/api/laporan-keuangan/rekap-all",
      headers: { Authorization: `Bearer ${token}` },
      type: "GET",
      data: {
        branch_id: paramUrlSetup.branchId,
        connection: paramUrlSetup.connection,
        date_from: paramUrlSetup.date_from,
        date_to: paramUrlSetup.date_to,
      },
      beforeSend: function () {
        $("#loading-screen").show();
      },
      success: function (resp) {
        const getData = resp;
        let loadLaporanKeuanganOmset = "";
        let headLaporanKeuanganOmset = "";

        $("#list-laporan-keuangan-omset tr").remove();
        $("#head-laporan-keuangan-omset tr").remove();

        if (paramUrlSetup.branchId) {
          headLaporanKeuanganOmset +=
            `<tr>` +
            `<th>No</th>` +
            `<th class="onOrdering" data='dates' orderby="none">Periode <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='total_omset' orderby="none">Total Omset (Rp) <span class="fa fa-sort"></span></th>` +
            `` +
            `</tr>`;

          if (getData.length) {
            $.each(getData, function (idx, v) {
              loadLaporanKeuanganOmset +=
                `<tr>` +
                `<td>${++idx}</td>` +
                `<td>${v.dates}</td>` +
                `<td>${
                  typeof v.total_omset == "number"
                    ? v.total_omset
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `</tr>`;
            });
          } else {
            loadLaporanKeuanganOmset += `<tr class="text-center"><td colspan="3">Tidak ada data.</td></tr>`;
          }
        } else {
          headLaporanKeuanganOmset +=
            `<tr>` +
            `<th>No</th>` +
            `<th class="onOrdering" data='dates' orderby="none">Periode <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='total_omset' orderby="none">Total Omset (Rp) <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='veteran_bintaro' orderby="none">Veteran Bintaro <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='tanjung_duren_raya' orderby="none">Tanjung Duren Raya <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='pasar_rebo' orderby="none">Pasar Rebo <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='srengseng_kembangan' orderby="none">Srengseng Kembangan <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='kahfi' orderby="none">Kahfi <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='bsd' orderby="none">BSD <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='kemayoran' orderby="none">Kemayoran <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='tanah_abang' orderby="none">Tanah Abang <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='rawamangun' orderby="none">Rawamangun <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='pondok_gede' orderby="none">Pondok Gede <span class="fa fa-sort"></span></th>` +
            `<th class="onOrdering" data='cirendeu' orderby="none">Cirendeu <span class="fa fa-sort"></span></th>` +
            `` +
            `</tr>`;

          if (getData.length) {
            $.each(getData, function (idx, v) {
              loadLaporanKeuanganOmset +=
                `<tr>` +
                `<td>${++idx}</td>` +
                `<td>${v.dates}</td>` +
                `<td>${
                  typeof v.total_omset == "number"
                    ? v.total_omset
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `<td>${
                  typeof v.veteran_bintaro == "number"
                    ? v.veteran_bintaro
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `<td>${
                  typeof v.tanjung_duren_raya == "number"
                    ? v.tanjung_duren_raya
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `<td>${
                  typeof v.pasar_rebo == "number"
                    ? v.pasar_rebo
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `<td>${
                  typeof v.srengseng_kembangan == "number"
                    ? v.srengseng_kembangan
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `<td>${
                  typeof v.kahfi == "number"
                    ? v.kahfi.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `<td>${
                  typeof v.bsd == "number"
                    ? v.bsd.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `<td>${
                  typeof v.kemayoran == "number"
                    ? v.kemayoran
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `<td>${
                  typeof v.tanah_abang == "number"
                    ? v.tanah_abang
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `<td>${
                  typeof v.rawamangun == "number"
                    ? v.rawamangun
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `<td>${
                  typeof v.pondok_gede == "number"
                    ? v.pondok_gede
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `<td>${
                  typeof v.cirendeu == "number"
                    ? v.cirendeu
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    : ""
                }</td>` +
                `</tr>`;
            });
          } else {
            loadLaporanKeuanganOmset += `<tr class="text-center"><td colspan="3">Tidak ada data.</td></tr>`;
          }
        }

        $("#head-laporan-keuangan-omset").append(headLaporanKeuanganOmset);
        $("#list-laporan-keuangan-omset").append(loadLaporanKeuanganOmset);

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

          loadLaporanKeuanganOmset();
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

  function loadCabang() {
    $.ajax({
      url: $(".baseUrl").val() + "/api/cabang/all",
      headers: { Authorization: `Bearer ${token}` },
      type: "GET",
      beforeSend: function () {
        $("#loading-screen").show();
      },
      success: function (data) {
        optCabang += `<option value=''>Cabang</option>`;

        if (data.length) {
          for (let i = 0; i < data.length; i++) {
            optCabang += `<option value=${data[i].id}-${data[i].connection}>${data[i].branch_name}</option>`;
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

  function widgetRekapOmset() {
    $.ajax({
      url: $(".baseUrl").val() + "/api/laporan-keuangan/rekap-all-chart",
      headers: { Authorization: `Bearer ${token}` },
      type: "GET",
      data: {
        branch_id: paramUrlSetup.branchId,
        connection: paramUrlSetup.connection,
        date_from: paramUrlSetup.date_from,
        date_to: paramUrlSetup.date_to,
      },
      beforeSend: function () {
        $("#loading-screen").show();
      },
      success: function (resp) {
        const getData = resp;
        const tempDataSeries = [];
        const categoriesXAxis = [];

        getData.forEach((dt) => {
          categoriesXAxis.push(dt.dates);
          tempDataSeries.push({ name: dt.dates, y: dt.total_omset });
        });

        const finalSeries = [{ name: "Omset", data: tempDataSeries }];

        Highcharts.chart("rekapWidgetOmset", {
          title: { text: "" },
          xAxis: { categories: categoriesXAxis },
          legend: { enabled: false },
          credits: { enabled: false },
          plotOptions: {
            column: {
              dataLabels: { enabled: true },
            },
          },
          yAxis: { title: { text: "Nominal (Rp)" } },
          series: finalSeries,
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
