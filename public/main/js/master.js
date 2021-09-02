let username = '';
let fullname = '';
let role = '';
let imageProfile = '';
let email = '';
let token = '';
let userId = '';
let branchId = '';
let branchName = '';

$(document).ready(function() {

  let getAuthUser = localStorage.getItem('vet-clinic');
  if (!getAuthUser) {
    location.href = $('.baseUrl').val() + '/masuk';
  } else {
    getAuthUser = JSON.parse(getAuthUser);

    username     = getAuthUser.username;
    fullname     = getAuthUser.fullname;
    role         = getAuthUser.role.toLowerCase();
    imageProfile = getAuthUser.image_profile;
    userId       = getAuthUser.user_id;
    token        = getAuthUser.token;
    email        = getAuthUser.email;
    branchId     = getAuthUser.branch_id;
    branchName   = getAuthUser.branch_name;

    $('.username-txt').append(username);
    $('.nameAndRole-txt').append(fullname + ' - ' + role + ' - ' + branchName);

    const setUrlImage = `${$('.baseUrl').val()}${imageProfile ? imageProfile : '/assets/image/avatar-default.svg'}`;
    $('.image-header').attr("src", setUrlImage);

    const getUrl = window.location.pathname;
    if (getUrl.includes('profil')) {
      $('.content-header').append('<h3>User Profil</h3>')
    } else if(getUrl == '/') {
      $('.content-header').append('<h2>Selamat datang di Sistem Administrasi Bintang Vet Clinic</h2>')
    }

    if (role === 'admin') {
      $('.menuPasien').show();   $('.menuPendaftaran').show();
      $('.menuDokter').show();   $('.menuTindakan').show();
      $('.menuGudang').show();   $('.menuPembayaran').show();
      $('.menuKeuangan').show(); $('.menuKunjungan').show();
      $('.menuCabang').show();   $('.menuUser').show();
      $('.menuPeriksa').show();  $('.menuPenggajian').show();
    } else if (role === 'resepsionis') {
      $('.menuPasien').show();   $('.menuPendaftaran').show();
      $('.menuTindakan').show(); $('.menuPembayaran').show();
      $('.menuKunjungan').show(); $('.menuGudang').show();
      $('.menuPenggajian').show();
    } else if (role === 'dokter') {
      $('.menuDokter').show();   $('.menuPasien').show();
      $('.menuTindakan').show(); $('.menuGudang').show();
      $('.menuKunjungan').show(); $('.menuPeriksa').show();
      $('.menuPendaftaran').show(); $('.menuKeuangan').show();
      $('.menuPembayaran').show(); $('.menuPenggajian').show();
    }
  }

  // set active class for current page
  const origin = window.location.origin;
  const pathName = window.location.pathname;
  const fullPath = origin + pathName;
  $('.sidebar-menu a').each(function(Key, Value) {

    if ( Value['href'] === fullPath) {
      $(Value).parent().addClass('active');

      if (pathName === '/kategori-barang' || pathName === '/satuan-barang'  || pathName === '/daftar-barang'
        || pathName === '/pembagian-harga-barang' || pathName === '/kelompok-obat' || pathName === '/pembagian-harga-kelompok-obat') {
        $('.menuGudang').addClass('active');
      } else if(pathName === '/kategori-jasa' || pathName === '/daftar-jasa' || pathName === '/pembagian-harga-jasa') {
        $('.menuLayanan').addClass('active');
      } else if (pathName === '/rawat-jalan' || pathName === '/rawat-inap') {
        $('.menuPendaftaran').addClass('active');
      } else if (pathName === '/dokter-rawat-jalan' || pathName === '/dokter-rawat-inap') {
        $('.menuDokter').addClass('active');
      } else if (pathName === '/laporan-keuangan-harian' || pathName === '/laporan-keuangan-mingguan' || pathName === '/laporan-keuangan-bulanan') {
        $('.menuKeuangan').addClass('active');
      }
    } else {
      // additional custom url
      if (Value['href'] ==  origin + '/pembayaran'
        && (pathName == '/pembayaran/tambah' || pathName.includes('/pembayaran/edit') || pathName.includes('/pembayaran/detail'))) {
        $(Value).parent().addClass('active');
      } else if (Value['href'] ==  origin + '/pasien' && pathName.includes('/riwayat-pameriksaan')) {
        $(Value).parent().addClass('active');
      } else if (Value['href'] ==  origin + '/hasil-pemeriksaan' && (pathName == '/hasil-pemeriksaan/tambah'
        || pathName.includes('/hasil-pemeriksaan/edit') || pathName.includes('/hasil-pemeriksaan/detail'))) {
        $(Value).parent().addClass('active');
      } else if ((Value['href'] ==  origin + '/laporan-keuangan-harian' && (pathName.includes('/laporan-keuangan-harian/detail')))
        || (Value['href'] ==  origin + '/laporan-keuangan-mingguan' && (pathName.includes('/laporan-keuangan-mingguan/detail')))
        || (Value['href'] ==  origin + '/laporan-keuangan-bulanan' && (pathName.includes('/laporan-keuangan-bulanan/detail'))) ) {
        $('.menuKeuangan').addClass('active'); $(Value).parent().addClass('active');
      }
    }
  });
  $('#btn-profil').click(function() {
    window.location.href = $('.baseUrl').val() + `/profil/${userId}`;
  });

  $('#btn-logout').click(function() {
    const fd = new FormData();
    fd.append('username', username);

    $.ajax({
      url : $('.baseUrl').val() + '/api/keluar',
      type: 'POST',
      dataType: "json",
      headers: { 'Authorization': `Bearer ${token}` },
      data: fd, contentType: false, cache: false,
      processData: false,
      success:function(resp) {
        localStorage.removeItem('vet-clinic');
        location.href = $('.baseUrl').val() + '/masuk';
      },
      error: function(err) {
        if (err.status == 401) {
          localStorage.removeItem('vet-clinic');
          location.href = $('.baseUrl').val() + '/masuk';
        }
      }
    });
  });

  function showTime() {
    let months  = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    let myDays  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    let date    = new Date();
    let day     = date.getDate();
    let month   = date.getMonth();
    let thisDay = date.getDay();
    let nowDay   = myDays[thisDay];
    let yy      = date.getYear();
    let year    = (yy < 1000) ? yy + 1900 : yy;

    let today       = new Date();
    let curr_hour   = today.getHours();
    let curr_minute = today.getMinutes();
    let curr_second = today.getSeconds();
    let a_p  = (curr_hour < 12) ? "AM" : "PM";

    if (curr_hour == 0) { curr_hour = 12; }
    if (curr_hour > 12) { curr_hour = curr_hour - 12; }

    curr_hour = checkTime(curr_hour);
    curr_minute = checkTime(curr_minute);
    curr_second = checkTime(curr_second);

    let newTime = curr_hour + ":" + curr_minute + ":" + curr_second + " " + a_p;
    let newDay = nowDay + ', ' + day + ' ' + months[month] + ' ' + year;

    // document.getElementById('time').innerHTML= ;

    $('#time-text').text(newDay + ' ' + newTime);
  }

  function checkTime(i) {
    if (i < 10) { i = "0" + i; }
    return i;
  }
  setInterval(showTime, 500);
});

// <script type='text/javascript'>

// 		</script>
