$(document).ready(function() {

  const url = window.location.pathname;
  const stuff = url.split('/');
  const id = stuff[stuff.length-1];
  let listJasaDetail = [];
  let listBarangDetail = [];
  let listDeskripsiPasien = [];

  refreshText();
  loadRiwayatPasien();

  $('#btnKembali').click(function() {
    window.location.href = $('.baseUrl').val() + '/pasien';
  });

  $('.btn-kembali-modal-riwayat-pemeriksaan').click(function() {
    $('#modal-detail-riwayat-pasien .nav-tabs li').remove();
    $('#modal-detail-riwayat-pasien .nav-tabs').append(`
      <li class="active"><a href="#general" data-toggle="tab">Utama</a></li>
      <li><a href="#kelompok_obat" data-toggle="tab">Obat</a></li>
    `);

    $('#modal-detail-riwayat-pasien #general').addClass('active in');
    $('#modal-detail-riwayat-pasien #kelompok_obat').removeClass('active in');
  });

  function loadRiwayatPasien() {
    $.ajax({
			url     : $('.baseUrl').val() + '/api/pasien/riwayat',
			headers : { 'Authorization': `Bearer ${token}` },
			type    : 'GET',
      data: { patient_id: id },
			beforeSend: function() { $('#loading-screen').show(); },
			success: function(data) {
				let listRiwayatPasien = '';
				$('#list-riwayat-pasien tr').remove();

        if(data.length) {
          $.each(data, function(idx, v) {
            listRiwayatPasien += `<tr>`
              + `<td>${++idx}</td>`
              + `<td>${v.registration_number}</td>`
              + `<td>${v.complaint}</td>`
              + `<td>${v.registrant}</td>`
              + `<td>${v.created_by}</td>`
              + `<td>${v.created_at}</td>`
              + `<td>
                  <button type="button" class="btn btn-info openDetail" title="Detail" value=${v.registration_id}><i class="fa fa-eye" aria-hidden="true"></i></button>
                </td>`
              + `</tr>`;
          });
        } else { listRiwayatPasien += `<tr class="text-center"><td colspan="7">Tidak ada data.</td></tr>` }
				$('#list-riwayat-pasien').append(listRiwayatPasien);

				$('.openDetail').click(function() {
          $('.modal-title').text('Detail Riwayat Pemeriksaan');
          $('#modal-detail-riwayat-pasien').modal('show');

          $.ajax({
            url     : $('.baseUrl').val() + '/api/pasien/detail-riwayat',
            headers : { 'Authorization': `Bearer ${token}` },
            type    : 'GET',
            data    : { patient_registration_id: $(this).val() },
            beforeSend: function() { $('#loading-screen').show(); },
            success: function(data) {
              $('#nomorRegistrasiTxt').text(data.registration_number);
              $('#anamnesaTxt').text(data.anamnesa); $('#signTxt').text(data.sign);
              $('#diagnosaTxt').text(data.diagnosa);
              $('#rawatInapTxt').text(data.status_outpatient_inpatient ? 'Ya' : 'Tidak'); $('#statusPemeriksaanTxt').text(data.status_finish ? 'Selesai' : 'Belum');
              listJasaDetail = data.services; listBarangDetail = data.item;
              listDeskripsiPasien = data.inpatient;

              processAppendListJasaDetail(); processAppendListBarangDetail();
              processAppendListDeskripsiKondisiPasien();

              drawListKelompokObatDetail(data.item);

            }, complete: function() { $('#loading-screen').hide(); },
            error: function(err) {
              if (err.status == 401) {
                localStorage.removeItem('vet-clinic');
                location.href = $('.baseUrl').val() + '/masuk';
              }
            }
          });
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

  function processAppendListDeskripsiKondisiPasien() {
    let rowListDeksripsiKondPasien = '';
    let no = 1;
    $('#list-deskripsi-kondisi-pasien tr').remove();

    if (listDeskripsiPasien.length) {
      listDeskripsiPasien.forEach((desc) => {
        rowListDeksripsiKondPasien += `<tr>`
          + `<td>${no}</td>`
          + `<td>${desc.created_at}</td>`
          + `<td>${desc.created_by}</td>`
          + `<td>${desc.description}</td>`
          + `</tr>`;
          ++no;
      });
    } else {
      rowListDeksripsiKondPasien += `<tr class="text-center"><td colspan="4">Tidak ada data.</td></tr>`;
    }
    $('#list-deskripsi-kondisi-pasien').append(rowListDeksripsiKondPasien);
  }

  function processAppendListJasaDetail() {
    let rowListJasaDetail = '';
    let no = 1;
    $('#detail-list-jasa tr').remove();

    if(listJasaDetail.length) {
      listJasaDetail.forEach((lj) => {
        rowListJasaDetail += `<tr>`
          + `<td>${no}</td>`
          + `<td>${lj.created_at}</td>`
          + `<td>${lj.created_by}</td>`
          + `<td>${lj.category_name}</td>`
          + `<td>${lj.service_name}</td>`
          + `<td>${
            Number(lj.price_overall || 0).toLocaleString('id-ID')
            // typeof(lj.price_overall) == 'number' ? lj.price_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''
          }</td>`
          + `</tr>`;
          ++no;
      });
    } else {
      rowListJasaDetail += `<tr class="text-center"><td colspan="6">Tidak ada data.</td></tr>`;
    }
    $('#detail-list-jasa').append(rowListJasaDetail);
  }

  function processAppendListBarangDetail() {
    let rowListBarangDetail = '';
    let no = 1;
    $('#detail-list-barang tr').remove();

    if (listBarangDetail.length) {
      listBarangDetail.forEach((lb) => {
        rowListBarangDetail += `<tr>`
          + `<td>${no}</td>`
          + `<td>${lb.created_at}</td>`
          + `<td>${lb.created_by}</td>`
          + `<td>${lb.item_name}</td>`
          + `<td>${lb.category_name}</td>`
          + `<td>${lb.unit_name}</td>`
          + `<td>${typeof(lb.price_overall) == 'number' ? lb.price_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
          + `</tr>`;
          ++no;
      });
    } else {
      rowListBarangDetail += `<tr class="text-center"><td colspan="7">Tidak ada data.</td></tr>`;
    }
    $('#detail-list-barang').append(rowListBarangDetail);
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
          + `</tr>`;
          ++no;
      });
    } else {
      rowSelectedListBarang += '<tr><td colspan="9" class="text-center">Tidak ada data.</td></tr>';
    }

    return rowSelectedListBarang;
  }

  function refreshText() {
    $('#nomorRegistrasiTxt').text('-'); $('#diagnosaTxt').text('-');
    $('#anamnesaTxt').text('-'); $('#signTxt').text('-');
    $('#rawatInapTxt').text('-'); $('#statusPemeriksaanTxt').text('-');
  }

});
