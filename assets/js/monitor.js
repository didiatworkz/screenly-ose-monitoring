$('[data-tooltip="tooltip"]').tooltip();
$('[data-tooltip=tooltip]').hover(function(){
  $('.tooltip').css('top',parseInt($('.tooltip').css('left')) + 10 + 'px')
});

function getUrlParameterByName(name, url)
{
  if (!url) url = window.location.href;
  name = name.replace(/[\[\]]/g, "\\$&");
  var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"), results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, " "));
}

$('.changeState').on('click', function() {
  var asset = $(this).data('asset_id');
  var id = getUrlParameterByName('playerID');
  var changeAssetState = 1;
  $.ajax({
    url: '_functions.php',
    type: 'POST',
    data: {asset: asset, id: id, changeAssetState: changeAssetState},
    success: function(data){
      $('span[data-asset_id="'+asset+'"').toggle(function() {
        $(this).toggleClass('badge-success badge-danger').show();
        if($(this).hasClass('badge-danger')) $(this).text('inactive');
        else $(this).text('active');
      });
      $.notify({icon: 'tim-icons icon-bell-55',message: 'Asset status changed'},{type: 'success',timer: 1000,placement: {from: 'top',align: 'center'}});
    },
    error: function(data){
      $.notify({icon: 'tim-icons icon-bell-55',message: 'Error! - Can \'t change the Asset'},{type: 'danger',timer: 1000,placement: {from: 'top',align: 'center'}});
    }
  });
});

$('.changeAsset').on('click', function() {
  var order = $(this).data('order');
  var id = getUrlParameterByName('playerID');
  var changeAsset = 1;
  $.ajax({
    url: '_functions.php',
    type: 'POST',
    data: { order: order, playerID: id, changeAsset: changeAsset },
    success: function(data){
      $.notify({icon: 'tim-icons icon-bell-55',message: data},{type: 'success',timer: 1000,placement: {from: 'top',align: 'center'}});
    },
    error: function(data){
      $.notify({icon: 'tim-icons icon-bell-55',message: 'Error! - Can \'t change the Asset'},{type: 'danger',timer: 1000,placement: {from: 'top',align: 'center'}});
    }
  });
});

$('#assets').DataTable({
  responsive: true,
  orderFixed: [[ 3, 'desc' ], [ 2, 'asc' ]],
  rowGroup: {
    dataSrc: 3,
  },
  lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
  stateSave: true
});

$('#extension').DataTable({
  responsive: true,
  order: [[ 1, 'asc' ]],
  lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
  stateSave: false
});

$('#users').DataTable({
  responsive: true,
  order: [[ 0, 'asc' ]],
  lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
  stateSave: false
});
// New Asset


var acceptedFileTypes = "image/*, video/*";
var upload_asset = 1;
Dropzone.options.dropzone = {
  parallelUploads: 1,
  autoDiscover: false,
  paramName: "file_upload",
  createImageThumbnails: true,
  acceptedFiles: acceptedFileTypes,
  headers:{'Authorization':'Basic ' + scriptPlayerAuth},
  success: function(file, response){
    var mimetype = "unknown";
    var fname = file.name;
    var ftype = file.type;
    var playerID = getUrlParameterByName('playerID');
    if(ftype.includes("image")) mimetype = "image";
    else if (ftype.includes("video")) mimetype = "video";
    else mimetype = "unknown";
    $.ajax({
     url: '_functions.php',
     type: 'POST',
     data: { name: fname, url: response, mimetype: mimetype, id: playerID, newAsset: upload_asset  },
     success: function(data){
       $.notify({icon: 'tim-icons icon-bell-55',message: data},{type: 'success',timer: 1000,placement: {from: 'top',align: 'center'}});
     },
     error: function(data){
       $.notify({icon: 'tim-icons icon-bell-55',message: data},{type: 'danger',timer: 1000,placement: {from: 'top',align: 'center'}});
     }
   });
  }
}
$("#assetNewForm").submit(function(e) {
  e.preventDefault();
  var form = $(this);
  $.ajax({
   url: '_functions.php',
   type: 'POST',
   data: form.serialize(),
   success: function(data){
     $('#newAsset').modal('hide');
     $.notify({icon: 'tim-icons icon-bell-55',message: data},{type: 'success',timer: 1000,placement: {from: 'top',align: 'center'}});
     setTimeout(function() {
      location.reload();
    }, 2000);
   },
   error: function(data){
     $.notify({icon: 'tim-icons icon-bell-55',message: data},{type: 'danger',timer: 1000,placement: {from: 'top',align: 'center'}});
   }
 });
});
$('.close_upload').on('click', function(){
  $('#newAsset').modal('hide');
  location.reload();
});

$('button.options').on('click', function(){
  var eA = $('#editAsset');
  eA.find('#InputAssetName').val($(this).data('name'));
  eA.find('#InputAssetUrl').val($(this).data('uri'));
  eA.find('#InputAssetStart').val($(this).data('start-date'));
  eA.find('#InputAssetStartTime').val($(this).data('start-time'));
  eA.find('#InputAssetEnd').val($(this).data('end-date'));
  eA.find('#InputAssetEndTime').val($(this).data('end-time'));
  eA.find('#InputAssetDuration').val($(this).data('duration'));
  eA.find('#InputAssetId').val($(this).data('asset'));
  eA.modal('show');
  return false;
});

// New player
$("#newPlayerDiscover").submit(function(e) {
  e.preventDefault();
  $(".start_discovery").html('Loading...');
  $('.start_discovery').prop('disabled', true);
  $("#InputCIDR").blur();
  $("#discoverStatus").empty();
  var form = $(this);
  $.ajax({
   url: 'assets/php/discover.php',
   type: 'GET',
   data: form.serialize(),
   success: function(data){
     $.notify({icon: 'tim-icons icon-bell-55',message: 'Scan complete'},{type: 'success',timer: 1000,placement: {from: 'top',align: 'center'}});
     $("#discoverStatus").html(data);
     $(".start_discovery").html('Discover');
     $('.start_discovery').prop('disabled', false);
   },
   error: function(data){
     $.notify({icon: 'tim-icons icon-bell-55',message: 'Scan failed!'},{type: 'danger',timer: 1000,placement: {from: 'top',align: 'center'}});
     $("#discoverStatus").html(data);
     $(".start_discovery").html('Discover');
     $('.start_discovery').prop('disabled', false);
   }
 });

});
$('.close_player').on('click', function(){
  $('#newPlayer').modal('hide');
  location.reload(0);
});

// Install Extensions
$("#installExtension").submit(function(e) {
  e.preventDefault();
  $(".install").html('Wait...');
  $('.install').prop('disabled', true);
  var form = $(this);
  $.ajax({
   url: 'assets/php/extensions.php',
   type: 'POST',
   data: form.serialize(),
   success: function(data){
     $.notify({icon: 'tim-icons icon-bell-55',message: 'Scan complete'},{type: 'success',timer: 1000,placement: {from: 'top',align: 'center'}});
     $(".install").html('Discover');
     $('.install').prop('disabled', false);
   },
   error: function(data){
     $.notify({icon: 'tim-icons icon-bell-55',message: 'Scan failed!'},{type: 'danger',timer: 1000,placement: {from: 'top',align: 'center'}});
     $(".install").html('Discover');
     $('.install').prop('disabled', false);
   }
 });

});
$('.install_close').on('click', function(){
  $('#installer').modal('hide');
  location.reload(0);
});

$('.editPlayerOpen').on('click', function() {
  var id = getUrlParameterByName('playerID');
  var editInformation = 1;
  $.ajax({
    url: '_functions.php',
    type: 'POST',
    dataType: 'JSON',
    data: { playerID: id, editInformation: editInformation },
    success: function(response){
      var eP = $('#editPlayer');
        eP.find('#playerIDEdit').val(id);
        eP.find('#InputPlayerNameEdit').val(response.player_name);
        eP.find('#playerNameTitle').val(response.player_name);
        eP.find('#InputLocationEdit').val(response.player_location);
        eP.find('#InputAdressEdit').val(response.player_address);
        eP.find('#InputUserEdit').val(response.player_user);
        eP.find('#InputPasswordEdit').val(response.player_password);
        eP.modal('show');
        return false;
    },
    error: function(data){
      $.notify({icon: 'tim-icons icon-bell-55',message: 'Error! - Can \'t change the Asset'},{type: 'danger',timer: 1000,placement: {from: 'top',align: 'center'}});
    }
  });
});


$('button.reboot').on('click', function(){
  var eR = $('#confirmReboot');
  var id = getUrlParameterByName('playerID');
  eR.modal('show');

  $('.exec_reboot').on('click', function() {
    var exec_reboot = 1;
    $.ajax({
      url: '_functions.php',
      type: 'POST',
      data: { playerID: id, exec_reboot: exec_reboot },
      success: function(data){
        $.notify({icon: 'tim-icons icon-bell-55',message: data},{type: 'success',timer: 1000,placement: {from: 'top',align: 'center'}});
        eR.modal('hide');
      },
      error: function(data){
        $.notify({icon: 'tim-icons icon-bell-55',message: 'Error! - Can \'t change the Asset'},{type: 'danger',timer: 1000,placement: {from: 'top',align: 'center'}});
        eR.modal('hide');
      }
    });
  });
});

$('#confirmDelete').on('show.bs.modal', function(e) {
  $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
});

$(function(){
  var navMain = $('.navbar-collapse');
  navMain.on('click', '[data-toggle]', null, function () {
    navMain.collapse('hide');
  });
});

function reloadPlayerImage(){
  $('img.player').each(function(){
    var url = $(this).attr('src').split('?')[0];
    $(this).attr('src', url + '?' + Math.random());
  })
}

setInterval('reloadPlayerImage();',settingsRefreshRate);
$('.modal').on('shown.bs.modal', function(){
  $(this).find('[autofocus]').focus();
});
