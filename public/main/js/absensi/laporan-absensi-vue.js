const absensiApp = new Vue({
  el: '#absensi-app',
  data: {
    listAbsensi: [],
    listCabang: [],
    listShift: [],
    fotoPreview: '',
    loading: false,
    filter: {
      tanggal_dari: '',
      tanggal_sampai: '',
      branch_id: '',
      shift_id: '',
      status: '',
      keyword: '',
    },
    baseUrl: '',
    token: '',
    role: '',
    branchId: '',
    isAdmin: false,
  },
  mounted() {
    const stored = localStorage.getItem('vet-clinic');
    if (!stored) return;
    const auth = JSON.parse(stored);
    this.token    = auth.token;
    this.role     = auth.role;
    this.branchId = auth.branch_id;
    this.baseUrl  = document.querySelector('.baseUrl').value;
    this.isAdmin  = auth.role === 'admin';

    // Set default filter bulan ini
    const now = new Date();
    const y = now.getFullYear();
    const m = String(now.getMonth() + 1).padStart(2, '0');
    this.filter.tanggal_dari   = y + '-' + m + '-01';
    this.filter.tanggal_sampai = y + '-' + m + '-' + String(new Date(y, now.getMonth() + 1, 0).getDate()).padStart(2, '0');

    if (this.isAdmin) this.loadCabang();
    this.loadShift();
    this.loadAbsensi();
  },
  methods: {
    loadAbsensi() {
      this.loading = true;
      const params = Object.entries(this.filter)
        .filter(([, v]) => v !== '')
        .map(([k, v]) => k + '=' + encodeURIComponent(v))
        .join('&');

      axios.get(this.baseUrl + '/api/absensi?' + params, {
        headers: { Authorization: 'Bearer ' + this.token }
      }).then(res => {
        this.listAbsensi = res.data;
      }).catch(() => {
        this.listAbsensi = [];
      }).finally(() => { this.loading = false; });
    },
    loadCabang() {
      axios.get(this.baseUrl + '/api/cabang', {
        headers: { Authorization: 'Bearer ' + this.token }
      }).then(res => { this.listCabang = res.data; }).catch(() => {});
    },
    loadShift() {
      axios.get(this.baseUrl + '/api/shift', {
        headers: { Authorization: 'Bearer ' + this.token }
      }).then(res => { this.listShift = res.data; }).catch(() => {});
    },
    lihatFoto(url) {
      this.fotoPreview = url;
      $('#modal-foto').modal('show');
    },
    exportExcel() {
      const params = Object.entries(this.filter)
        .filter(([, v]) => v !== '')
        .map(([k, v]) => k + '=' + encodeURIComponent(v))
        .join('&');

      $('#loading-screen').show();

      $.ajax({
        url      : this.baseUrl + '/api/absensi/export?' + params,
        headers  : { 'Authorization': 'Bearer ' + this.token },
        xhrFields: { responseType: 'blob' },
        success  : function (data, status, xhr) {
          const disposition = xhr.getResponseHeader('content-disposition');
          const matches = /"([^"]*)"/.exec(disposition);
          const filename = (matches && matches[1]) ? matches[1] : 'Laporan Absensi.xlsx';
          const blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
          const downloadUrl = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = downloadUrl;
          a.download = filename;
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
        },
        complete : function () { $('#loading-screen').hide(); },
        error    : function (err) {
          $('#loading-screen').hide();
          if (err.status === 401) {
            localStorage.removeItem('vet-clinic');
            location.href = location.origin + '/masuk';
          } else {
            alert('Gagal mengunduh file Excel.');
          }
        },
      });
    },
  },
});
