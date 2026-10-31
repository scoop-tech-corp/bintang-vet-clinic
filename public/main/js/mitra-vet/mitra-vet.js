$(document).ready(function () {
    let currentPageStok = 1;
    let currentPageOrder = 1;
    let selectedItemId = null;
    let selectedOrderId = null;
    let modalActionState = ""; // 'add-item', 'edit-item', 'add-stock', 'delete-item', 'delete-order'
    let orderItemsList = []; // items in current draft order
    let availableItemsDropdown = []; // items loaded for dropdown in order modal
    let availableCustomersList = []; // customers list for order modal dropdown

    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;
    const startYear = 2021; // Tahun awal operasional data

    // Inisialisasi dropdown filter tahun secara dinamis (fleksibel)
    function initYearFilters() {
        let chartYearOptions = '';
        let orderYearOptions = '<option value="">-- Semua Tahun --</option>';

        // Loop dari tahun berjalan (+1 untuk antisipasi) mundur sampai tahun awal
        for (let y = currentYear + 1; y >= startYear; y--) {
            chartYearOptions += `<option value="${y}" ${y === currentYear ? 'selected' : ''}>${y}</option>`;
            orderYearOptions += `<option value="${y}">${y}</option>`;
        }

        $('#filter-chart-year').html(chartYearOptions).val(currentYear);
        $('#filter-order-year').html(orderYearOptions); // default semua tahun agar riwayat selalu terlihat
        $('#filter-chart-month').val(currentMonth);
    }
    initYearFilters();

    // Helper format Rupiah
    function formatRupiah(val) {
        if (val === null || val === undefined || isNaN(val)) return 'Rp 0';
        return 'Rp ' + parseFloat(val).toLocaleString('id-ID');
    }

    // Validasi Akses Role:
    // Admin & Dokter: Bisa Tambah Barang & Order Barang
    // Resepsionis: Hanya Bisa Order Barang
    // Selain itu: Ditolak / Redirect
    const userRole = (typeof role !== 'undefined' ? role.toLowerCase() : '');

    if (userRole === 'resepsionis') {
        // Resepsionis tidak dapat menambah barang maupun menambah stok
        $('#btn-tambah-barang-wrap').hide();
        $('a[href="#tab-tambah-barang"]').parent().hide();
        $('#tab-stok .section-left-box-title').hide();

        // Otomatis aktifkan tab Orderan sebagai default untuk resepsionis
        $('a[href="#tab-orderan"]').tab('show');
    }

    // =========================================================================
    // 1. STOK BARANG (TAB 1)
    // =========================================================================

    function loadStokBarang() {
        const keyword = $('#search-stok').val();
        $('#list-stok-mitra-vet').html('<tr class="text-center"><td colspan="7">Memuat data stok...</td></tr>');

        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/items',
            type: 'GET',
            dataType: 'JSON',
            headers: { Authorization: `Bearer ${token}` },
            data: {
                page: currentPageStok,
                keyword: keyword
            },
            success: function (resp) {
                let rows = '';
                const data = resp.data || [];

                $('#total-jenis-barang').text(data.length > 0 ? (resp.total_paging > 1 ? `${data.length}+` : data.length) : '0');
                $('#total-nilai-aset').text(formatRupiah(resp.total_nilai_aset));
                $('#tfoot-total-aset').text(formatRupiah(resp.total_nilai_aset));

                if (data.length === 0) {
                    rows = '<tr class="text-center"><td colspan="7">Belum ada data barang.</td></tr>';
                } else {
                    data.forEach(function (item, index) {
                        const no = (currentPageStok - 1) * 50 + (index + 1);
                        const nilaiAset = parseFloat(item.harga_satuan) * parseInt(item.stok);
                        const stokBadge = item.stok <= 5
                            ? `<span class="label label-danger">${item.stok}</span>`
                            : `<span class="label label-success">${item.stok}</span>`;

                        let actionBtns = '';

                        // Hanya Admin dan Dokter yang dapat Menambah Stok dan Mengedit Barang
                        if (userRole === 'admin' || userRole === 'dokter') {
                            actionBtns += `
                                <button class="btn btn-xs btn-success btn-quick-stock" data-id="${item.id}" data-nama="${item.nama_barang}" data-stok="${item.stok}" title="Tambah Stok">
                                    <i class="fa fa-plus"></i> Stok
                                </button>
                                <button class="btn btn-xs btn-warning btn-edit-item" data-id="${item.id}" data-nama="${item.nama_barang}" data-satuan="${item.satuan}" data-harga="${item.harga_satuan}" title="Edit Barang">
                                    <i class="fa fa-pencil"></i>
                                </button>
                            `;
                        }

                        // Hanya Admin yang dapat Menghapus Barang
                        if (userRole === 'admin') {
                            actionBtns += `
                                <button class="btn btn-xs btn-danger btn-delete-item" data-id="${item.id}" data-nama="${item.nama_barang}" title="Hapus Barang">
                                    <i class="fa fa-trash"></i>
                                </button>
                            `;
                        }

                        if (!actionBtns) {
                            actionBtns = '<span class="text-muted">-</span>';
                        }

                        rows += `
                            <tr>
                                <td>${no}</td>
                                <td><strong>${item.nama_barang}</strong></td>
                                <td>${item.satuan}</td>
                                <td>${stokBadge}</td>
                                <td>${formatRupiah(item.harga_satuan)}</td>
                                <td><strong>${formatRupiah(nilaiAset)}</strong></td>
                                <td>${actionBtns}</td>
                            </tr>
                        `;
                    });
                }

                $('#list-stok-mitra-vet').html(rows);
                renderPagination('#pagination-stok', resp.total_paging, currentPageStok, function (p) {
                    currentPageStok = p;
                    loadStokBarang();
                });
            },
            error: function (err) {
                if (err.status === 401) {
                    localStorage.removeItem('vet-clinic');
                    location.href = $('.baseUrl').val() + '/masuk';
                }
                $('#list-stok-mitra-vet').html('<tr class="text-center text-danger"><td colspan="7">Gagal memuat data.</td></tr>');
            }
        });
    }

    // Search stok
    $('#search-stok').on('keyup', function (e) {
        if (e.which === 13) {
            currentPageStok = 1;
            loadStokBarang();
        }
    });
    $('#tab-stok .fa-search').on('click', function () {
        currentPageStok = 1;
        loadStokBarang();
    });

    // Tombol Edit Item (Modal)
    $(document).on('click', '.btn-edit-item', function () {
        modalActionState = 'edit-item';
        selectedItemId = $(this).data('id');
        $('#modal-tambah-barang-title').text('Ubah Data Barang');
        $('#input-nama-barang').val($(this).data('nama'));
        $('#input-satuan').val($(this).data('satuan'));
        $('#input-harga-satuan').val($(this).data('harga'));
        $('#wrap-stok-awal').hide();
        $('.validate-error').empty();
        $('#modal-tambah-barang').modal('show');
    });

    // Simpan Edit Barang dari Modal
    $('#btnSimpanBarang').on('click', function () {
        $('.validate-error').empty();
        const nama = $('#input-nama-barang').val().trim();
        const satuan = $('#input-satuan').val().trim();
        const harga = $('#input-harga-satuan').val().trim();

        let hasError = false;
        if (!nama) { $('#err-nama-barang').text('Nama barang wajib diisi'); hasError = true; }
        if (!satuan) { $('#err-satuan').text('Satuan wajib diisi'); hasError = true; }
        if (!harga || isNaN(harga)) { $('#err-harga-satuan').text('Harga harus angka valid'); hasError = true; }
        if (hasError) return;

        $('#btnSimpanBarang').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/items',
            type: 'PUT',
            dataType: 'JSON',
            headers: { Authorization: `Bearer ${token}` },
            data: {
                id: selectedItemId,
                nama_barang: nama,
                satuan: satuan,
                harga_satuan: harga
            },
            success: function (resp) {
                $('#modal-tambah-barang').modal('hide');
                showNotice('Sukses', resp.message || 'Data barang berhasil diubah.');
                loadStokBarang();
                loadChart();
            },
            error: function (err) {
                handleAjaxError(err, '#err-global-barang');
            },
            complete: function () {
                $('#btnSimpanBarang').prop('disabled', false).text('Simpan');
            }
        });
    });

    // Tombol Tambah Stok Cepat
    $(document).on('click', '.btn-quick-stock', function () {
        selectedItemId = $(this).data('id');
        $('#stok-nama-barang-display').val($(this).data('nama'));
        $('#stok-current').val($(this).data('stok'));
        $('#stok-tambah-qty').val('');
        $('#stok-tambah-keterangan').val('');
        $('.validate-error').empty();
        $('#modal-tambah-stok').modal('show');
    });

    // Submit Tambah Stok
    $('#btnSimpanTambahStok').on('click', function () {
        $('.validate-error').empty();
        const qty = $('#stok-tambah-qty').val();
        const ket = $('#stok-tambah-keterangan').val();

        if (!qty || parseInt(qty) <= 0) {
            $('#err-stok-tambah-qty').text('Jumlah stok harus angka bulat lebih dari 0');
            return;
        }

        $('#btnSimpanTambahStok').prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/tambah-stok',
            type: 'POST',
            dataType: 'JSON',
            headers: { Authorization: `Bearer ${token}` },
            data: {
                item_id: selectedItemId,
                quantity: qty,
                keterangan: ket
            },
            success: function (resp) {
                $('#modal-tambah-stok').modal('hide');
                showNotice('Sukses', resp.message || 'Stok berhasil ditambahkan.');
                loadStokBarang();
                loadChart();
            },
            error: function (err) {
                handleAjaxError(err, '#err-global-stok');
            },
            complete: function () {
                $('#btnSimpanTambahStok').prop('disabled', false).text('Tambah Stok');
            }
        });
    });

    // Tombol Hapus Item
    $(document).on('click', '.btn-delete-item', function () {
        selectedItemId = $(this).data('id');
        const nama = $(this).data('nama');
        modalActionState = 'delete-item';
        $('#modal-confirmation .box-body').html(`Apakah Anda yakin ingin menghapus barang <strong>"${nama}"</strong>?`);
        $('#modal-confirmation').modal('show');
    });

    // Download Log Penambahan Stok ke Excel
    $('#btnExportStockLog').on('click', function () {
        const month = $('#filter-chart-month').val();
        const year = $('#filter-chart-year').val();
        const keyword = $('#search-stok').val();

        let filename = 'Log Penambahan Stok Mitra Vet.xlsx';

        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/stock-histories/export-excel',
            headers: { Authorization: `Bearer ${token}` },
            type: 'GET',
            xhrFields: { responseType: 'blob' },
            data: { month: month, year: year, keyword: keyword },
            beforeSend: function () {
                if ($('#loading-screen').length) $('#loading-screen').show();
            },
            success: function (data, status, xhr) {
                let disposition = xhr.getResponseHeader('content-disposition');
                if (disposition) {
                    let matches = /"([^"]*)"/.exec(disposition);
                    if (matches != null && matches[1]) filename = matches[1];
                }
                let blob = new Blob([data], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                let downloadUrl = URL.createObjectURL(blob);
                let a = document.createElement("a");
                a.href = downloadUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                setTimeout(() => {
                    document.body.removeChild(a);
                    URL.revokeObjectURL(downloadUrl);
                }, 100);
            },
            complete: function () {
                if ($('#loading-screen').length) $('#loading-screen').hide();
            },
            error: function (err) {
                if (err.status === 401) {
                    localStorage.removeItem('vet-clinic');
                    location.href = $('.baseUrl').val() + '/masuk';
                } else if (err.status === 403) {
                    showNotice('Akses Ditolak', 'Hanya Admin dan Dokter yang dapat mendownload log stok.');
                } else {
                    showNotice('Error', 'Gagal mendownload log penambahan stok.');
                }
            }
        });
    });

    // =========================================================================
    // 2. TAB TAMBAH BARANG (TAB 2)
    // =========================================================================

    // A. Simpan Barang Manual dari Tab
    $('#btnSimpanTabBarang').on('click', function () {
        $('#err-tab-nama, #err-tab-satuan, #err-tab-harga, #err-tab-stok, #err-tab-global').empty();
        const nama = $('#tab-nama-barang').val().trim();
        const satuan = $('#tab-satuan').val().trim();
        const harga = $('#tab-harga-satuan').val().trim();
        const stokAwal = $('#tab-stok-awal').val().trim();

        let hasError = false;
        if (!nama) { $('#err-tab-nama').text('Nama barang wajib diisi'); hasError = true; }
        if (!satuan) { $('#err-tab-satuan').text('Satuan wajib diisi'); hasError = true; }
        if (!harga || isNaN(harga)) { $('#err-tab-harga').text('Harga harus angka valid'); hasError = true; }
        if (stokAwal === '' || isNaN(stokAwal) || parseInt(stokAwal) < 0) {
            $('#err-tab-stok').text('Stok awal wajib diisi (>= 0)');
            hasError = true;
        }
        if (hasError) return;

        $('#btnSimpanTabBarang').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/items',
            type: 'POST',
            dataType: 'JSON',
            headers: { Authorization: `Bearer ${token}` },
            data: {
                nama_barang: nama,
                satuan: satuan,
                harga_satuan: harga,
                stok: stokAwal
            },
            success: function (resp) {
                $('#form-tab-tambah-barang')[0].reset();
                showNotice('Sukses', resp.message || 'Barang baru berhasil ditambahkan.');
                loadStokBarang();
                loadChart();
            },
            error: function (err) {
                handleAjaxError(err, '#err-tab-global');
            },
            complete: function () {
                $('#btnSimpanTabBarang').prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Barang');
            }
        });
    });

    // B. Download Template Excel
    $(document).on('click', '.btn-download-template-act', function () {
        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/items/download-template',
            headers: { Authorization: `Bearer ${token}` },
            type: 'GET',
            xhrFields: { responseType: 'blob' },
            beforeSend: function () {
                if ($('#loading-screen').length) $('#loading-screen').show();
            },
            success: function (data, status, xhr) {
                let filename = 'Template Produk Mitra Vet.xlsx';
                let disposition = xhr.getResponseHeader('content-disposition');
                if (disposition) {
                    let matches = /"([^"]*)"/.exec(disposition);
                    if (matches != null && matches[1]) filename = matches[1];
                }
                let blob = new Blob([data], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                let downloadUrl = URL.createObjectURL(blob);
                let a = document.createElement("a");
                a.href = downloadUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                setTimeout(() => {
                    document.body.removeChild(a);
                    URL.revokeObjectURL(downloadUrl);
                }, 100);
            },
            complete: function () {
                if ($('#loading-screen').length) $('#loading-screen').hide();
            },
            error: function (err) {
                if (err.status === 401) {
                    localStorage.removeItem('vet-clinic');
                    location.href = $('.baseUrl').val() + '/masuk';
                } else {
                    showNotice('Error', 'Gagal mendownload template Excel.');
                }
            }
        });
    });

    // C. Upload & Proses Excel dari Tab
    $('#btnTabProsesUpload').on('click', function () {
        $('#err-tab-upload-file').empty();
        $('#tab-upload-result').hide().empty();

        const fileInput = document.getElementById('tab-file-excel');
        if (!fileInput.files.length) {
            $('#err-tab-upload-file').text('Pilih file Excel terlebih dahulu (.xlsx / .xls)');
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        $('#tab-upload-progress').show();
        $('#btnTabProsesUpload').prop('disabled', true);

        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/items/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { Authorization: `Bearer ${token}` },
            success: function (resp) {
                $('#tab-upload-progress').hide();
                $('#btnTabProsesUpload').prop('disabled', false);
                $('#tab-file-excel').val('');
                let successHtml = `
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> <strong>${resp.message || 'Upload berhasil diproses!'}</strong>
                `;
                if (resp.updated_items && resp.updated_items.length > 0) {
                    successHtml += `<div style="margin-top:6px; font-size:12px;"><strong>Barang yang bertambah stoknya:</strong> ${resp.updated_items.join(', ')}</div>`;
                }
                if (resp.inserted_items && resp.inserted_items.length > 0) {
                    successHtml += `<div style="margin-top:4px; font-size:12px;"><strong>Barang baru ditambahkan:</strong> ${resp.inserted_items.join(', ')}</div>`;
                }
                successHtml += `</div>`;
                $('#tab-upload-result').html(successHtml).show();
                showNotice('Berhasil', resp.message || 'Data Excel berhasil diproses.');
                loadStokBarang();
                loadChart();
            },
            error: function (err) {
                $('#tab-upload-progress').hide();
                $('#btnTabProsesUpload').prop('disabled', false);
                let errHtml = '<div class="alert alert-danger"><strong>Gagal mengunggah file:</strong><ul>';
                if (err.responseJSON && err.responseJSON.errors) {
                    err.responseJSON.errors.forEach(function (e) {
                        errHtml += `<li>${e}</li>`;
                    });
                } else if (err.responseJSON && err.responseJSON.message) {
                    errHtml += `<li>${err.responseJSON.message}</li>`;
                } else {
                    errHtml += `<li>Terjadi kesalahan jaringan atau server.</li>`;
                }
                errHtml += '</ul></div>';
                $('#tab-upload-result').html(errHtml).show();
            }
        });
    });

    // =========================================================================
    // 3. ORDERAN & NOTA (TAB 3)
    // =========================================================================

    function loadOrders() {
        const keyword = $('#search-order').val();
        const month = $('#filter-order-month').val();
        const year = $('#filter-order-year').val();

        $('#list-orderan-mitra-vet').html('<tr class="text-center"><td colspan="10">Memuat data orderan...</td></tr>');

        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/orders',
            type: 'GET',
            dataType: 'JSON',
            headers: { Authorization: `Bearer ${token}` },
            data: {
                page: currentPageOrder,
                keyword: keyword,
                month: month,
                year: year
            },
            success: function (resp) {
                let rows = '';
                const data = resp.data || [];

                if (data.length === 0) {
                    rows = '<tr class="text-center"><td colspan="11">Belum ada orderan.</td></tr>';
                } else {
                    data.forEach(function (order, index) {
                        const no = (currentPageOrder - 1) * 50 + (index + 1);

                        // Format rincian barang yang diorder
                        let detailItemsHtml = '<span class="text-muted">-</span>';
                        if (order.items && order.items.length > 0) {
                            let itemsList = [];
                            order.items.forEach(function (it) {
                                let nama = it.nama_barang || 'Barang';
                                let satuan = it.satuan ? ` ${it.satuan}` : '';
                                itemsList.push(`<div><i class="fa fa-caret-right text-info"></i> <strong>${nama}</strong> <span class="label label-default" style="font-size:11px;">${it.quantity}${satuan}</span></div>`);
                            });
                            detailItemsHtml = `<div style="font-size:12px; line-height:1.6; text-align:left;">${itemsList.join('')}</div>`;
                        }

                        let actionBtns = `
                            <a href="${$('.baseUrl').val()}/mitra-vet/nota/${order.id}" target="_blank" class="btn btn-xs btn-primary" title="Cetak Nota">
                                <i class="fa fa-print"></i> Nota
                            </a>
                        `;

                        if (typeof role !== 'undefined' && role.toLowerCase() === 'admin') {
                            actionBtns += `
                                <button class="btn btn-xs btn-danger btn-delete-order" data-id="${order.id}" data-no="${order.nomor_order}" title="Batalkan & Hapus Order">
                                    <i class="fa fa-trash"></i>
                                </button>
                            `;
                        }

                        rows += `
                            <tr>
                                <td>${no}</td>
                                <td><span class="label label-info">${order.nomor_order}</span></td>
                                <td><strong>${order.nama_pembeli}</strong></td>
                                <td>${order.no_hp}</td>
                                <td>${detailItemsHtml}</td>
                                <td>${formatRupiah(order.total_harga)}</td>
                                <td>${formatRupiah(order.biaya_ongkir)}</td>
                                <td><strong>${formatRupiah(order.grand_total)}</strong></td>
                                <td>${order.created_by || '-'}</td>
                                <td>${order.created_at}</td>
                                <td>${actionBtns}</td>
                            </tr>
                        `;
                    });
                }

                $('#list-orderan-mitra-vet').html(rows);
                renderPagination('#pagination-orderan', resp.total_paging, currentPageOrder, function (p) {
                    currentPageOrder = p;
                    loadOrders();
                });
            },
            error: function () {
                $('#list-orderan-mitra-vet').html('<tr class="text-center text-danger"><td colspan="11">Gagal memuat orderan.</td></tr>');
            }
        });
    }

    $('#search-order').on('keyup', function (e) {
        if (e.which === 13) {
            currentPageOrder = 1;
            loadOrders();
        }
    });

    $('#filter-order-month, #filter-order-year').on('change', function () {
        currentPageOrder = 1;
        loadOrders();
    });

    // Export Excel Orderan (Disort berdasarkan Nama Pembeli)
    $('#btnExportOrderExcel').on('click', function () {
        const month = $('#filter-order-month').val();
        const year = $('#filter-order-year').val();
        const keyword = $('#search-order').val();

        let filename = 'Laporan Orderan Mitra Vet.xlsx';

        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/orders/export-excel',
            headers: { Authorization: `Bearer ${token}` },
            type: 'GET',
            xhrFields: { responseType: 'blob' },
            data: { month: month, year: year, keyword: keyword },
            beforeSend: function () {
                if ($('#loading-screen').length) $('#loading-screen').show();
            },
            success: function (data, status, xhr) {
                let disposition = xhr.getResponseHeader('content-disposition');
                if (disposition) {
                    let matches = /"([^"]*)"/.exec(disposition);
                    if (matches != null && matches[1]) filename = matches[1];
                }
                let blob = new Blob([data], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                let downloadUrl = URL.createObjectURL(blob);
                let a = document.createElement("a");
                a.href = downloadUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                setTimeout(() => {
                    document.body.removeChild(a);
                    URL.revokeObjectURL(downloadUrl);
                }, 100);
            },
            complete: function () {
                if ($('#loading-screen').length) $('#loading-screen').hide();
            },
            error: function (err) {
                if (err.status === 401) {
                    localStorage.removeItem('vet-clinic');
                    location.href = $('.baseUrl').val() + '/masuk';
                } else {
                    showNotice('Error', 'Gagal mendownload data orderan.');
                }
            }
        });
    });

    // Inisialisasi Select2 pencarian pada dropdown Mitra & Barang di dalam Modal Order
    function initOrderSelect2() {
        $('#order-select-customer').select2({
            placeholder: '-- Cari & Pilih Pelanggan/Mitra --',
            allowClear: true,
            dropdownParent: $('#modal-order'),
            width: '100%'
        });

        $('#order-select-item').select2({
            placeholder: '-- Cari & Pilih Barang --',
            allowClear: true,
            dropdownParent: $('#modal-order'),
            width: '100%'
        });
    }

    // Load customer dropdown untuk modal orderan
    function loadCustomerDropdown(selectCustomerId = null) {
        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/customers',
            type: 'GET',
            dataType: 'JSON',
            headers: { Authorization: `Bearer ${token}` },
            success: function (customers) {
                availableCustomersList = customers;
                let opt = '<option value="">-- Cari & Pilih Pelanggan/Mitra --</option>';
                customers.forEach(function (c) {
                    opt += `<option value="${c.id}">${c.nama}</option>`;
                });
                $('#order-select-customer').html(opt);
                initOrderSelect2();

                if (selectCustomerId) {
                    $('#order-select-customer').val(selectCustomerId).trigger('change');
                }
            }
        });
    }

    // Auto-fill No HP dan Alamat saat Pelanggan dipilih dari Dropdown
    $('#order-select-customer').on('change', function () {
        const custId = parseInt($(this).val());
        if (!custId) {
            $('#order-no-hp').val('');
            $('#order-alamat').val('');
            return;
        }

        const found = availableCustomersList.find(c => c.id === custId);
        if (found) {
            $('#order-no-hp').val(found.no_hp);
            $('#order-alamat').val(found.alamat);
        }
    });

    // Buka Modal Tambah Customer Baru Cepat
    $('#btnBukaModalCustomer').on('click', function () {
        $('#form-tambah-customer')[0].reset();
        $('#err-cust-nama, #err-cust-hp, #err-cust-alamat, #err-cust-global').empty();
        $('#modal-tambah-customer').modal('show');
    });

    // Simpan Customer Baru
    $('#btnSimpanCustomerBaru').on('click', function () {
        $('#err-cust-nama, #err-cust-hp, #err-cust-alamat, #err-cust-global').empty();
        const nama = $('#cust-nama').val().trim();
        const hp = $('#cust-hp').val().trim();
        const alamat = $('#cust-alamat').val().trim();

        let hasError = false;
        if (!nama) { $('#err-cust-nama').text('Nama wajib diisi'); hasError = true; }
        if (!hp) { $('#err-cust-hp').text('No HP wajib diisi'); hasError = true; }
        if (!alamat) { $('#err-cust-alamat').text('Alamat wajib diisi'); hasError = true; }
        if (hasError) return;

        $('#btnSimpanCustomerBaru').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/customers',
            type: 'POST',
            dataType: 'JSON',
            headers: { Authorization: `Bearer ${token}` },
            data: { nama: nama, no_hp: hp, alamat: alamat },
            success: function (resp) {
                $('#modal-tambah-customer').modal('hide');
                loadCustomerDropdown(resp.customer.id);
            },
            error: function (err) {
                handleAjaxError(err, '#err-cust-global');
            },
            complete: function () {
                $('#btnSimpanCustomerBaru').prop('disabled', false).html('<i class="fa fa-check"></i> Simpan & Pilih');
            }
        });
    });

    // Form Order Baru
    $('#btnBuatOrder').on('click', function () {
        orderItemsList = [];
        $('#form-order')[0].reset();
        $('#order-ongkir').val('0');
        renderOrderItemsTable();
        $('.validate-error').empty();

        // Load item dropdown
        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/items/dropdown',
            type: 'GET',
            dataType: 'JSON',
            headers: { Authorization: `Bearer ${token}` },
            success: function (items) {
                availableItemsDropdown = items;
                let opt = '<option value="">-- Cari & Pilih Barang --</option>';
                items.forEach(function (i) {
                    opt += `<option value="${i.id}">${i.nama_barang} (Stok: ${i.stok} ${i.satuan}) - ${formatRupiah(i.harga_satuan)}</option>`;
                });
                $('#order-select-item').html(opt);

                // Load customer dropdown lalu tampilkan modal
                loadCustomerDropdown();
                $('#modal-order').modal('show');
            }
        });
    });

    // Tambah item ke draf order
    $('#btnAddItemToOrder').on('click', function () {
        $('#err-order-items').empty();
        const selectedId = parseInt($('#order-select-item').val());
        const qty = parseInt($('#order-item-qty').val());

        if (!selectedId) {
            $('#err-order-items').text('Pilih barang terlebih dahulu');
            return;
        }
        if (!qty || qty <= 0) {
            $('#err-order-items').text('Jumlah qty minimal 1');
            return;
        }

        const found = availableItemsDropdown.find(i => i.id === selectedId);
        if (!found) return;

        if (qty > found.stok) {
            $('#err-order-items').text(`Stok tidak mencukupi! Tersedia hanya ${found.stok}`);
            return;
        }

        // Cek jika item sudah ada dalam draf
        const existingIdx = orderItemsList.findIndex(i => i.item_id === selectedId);
        if (existingIdx !== -1) {
            const totalQty = orderItemsList[existingIdx].quantity + qty;
            if (totalQty > found.stok) {
                $('#err-order-items').text(`Total order melebihi stok! Tersedia: ${found.stok}`);
                return;
            }
            orderItemsList[existingIdx].quantity = totalQty;
            orderItemsList[existingIdx].subtotal = totalQty * parseFloat(found.harga_satuan);
        } else {
            orderItemsList.push({
                item_id: found.id,
                nama_barang: found.nama_barang,
                satuan: found.satuan,
                harga_satuan: parseFloat(found.harga_satuan),
                quantity: qty,
                subtotal: qty * parseFloat(found.harga_satuan)
            });
        }

        $('#order-item-qty').val(1);
        $('#order-select-item').val('').trigger('change');
        renderOrderItemsTable();
    });

    // Hapus item dari draf
    $(document).on('click', '.btn-remove-order-item', function () {
        const idx = $(this).data('idx');
        orderItemsList.splice(idx, 1);
        renderOrderItemsTable();
    });

    // Update ongkir trigger
    $('#order-ongkir').on('input keyup', function () {
        renderOrderItemsTable();
    });

    function renderOrderItemsTable() {
        let rows = '';
        let totalHarga = 0;

        if (orderItemsList.length === 0) {
            rows = '<tr class="text-center"><td colspan="6">Belum ada item dipilih.</td></tr>';
        } else {
            orderItemsList.forEach(function (oi, idx) {
                totalHarga += oi.subtotal;
                rows += `
                    <tr class="order-item-row">
                        <td>${oi.nama_barang}</td>
                        <td>${oi.satuan}</td>
                        <td>${oi.quantity}</td>
                        <td>${formatRupiah(oi.harga_satuan)}</td>
                        <td>${formatRupiah(oi.subtotal)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-xs btn-danger btn-remove-order-item" data-idx="${idx}">
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        let ongkir = parseFloat($('#order-ongkir').val()) || 0;
        let grandTotal = totalHarga + ongkir;

        $('#list-order-items').html(rows);
        $('#order-total-harga').text(formatRupiah(totalHarga));
        $('#order-total-ongkir').text(formatRupiah(ongkir));
        $('#order-grand-total').text(formatRupiah(grandTotal));
    }

    // Submit Order Baru
    $('#btnSimpanOrder').on('click', function () {
        $('.validate-error').empty();
        const customerId = $('#order-select-customer').val();
        const namaPembeli = $('#order-select-customer option:selected').text().trim();
        const noHp = $('#order-no-hp').val().trim();
        const alamat = $('#order-alamat').val().trim();
        const ongkir = $('#order-ongkir').val().trim() || 0;
        const catatan = $('#order-catatan').val().trim();

        let hasError = false;
        if (!customerId) { $('#err-order-nama').text('Pilih pelanggan terlebih dahulu'); hasError = true; }
        if (!noHp) { $('#err-order-hp').text('Nomor HP wajib diisi'); hasError = true; }
        if (!alamat) { $('#err-order-alamat').text('Alamat pengiriman wajib diisi'); hasError = true; }
        if (orderItemsList.length === 0) {
            $('#err-order-items').text('Tambahkan minimal 1 item barang ke orderan');
            hasError = true;
        }
        if (hasError) return;

        const payload = {
            customer_id: customerId,
            nama_pembeli: namaPembeli,
            no_hp: noHp,
            alamat: alamat,
            biaya_ongkir: ongkir,
            catatan: catatan,
            items: orderItemsList.map(i => ({ item_id: i.item_id, quantity: i.quantity }))
        };

        $('#btnSimpanOrder').prop('disabled', true).text('Memproses Order...');

        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/orders',
            type: 'POST',
            dataType: 'JSON',
            headers: { Authorization: `Bearer ${token}` },
            data: payload,
            success: function (resp) {
                $('#modal-order').modal('hide');
                showNotice('Sukses', `Order ${resp.nomor_order} berhasil dibuat! Nota siap dicetak.`);

                if (resp.order_id) {
                    window.open($('.baseUrl').val() + '/mitra-vet/nota/' + resp.order_id, '_blank');
                }

                loadOrders();
                loadStokBarang();
                loadChart();
            },
            error: function (err) {
                handleAjaxError(err, '#err-global-order');
            },
            complete: function () {
                $('#btnSimpanOrder').prop('disabled', false).html('<i class="fa fa-save"></i> Simpan & Buat Nota');
            }
        });
    });

    // Tombol Hapus Order
    $(document).on('click', '.btn-delete-order', function () {
        selectedOrderId = $(this).data('id');
        const no = $(this).data('no');
        modalActionState = 'delete-order';
        $('#modal-confirmation .box-body').html(`Apakah Anda yakin ingin membatalkan & menghapus order <strong>"${no}"</strong>? Stok barang yang terjual akan otomatis dikembalikan ke gudang.`);
        $('#modal-confirmation').modal('show');
    });

    // Submit Konfirmasi Modal Global
    $('#submitConfirm').on('click', function () {
        if (modalActionState === 'delete-item') {
            $.ajax({
                url: $('.baseUrl').val() + '/api/mitra-vet/items',
                type: 'DELETE',
                dataType: 'JSON',
                headers: { Authorization: `Bearer ${token}` },
                data: { id: selectedItemId },
                success: function (resp) {
                    $('#modal-confirmation').modal('hide');
                    showNotice('Berhasil', resp.message || 'Barang berhasil dihapus.');
                    loadStokBarang();
                    loadChart();
                },
                error: function (err) {
                    $('#modal-confirmation').modal('hide');
                    showNotice('Gagal', err.responseJSON ? err.responseJSON.message : 'Gagal menghapus data.');
                }
            });
        } else if (modalActionState === 'delete-order') {
            $.ajax({
                url: $('.baseUrl').val() + '/api/mitra-vet/orders',
                type: 'DELETE',
                dataType: 'JSON',
                headers: { Authorization: `Bearer ${token}` },
                data: { id: selectedOrderId },
                success: function (resp) {
                    $('#modal-confirmation').modal('hide');
                    showNotice('Berhasil', resp.message || 'Order berhasil dibatalkan dan stok telah dikembalikan.');
                    loadOrders();
                    loadStokBarang();
                    loadChart();
                },
                error: function (err) {
                    $('#modal-confirmation').modal('hide');
                    showNotice('Gagal', err.responseJSON ? err.responseJSON.message : 'Gagal membatalkan order.');
                }
            });
        }
    });

    // =========================================================================
    // 4. GRAFIK HIGHCHARTS
    // =========================================================================

    function loadChart() {
        const month = $('#filter-chart-month').val();
        const year = $('#filter-chart-year').val();

        $.ajax({
            url: $('.baseUrl').val() + '/api/mitra-vet/chart',
            type: 'GET',
            dataType: 'JSON',
            headers: { Authorization: `Bearer ${token}` },
            data: { month: month, year: year },
            success: function (resp) {
                $('#summary-masuk-qty').text(resp.summary.total_masuk_qty || 0);
                $('#summary-masuk-nilai').text(formatRupiah(resp.summary.total_masuk_nilai));
                $('#summary-keluar-qty').text(resp.summary.total_keluar_qty || 0);
                $('#summary-keluar-nilai').text(formatRupiah(resp.summary.total_keluar_nilai));
                $('#aset-keluar-bulan').text(formatRupiah(resp.summary.total_keluar_nilai));

                const daysInMonth = new Date(year, month, 0).getDate();
                const categories = [];
                const masukData = [];
                const keluarData = [];

                const mapMasuk = {};
                (resp.masuk || []).forEach(m => { mapMasuk[m.hari] = parseFloat(m.total_nilai); });
                const mapKeluar = {};
                (resp.keluar || []).forEach(k => { mapKeluar[k.hari] = parseFloat(k.total_nilai); });

                for (let d = 1; d <= daysInMonth; d++) {
                    categories.push('Tgl ' + d);
                    masukData.push(mapMasuk[d] || 0);
                    keluarData.push(mapKeluar[d] || 0);
                }

                Highcharts.chart('chart-mitra-vet', {
                    chart: { type: 'column' },
                    title: { text: null },
                    xAxis: { categories: categories, crosshair: true },
                    yAxis: {
                        min: 0,
                        title: { text: 'Nilai Aset (Rp)' },
                        labels: {
                            formatter: function () {
                                return 'Rp ' + (this.value / 1000).toLocaleString('id-ID') + 'k';
                            }
                        }
                    },
                    tooltip: {
                        shared: true,
                        useHTML: true,
                        headerFormat: '<small>{point.key}</small><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>Rp {point.y:,.0f}</b></td></tr>',
                        footerFormat: '</table>'
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.1,
                            borderWidth: 0
                        }
                    },
                    series: [
                        { name: 'Aset Masuk', data: masukData, color: '#00a65a' },
                        { name: 'Aset Keluar', data: keluarData, color: '#f39c12' }
                    ],
                    credits: { enabled: false }
                });
            }
        });
    }

    $('#filter-chart-month, #filter-chart-year').on('change', function () {
        loadChart();
    });

    // =========================================================================
    // 5. HELPER FUNCTIONS
    // =========================================================================

    function showNotice(title, message) {
        if ($('#msg-box').length) {
            $('#msg-box .modal-title').text(title);
            $('#msg-box .modal-body').text(message);
            $('#msg-box').modal('show');
        } else {
            alert(message);
        }
    }

    function handleAjaxError(err, targetElement) {
        let msg = '';
        if (err.status === 422 && err.responseJSON && err.responseJSON.errors) {
            err.responseJSON.errors.forEach(function (e) {
                msg += `<div>${e}</div>`;
            });
        } else if (err.responseJSON && err.responseJSON.message) {
            msg = `<div>${err.responseJSON.message}</div>`;
        } else {
            msg = '<div>Terjadi kesalahan pada sistem. Silakan coba lagi.</div>';
        }
        $(targetElement).html(msg);
    }

    function renderPagination(selector, totalPages, curPage, onPageClick) {
        $(selector).empty();
        if (totalPages <= 1) return;

        let html = '';
        if (curPage > 1) {
            html += `<li><a href="javascript:void(0)" class="page-link" data-page="${curPage - 1}">&laquo;</a></li>`;
        }

        for (let i = 1; i <= totalPages; i++) {
            if (i === curPage) {
                html += `<li class="active"><a href="javascript:void(0)">${i}</a></li>`;
            } else if (i === 1 || i === totalPages || (i >= curPage - 2 && i <= curPage + 2)) {
                html += `<li><a href="javascript:void(0)" class="page-link" data-page="${i}">${i}</a></li>`;
            } else if (i === curPage - 3 || i === curPage + 3) {
                html += `<li class="disabled"><a href="javascript:void(0)">...</a></li>`;
            }
        }

        if (curPage < totalPages) {
            html += `<li><a href="javascript:void(0)" class="page-link" data-page="${curPage + 1}">&raquo;</a></li>`;
        }

        $(selector).html(html);
        $(selector).find('.page-link').on('click', function () {
            onPageClick(parseInt($(this).data('page')));
        });
    }

    // Tab change trigger
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('href');
        if (target === '#tab-orderan') {
            loadOrders();
        } else if (target === '#tab-stok') {
            loadStokBarang();
            loadChart();
        }
    });

    $('#modal-order').on('shown.bs.modal', function () {
        initOrderSelect2();
    });

    // Inisialisasi awal
    if (userRole === 'resepsionis') {
        loadOrders();
    } else {
        loadStokBarang();
        loadChart();
    }
});
