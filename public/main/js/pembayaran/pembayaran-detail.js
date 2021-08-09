$(document).ready(function() {

  const url = window.location.pathname;
  const stuff = url.split('/');
  const id = stuff[stuff.length-1];

  let selectedListJasa = [];
  let selectedListBarang = [];
  let listTagihanJasa = [];
  let listTagihanBarang = [];
  let calculationPay = [];

  // if (role.toLowerCase() == 'dokter') {
	// 	window.location.href = $('.baseUrl').val() + `/unauthorized`;
	// }

  $('.btn-back-to-list .text, #btnKembali').click(function() {
    window.location.href = $('.baseUrl').val() + '/pembayaran';
  });

  $.ajax({
    url     : $('.baseUrl').val() + '/api/pembayaran/detail',
    headers : { 'Authorization': `Bearer ${token}` },
    type    : 'GET',
    data	  : { list_of_payment_id: id },
    beforeSend: function() { $('#loading-screen').show(); },
    success: function(data) {

      $('#nomorPasienTxt').text(data.registration.patient_number); $('#jenisHewanTxt').text(data.registration.pet_category);
      $('#namaHewanTxt').text(data.registration.pet_name); $('#jenisKelaminTxt').text(data.registration.pet_gender);
      $('#usiaHewanTahunTxt').text(`${data.registration.pet_year_age} Tahun`); $('#usiaHewanBulanTxt').text(`${data.registration.pet_month_age} Bulan`);
      $('#namaPemilikTxt').text(data.registration.owner_name); $('#alamatPemilikTxt').text(data.registration.owner_address);
      $('#nomorHpPemilikTxt').text(data.registration.owner_phone_number); $('#nomorRegistrasiTxt').text(data.registration.registration_number);
      $('#keluhanTxt').text(data.registration.complaint); $('#namaPendaftarTxt').text(data.registration.registrant);
      $('#rawatInapTxt').text(data.status_outpatient_inpatient ? 'Ya' : 'Tidak'); $('#statusPemeriksaanTxt').text(data.status_finish ? 'Selesai' : 'Belum');

      selectedListJasa = data.services; selectedListBarang = data.item;
      processAppendListSelectedJasa(); processAppendListSelectedBarang();

      listTagihanJasa = data.paid_services; listTagihanBarang = data.paid_item;
      processAppendListTagihanJasa(); processAppendListTagihanBarang();

      listTagihanJasa.forEach(tj => calculationPay.push(tj));
      listTagihanBarang.forEach(tb => calculationPay.push(tb));
      processCalculationTagihan(data);

    }, complete: function() { $('#loading-screen').hide(); },
    error: function(err) {
      if (err.status == 401) {
        localStorage.removeItem('vet-clinic');
        location.href = $('.baseUrl').val() + '/masuk';
      }
    }
  });

  function processAppendListSelectedJasa() {
    let rowSelectedListJasa = '';
    let no = 1;
    $('#list-selected-jasa tr').remove();

    selectedListJasa.forEach((lj, idx) => {
      rowSelectedListJasa += `<tr>`
        + `<td>${no}</td>`
        + `<td>${lj.created_at}</td>`
        + `<td>${lj.created_by}</td>`
        + `<td>${lj.category_name}</td>`
        + `<td>${lj.service_name}</td>`
        + `<td>${lj.quantity}</td>`
        + `<td>${typeof(lj.selling_price) == 'number' ? lj.selling_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
        + `<td>${typeof(lj.price_overall) == 'number' ? lj.price_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
        + `<td>${lj.status_paid_off ? 'Lunas' : 'Belum Lunas'}</td>`
        + `</tr>`;
        ++no;
    });
    $('#list-selected-jasa').append(rowSelectedListJasa);
  }

  function processAppendListTagihanJasa() {
    let rowListTagihanJasa = '';
    let no = 1;
    $('#list-tagihan-jasa tr').remove();

    if (listTagihanJasa.length) {
      listTagihanJasa.forEach((lj) => {
        rowListTagihanJasa += `<tr>`
          + `<td>${no}</td>`
          + `<td>${lj.created_at}</td>`
          + `<td>${lj.created_by}</td>`
          + `<td>${lj.category_name}</td>`
          + `<td>${lj.service_name}</td>`
          + `<td>${lj.quantity}</td>`
          + `<td>${typeof(lj.selling_price) == 'number' ? lj.selling_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
          + `<td>${typeof(lj.price_overall) == 'number' ? lj.price_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
          + `</tr>`;
          ++no;
      });
    } else { rowListTagihanJasa += `<tr class="text-center"><td colspan="8">Tidak ada data.</td></tr>` }
    $('#list-tagihan-jasa').append(rowListTagihanJasa);
  }

  function processAppendListSelectedBarang() {
    let rowSelectedListBarang = '';
    let no = 1;

    $('#list-selected-barang tr').remove();
    selectedListBarang.forEach((lb, idx) => {
      rowSelectedListBarang += `<tr>`
        + `<td>${no}</td>`
        + `<td>${lb.created_at}</td>`
        + `<td>${lb.created_by}</td>`
        + `<td>${lb.group_name}</td>`
        + `<td>${lb.quantity}</td>`
        + `<td>${typeof(lb.each_price) == 'number' ? lb.each_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
        + `<td>${typeof(lb.price_overall) == 'number' ? lb.price_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
        + `<td>${lb.status_paid_off ? 'Lunas' : 'Belum Lunas'}</td>`
        + `</tr>`;
        ++no;
    });
    $('#list-selected-barang').append(rowSelectedListBarang);
  }

  function processAppendListTagihanBarang() {
    let rowListTagihanBarang = '';
    let no = 1;
    $('#list-tagihan-barang tr').remove();

    if (listTagihanBarang.length) {
      listTagihanBarang.forEach((lb) => {
        rowListTagihanBarang += `<tr>`
          + `<td>${no}</td>`
          + `<td>${lb.created_at}</td>`
          + `<td>${lb.created_by}</td>`
          + `<td>${lb.group_name}</td>`
          + `<td>${lb.quantity}</td>`
          + `<td>${typeof(lb.each_price) == 'number' ? lb.each_price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
          + `<td>${typeof(lb.price_overall) == 'number' ? lb.price_overall.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''}</td>`
          + `</tr>`;
          ++no;
      });
    } else { rowListTagihanBarang += `<tr class="text-center"><td colspan="9">Tidak ada data.</td></tr>` }
    $('#list-tagihan-barang').append(rowListTagihanBarang);
  }

  function processCalculationTagihan(data) {
    if (!data.status_paid_off) {
      let total = 0;

      calculationPay.forEach(calc => total += calc.price_overall );

      let totalText = `Rp. ${total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')},00`;
      $('#label-tagihan').text('Total tagihan');
      $('#totalBayarTxt').text(totalText);
    } else {
      $('#label-tagihan').text('Status tagihan');
      $('#totalBayarTxt').text('Lunas');
    }
  }

});
