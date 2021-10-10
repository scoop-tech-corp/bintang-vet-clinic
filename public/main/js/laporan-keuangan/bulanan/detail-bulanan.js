$(document).ready(function() {

  if (role.toLowerCase() == 'resepsionis') {
		window.location.href = $('.baseUrl').val() + `/unauthorized`;
	} else {
    const url = window.location.pathname;
    const stuff = url.split('/');
    const lastUrl = stuff[stuff.length-1];
    const getParamMonth = new URL(window.location.href).searchParams.get('month');
    const getParamYear = new URL(window.location.href).searchParams.get('year');

    refreshText();
    loadDetailLaporanKeuanganBulanan(lastUrl, {month: getParamMonth, year: getParamYear});
  }

  $('.btn-back-to-list .text, #btnKembali').click(function() {
    window.location.href = $('.baseUrl').val() + '/laporan-keuangan-bulanan';
  });

  function loadDetailLaporanKeuanganBulanan(paramId, paramMonthYear) {
    $.ajax({
      url     : $('.baseUrl').val() + '/api/laporan-keuangan/detail',
      headers : { 'Authorization': `Bearer ${token}` },
      type    : 'GET',
      data	  : { id: paramId, month: paramMonthYear.month, year: paramMonthYear.year },
      beforeSend: function() { $('#loading-screen').show(); },
      success: function(data) {
      const getData = data;

      $('#nomorRegistrasiDetailTxt').text(getData.registration.registration_number); $('#nomorPasienDetailTxt').text(getData.registration.patient_number);
      $('#jenisHewanDetailTxt').text(getData.registration.pet_category); $('#namaHewanDetailTxt').text(getData.registration.pet_name);
      $('#jenisKelaminDetailTxt').text(getData.registration.pet_gender);
      $('#usiaHewanTahunDetailTxt').text(`${getData.registration.pet_year_age} Tahun`); $('#usiaHewanBulanDetailTxt').text(`${getData.registration.pet_month_age} Bulan`);
      $('#namaPemilikDetailTxt').text(getData.registration.owner_name); $('#alamatPemilikDetailTxt').text(getData.registration.owner_address);
      $('#nomorHpPemilikDetailTxt').text(getData.registration.owner_phone_number);
      $('#keluhanDetailTxt').text(getData.registration.complaint); $('#namaPendaftarDetailTxt').text(getData.registration.registrant);
      $('#rawatInapDetailTxt').text(getData.status_outpatient_inpatient ? 'Ya' : 'Tidak');
      $('#statusPemeriksaanDetailTxt').text(getData.status_finish ? 'Selesai' : 'Belum');
      $('#anamnesaDetailTxt').text(getData.check_up_result.anamnesa); $('#diagnosaDetailTxt').text(getData.check_up_result.diagnosa);
      $('#signDetailTxt').text(getData.check_up_result.sign);

      // draw list jasa
      let rowListJasa = ''; let no1 = 1;
        $('#detail-list-jasa tr').remove();
        if (getData.list_of_payment_services.length) {
          getData.list_of_payment_services.forEach((lj, idx) => {
            rowListJasa += `<tr>`
              + `<td>${no1}</td>`
              + `<td>${lj.created_at}</td>`
              + `<td>${lj.created_by}</td>`
              + `<td>${lj.category_name}</td>`
              + `<td>${lj.service_name}</td>`
              + `<td>${lj.quantity}</td>`
              + `<td>${typeof(lj.selling_price) == 'number' ? lj.selling_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
              + `<td>${typeof(lj.price_overall) == 'number' ? lj.price_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
              + `<td>${typeof(lj.capital_price) == 'number' ? lj.capital_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
              + `<td>${typeof(lj.doctor_fee) == 'number' ? lj.doctor_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
              + `<td>${typeof(lj.petshop_fee) == 'number' ? lj.petshop_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
              + `</tr>`;
              ++no1;
          });
          $('#detail-list-jasa').append(rowListJasa);
        } else {
          $('#detail-list-jasa').append('<tr><td colspan="11" class="text-center">Tidak ada data.</td></tr>');
        }

        let rowListDescription = ''; let no = 1;
        $('#detail-list-inpatient tr').remove();
        if (getData.inpatient.length) {
          getData.inpatient.forEach((inp, idx) => {
            rowListDescription += `<tr>`
              + `<td>${no}</td>`
              + `<td>${inp.created_at}</td>`
              + `<td>${inp.created_by}</td>`
              + `<td>${inp.description}</td>`
              + `</tr>`;
              ++no;
          });
          $('#detail-list-inpatient').append(rowListDescription);
        } else {
          $('#detail-list-inpatient').append('<tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>');
        }

        drawListKelompokObatDetail(getData.item);

      }, complete: function() { $('#loading-screen').hide(); },
      error: function(err) {
        if (err.status == 401) {
          localStorage.removeItem('vet-clinic');
          location.href = $('.baseUrl').val() + '/masuk';
        }
      }
    });
  }

  function drawListKelompokObatDetail(listItem) {
    $('#locateDrawKelompokBarang .target').remove();

    if (listItem.length) {
      let rowKelompokObat = ''; let no = 1;

      listItem.forEach((li, idx) => {
        let rowSelectedListBarang = appendListSelectBarang(li.list_of_medicine);

        rowKelompokObat += `<div class="target" style="margin-bottom: 30px">`
        + `<div class="m-b-10px" style="font-weight: 700">Kelompok Obat ${no}</div>`
        + `<div class="m-b-10px">`
        + `${li.group_name} - ${li.branch_name} - ${li.selling_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')}`
        + `</div>`
        + `<div class="table-responsive" id="table-list-barang-${idx}">`
        +   `<table class="table table-striped text-nowrap">`
        +    `<thead>`
        +      `<tr>`
        +        `<th>No</th>`
        +        `<th>Tanggal</th>`
        +        `<th>Dibuat Oleh</th>`
        +        `<th>Nama Barang</th>`
        +        `<th>Kategori Barang</th>`
        +        `<th>Satuan Barang</th>`
        +        `<th>Jumlah</th>`
        +        `<th>Harga Satuan</th>`
        +        `<th>Harga Keseluruhan</th>`
        +        `<th>Harga Modal</th>`
        +        `<th>Fee Dokter</th>`
        +        `<th>Fee Petshop</th>`
        +      `</tr>`
        +    `</thead>`
        +    `<tbody id="list-selected-barang-${idx}" class="list-selected-barang">${rowSelectedListBarang}</tbody>`
        +  `</table>`
        + `</div>`
        + `</div>`;
        ++no;
      });
      $('#locateDrawKelompokBarang').append(rowKelompokObat);

    } else {
      $('#locateDrawKelompokBarang').append(`<div class="target">Tidak ada kelompok obat.</div>`);
    }
  }

  function appendListSelectBarang(arrSelectedListBarang) {
    let = rowSelectedListBarang = ''; let no = 1;

    if (arrSelectedListBarang.length) {
      arrSelectedListBarang.forEach((lb) => {
        rowSelectedListBarang += `<tr>`
          + `<td>${no}</td>`
          + `<td>${lb.created_at ? lb.created_at : '-'}</td>`
          + `<td>${lb.created_by ? lb.created_by : '-'}</td>`
          + `<td>${lb.item_name}</td>`
          + `<td>${lb.category_name}</td>`
          + `<td>${lb.unit_name}</td>`
          + `<td>${lb.quantity}</td>`
          + `<td>${typeof(lb.selling_price) == 'number' ? lb.selling_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
          + `<td>${typeof(lb.price_overall) == 'number' ? lb.price_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
          + `<td>${typeof(lb.capital_price) == 'number' ? lb.capital_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
          + `<td>${typeof(lb.doctor_fee) == 'number' ? lb.doctor_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
          + `<td>${typeof(lb.petshop_fee) == 'number' ? lb.petshop_fee.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
          + `</tr>`;
          ++no;
      });
    } else {
      rowSelectedListBarang += '<tr><td colspan="9" class="text-center">Tidak ada data.</td></tr>';
    }

    return rowSelectedListBarang;
  }

  function refreshText() {
    $('#nomorRegistrasiDetailTxt').text('-'); $('#nomorPasienDetailTxt').text('-');
    $('#jenisHewanDetailTxt').text('-'); $('#namaHewanDetailTxt').text('-');
    $('#jenisKelaminDetailTxt').text('-'); $('#nomorHpPemilikDetailTxt').text('-');
    $('#usiaHewanTahunDetailTxt').text(`- Tahun`); $('#usiaHewanBulanDetailTxt').text(`- Bulan`);
    $('#namaPemilikDetailTxt').text('-'); $('#alamatPemilikDetailTxt').text('-');
    $('#keluhanDetailTxt').text('-'); $('#namaPendaftarDetailTxt').text('-');
    $('#rawatInapDetailTxt').text('-'); $('#statusPemeriksaanDetailTxt').text('-');
    $('#anamnesaDetailTxt').text('-'); $('#diagnosaDetailTxt').text('-');
    $('#signDetailTxt').text('-');
  }

});
