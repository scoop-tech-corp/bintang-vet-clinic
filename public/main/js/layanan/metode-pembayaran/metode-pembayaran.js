$(document).ready(function() {
  let getId = null;
  let modalState = '';
  let isValidNamaPembayaran = false;

  let isBeErr = false;
  let paramUrlSetup = {
    orderby:'',
    column: '',
    keyword: '',
  };

  if (role.toLowerCase() != 'admin') {
    $('.columnAction').hide();
  } else {
    $('.section-left-box-title').append(`<button class="btn btn-info openFormAdd m-r-10px">Tambah</button>`);
  }

  loadMetodePembayaran(); // load metode pembayaran

  $('.input-search-section .fa').click(function() {
    onSearch($('.input-search-section input').val());
  });

  $('.input-search-section input').keypress(function(e) {
    if (e.which == 13) { onSearch($(this).val()); }
  });
  
  $('.onOrdering').click(function() {
    const column = $(this).attr('data');
    const orderBy = $(this).attr('orderby');
    $('.onOrdering[data="'+column+'"]').children().remove();

    if (orderBy == 'none' || orderBy == 'asc') {
      $(this).attr('orderby', 'desc');
      $(this).append('<span class="fa fa-sort-desc"></span>');

    } else if(orderBy == 'desc') {
      $(this).attr('orderby', 'asc');
      $(this).append('<span class="fa fa-sort-asc"></span>');
    }

    paramUrlSetup.orderby = $(this).attr('orderby');
    paramUrlSetup.column = column;

    loadMetodePembayaran();
  });

  $('#namaPembayaran').keyup(function () { validationForm(); });
  
  $('.openFormAdd').click(function() {
    modalState = 'add';
    $('.modal-title').text('Tambah Metode Pembayaran');

    refreshForm();
    formConfigure();
  });

  $('#btnSubmitMetodePembayaran').click(function() {

    if (modalState == 'add') {

      const fd = new FormData();
      fd.append('nama_pembayaran', $('#namaPembayaran').val());

      $.ajax({
        url : $('.baseUrl').val() + '/api/metode-pembayaran',
        type: 'POST',
        dataType: 'JSON',
        headers: { 'Authorization': `Bearer ${token}` },
        data: fd, contentType: false, cache: false,
        processData: false,
        beforeSend: function() { $('#loading-screen').show(); },
        success: function(resp) {

          $("#msg-box .modal-body").text('Berhasil Menambah Metode Pembayaran');
          $('#msg-box').modal('show');

          setTimeout(() => {
            $('#modal-metode-pembayaran').modal('toggle');
            refreshForm();
            loadMetodePembayaran();
          }, 1000);
        }, complete: function() { $('#loading-screen').hide(); }
        , error: function(err) {
          if (err.status === 422) {
            let errText = ''; $('#beErr').empty(); $('#btnSubmitMetodePembayaran').attr('disabled', true);
            $.each(err.responseJSON.errors, function(idx, v) {
              errText += v + ((idx !== err.responseJSON.errors.length - 1) ? '<br/>' : '');
            });
            $('#beErr').append(errText); isBeErr = true;
          } else if (err.status == 401) {
            localStorage.removeItem('vet-clinic');
            location.href = $('.baseUrl').val() + '/masuk';
          }
        }
      });

    } else {
      // edit
      $('#modal-confirmation .modal-title').text('Peringatan');
      $('#modal-confirmation .box-body').text('Anda yakin untuk mengubah metode pembayaran ?');
      $('#modal-confirmation').modal('show');
    }
  });

  $('#submitConfirm').click(function() {

    if (modalState == 'edit') {
      // process edit
      const datas = {
        id: getId,
        nama_pembayaran: $('#namaPembayaran').val(),
      };

      $.ajax({
        url : $('.baseUrl').val() + '/api/metode-pembayaran',
        type: 'PUT',
        dataType: 'JSON',
        headers: { 'Authorization': `Bearer ${token}` },
        data: datas,
        beforeSend: function() { $('#loading-screen').show(); },
        success: function(data) {
          $('#modal-confirmation .modal-title').text('Peringatan');
          $('#modal-confirmation').modal('toggle');

          $("#msg-box .modal-body").text('Berhasil Mengubah Metode Pembayaran');
          $('#msg-box').modal('show');

          setTimeout(() => {
            $('#modal-metode-pembayaran').modal('toggle');
            refreshForm();
            loadMetodePembayaran();
          }, 1000);

        }, complete: function() { $('#loading-screen').hide(); }
        , error: function(err) {
          if (err.status === 422) {
            let errText = ''; $('#beErr').empty(); 
            $('#modal-confirmation').modal('toggle');
            $('#btnSubmitMetodePembayaran').attr('disabled', true);
            $.each(err.responseJSON.errors, function(idx, v) {
              errText += v + ((idx !== err.responseJSON.errors.length - 1) ? '<br/>' : '');
            });
            $('#beErr').append(errText); isBeErr = true;
          } else if (err.status == 401) {
            localStorage.removeItem('vet-clinic');
            location.href = $('.baseUrl').val() + '/masuk';
          }
        }
      });
    } else {
      // process delete
      $.ajax({
        url     : $('.baseUrl').val() + '/api/metode-pembayaran',
        headers : { 'Authorization': `Bearer ${token}` },
        type    : 'DELETE',
        data    : { id: getId },
        beforeSend: function() { $('#loading-screen').show(); },
        success: function(data) {
          $('#modal-confirmation .modal-title').text('Peringatan');
          $('#modal-confirmation').modal('toggle');

          $("#msg-box .modal-body").text('Berhasil menghapus metode pembayaran');
          $('#msg-box').modal('show');

          loadMetodePembayaran();

        }, complete: function() { $('#loading-screen').hide(); }
        , error: function(err) {
          if (err.status == 401) {
            localStorage.removeItem('vet-clinic');
            location.href = $('.baseUrl').val() + '/masuk';
          }
        }
      });
    }
  });

  function onSearch(keyword) {
    paramUrlSetup.keyword = keyword;
    loadMetodePembayaran();
  }

  function loadMetodePembayaran() {
    getId = null;
    modalState = '';
    $.ajax({
      url     : $('.baseUrl').val() + '/api/metode-pembayaran',
      headers : { 'Authorization': `Bearer ${token}` },
      type    : 'GET',
      data    : { orderby: paramUrlSetup.orderby, column: paramUrlSetup.column, keyword: paramUrlSetup.keyword },
      beforeSend: function() { $('#loading-screen').show(); },
      success: function(data) {
        let listMetodePembayaran = '';
        $('#list-metode-pembayaran tr').remove();

        if(data.length) {
          $.each(data, function(idx, v) {
            listMetodePembayaran += `<tr>`
              + `<td>${++idx}</td>`
              + `<td>${v.payment_name}</td>`
              + `<td>${v.created_by}</td>`
              + `<td>${v.created_at}</td>`
              + ((role.toLowerCase() != 'admin') ? `` : `<td>
                  <button type="button" class="btn btn-warning openFormEdit" value=${v.id}><i class="fa fa-pencil" aria-hidden="true"></i></button>
                  <button type="button" class="btn btn-danger openFormDelete" value=${v.id}><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                </td>`)
              + `</tr>`;
          });
        } else {
          listMetodePembayaran += `<tr class="text-center"><td colspan="7">Tidak ada data.</td></tr>`;
        }
        $('#list-metode-pembayaran').append(listMetodePembayaran);

        $('.openFormEdit').click(function() {
          const getObj = data.find(x => x.id == $(this).val());
          modalState = 'edit';

          $('.modal-title').text('Edit Metode Pembayaran');
          refreshForm();

          formConfigure();
          getId = getObj.id;
          $('#namaPembayaran').val(getObj.payment_name);
        });
      
        $('.openFormDelete').click(function() {
          getId = $(this).val();
          modalState = 'delete';

          $('#modal-confirmation .modal-title').text('Peringatan');
          $('#modal-confirmation .box-body').text('Anda yakin ingin menghapus Metode Pembayaran ini?');
          $('#modal-confirmation').modal('show');
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

  function formConfigure() {
    $('#modal-metode-pembayaran').modal('show');
    $('#btnSubmitMetodePembayaran').attr('disabled', true);
  }

  function refreshForm() {
    $('#namaPembayaran').val(null);
    $('#namaPembayaranErr1').text(''); isValidNamaPembayaran = true;
    $('#beErr').empty(); isBeErr = false;
  }

  function validationForm() {
    $('#namaPembayaranErr1').text(!$('#namaPembayaran').val() ? 'Nama metode pembayaran harus di isi' : '');
    isValidNamaPembayaran = !$('#namaPembayaran').val() ? false : true;

    $('#beErr').empty(); isBeErr = false;
    $('#btnSubmitMetodePembayaran').attr('disabled', (!isValidNamaPembayaran || isBeErr) ? true : false);
  }

});
