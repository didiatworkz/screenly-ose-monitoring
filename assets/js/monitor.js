/*
                            _
   ____                    | |
  / __ \__      _____  _ __| | __ ____
 / / _` \ \ /\ / / _ \| '__| |/ /|_  /
| | (_| |\ V  V / (_) | |  |   <  / /
 \ \__,_| \_/\_/ \___/|_|  |_|\_\/___|
  \____/

    http://www.atworkz.de
       info@atworkz.de
________________________________________
      Screenly OSE Monitor
________________________________________
*/

function setNotification(style, message) {
  localStorage.setItem("notification_style", style);
  localStorage.setItem("notification_message", message);
  localStorage.setItem("notification_counter", "0");
}

$('.close_modal').on('click', function(){
  var closeClass = $(this).data('close');
  $(closeClass).modal('hide');
  location.reload(0);
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

var asset_table = $('#assets').DataTable({
  responsive: false,
  orderFixed: [[ 4, 'desc' ], [ 2, 'desc' ]],
  rowGroup: {
    dataSrc: 4,
  },
  ordering: true,
  responsive: {
    details: {
      type: 'column'
    }
  },
  columnDefs: [{
    className: 'control',
    orderable: false,
    targets:   0
  }],
  lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
  stateSave: true,
  autoWidth: false
});

$("#assets tbody").sortable({
  //placeholder: "ui-state-highlight",
  cursor: 'move',
  items: 'tr:not(.asset-hidden)',
  axis: 'y',
  helper: function(e, tr){
    var $originals = tr.children();
    var $helper = tr.clone();
    $helper.children().each(function(index){
     $(this).width($originals.eq(index).width());
    });
    return $helper;
  },
	update: function (event, ui) {
    var player = ui.item["0"].dataset.playerid;
    var data = $(this).sortable('serialize', {key: 'order[]', expression: /(.+)/});
    data += '&id=' + player;
    data += '&changeOrder=1';

		$.ajax({
      type: "POST",
      url: "_functions.php",
      data: data,
		});
  }
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
Dropzone.autoDiscover = false;

if ($('.drop').length) {
  var acceptedFileTypes = "image/*, video/*";
  var upload_asset = 1;
  var myDropzone = new Dropzone(".dropzone", {
    parallelUploads: 100,
    parallelUploads: 100,
    addRemoveLinks: true,
    maxFilesize: 100,
    timeout: 60000,
    paramName: "file_upload",
    acceptedFiles: acceptedFileTypes,
    headers:{'Authorization':'Basic ' + scriptPlayerAuth},
    complete: function(file){ $('#newAsset').modal('hide'); location.reload(); },
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
       timeout: 5000,
       success: function(data){
         setNotification('success', data);
         myDropzone.removeFile(file);
       },
       error: function(data){
         $.notify({icon: 'tim-icons icon-bell-55',message: data},{type: 'danger',timer: 1000,placement: {from: 'top',align: 'center'}});
       }
     });
    }
  });
}

if ($('.dropzoneMulti').length) {
  var myMulitDropzone = new Dropzone(".dropzoneMulti", {
    acceptedFiles: acceptedFileTypes,
    autoProcessQueue: false,
    parallelUploads: 100,
    addRemoveLinks: true,
    maxFilesize: 60,
    timeout: 60000,
    method: 'post',
    url: '_functions.php',
    dictFileTooBig: 'This file is to big! Max allowed {{maxFilesize}}MB. Please upload this file via the Player Management driectly',
    accept: function(file, done) {
        console.log("uploaded");
        done();
    },

    init: function (e) {
      var myMulitDropzone = this;

      $('#uploadfiles').on("click", function() {
          myMulitDropzone.processQueue();
      });

      myMulitDropzone.on("sending", function(file, xhr, data) {
        var ids = [];
        var mimetype = "unknown";
        var fname = file.name;
        var ftype = file.type;
        if(ftype.includes("image")) mimetype = "image";
        else if (ftype.includes("video")) mimetype = "video";
        else mimetype = "unknown";

        $("input:checkbox[name='id[]']:checked").each(function(){
          ids.push($(this).val());
        });
        data.append("playerID", ids);
        data.append("multidrop", '1');
        data.append("newAsset", '1');
        data.append("mimetype", mimetype);
        data.append("name", fname);
        $('#uploadfiles').hide();
      });

      this.on("success", function(file){
        myMulitDropzone.removeFile(file);
        var response = file.xhr.response;
        console.log(file.xhr.response);
      });

      this.on("complete", function(file){
        $('#refresh').show();
        $('.dz-message').text("Upload done! - Reload this page...");
      });
    }
  });
}

$('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
  $('.card-body').find('input[type=checkbox]:checked').remove();
});

$("#assetNewForm").submit(function(e) {
  e.preventDefault();
  var form = $(this);
  var loopLength = 1;
  if(form.data('multiloader') == true) {
    loopLength = form[0].length;
  }

  var formData = form.serialize();

  for (var i = 0; i < loopLength; i++) {
    if(form.data('multiloader') == true) {
      if(form[0][i].checked == true){
        var newID = form[0][i].value;
        formData = $('input:not([name^=id])', this).serialize() + '&id=' + newID;
      }
      else continue;
    }

    $.ajax({
     url: '_functions.php',
     type: 'POST',
     data: formData,
     success: function(data){
       $('#newAsset').modal('hide');
       setNotification('success', data);
       setTimeout(function() {
         location.reload();
       }, 0);
     },
     error: function(data){
       $.notify({icon: 'tim-icons icon-bell-55',message: data},{type: 'danger',timer: 1000,placement: {from: 'top',align: 'center'}});
     }
   });
  }
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

// SEARCH
 $("#inlineFormInputGroup").on("keyup", function() {
  var input = $(this).val().toUpperCase();

  $(".col-sm-6").each(function() {
    if ($(this).data("string").toUpperCase().indexOf(input) < 0) {
      $(this).hide();
    } else {
      $(this).show();
    }
  })
});

$('#inlineFormInputGroup').on("keypress", function (e) {
    if (e.which == 13) $('#searchModal').modal('hide');
});

// New player
$('input:radio[name="add_player_mode"]').click(function(){
  var inputValue = $(this).attr("value");
  var targetBox = $("." + inputValue);
  $(".tab").not(targetBox).hide();
  $(targetBox).show();
});

$("#authentication").change(function(){
 $(".authentication").toggle();
});

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

$('.editPlayerOpen').on('click', function() {
  var id = $(this).data('playerid');
  var editInformation = 1;
  $.ajax({
    url: '_functions.php',
    type: 'POST',
    dataType: 'JSON',
    data: { playerID: id, editInformation: editInformation },
    success: function(response){
      var eP = $('#editPlayer');
      eP.find('#playerIDEdit').val(id);
      eP.find('#playerNameTitle').text(response.player_name);
      eP.find('#InputPlayerNameEdit').val(response.player_name);
      eP.find('#InputLocationEdit').val(response.player_location);
      eP.find('#InputAdressEdit').val(response.player_address);
      eP.find('#InputUserEdit').val(response.player_user);
      eP.find('#InputPasswordEdit').val(response.player_password);
      eP.modal('show');
      return false;
    },
    error: function(data){
      $.notify({icon: 'tim-icons icon-bell-55',message: 'Error! - Can \'t change the Player information'},{type: 'danger',timer: 1000,placement: {from: 'top',align: 'center'}});
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

$('#confirmDelete, #confirmDeleteAssets').on('show.bs.modal', function(e) {
  $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
});

$(function(){
  var navMain = $('.navbar-collapse');
  navMain.on('click', '[data-toggle="modal"]', null, function () {
    navMain.collapse('hide');
  });
});


function reloadPlayerImage(){
  $('img.player').each(function(index, element){
    var url = $(element).attr('data-src');
    $.ajax({
      url: 'assets/php/image.php',
      data: {image: 1, ip: url},
      dataType: 'json',
      type: 'GET',
      success: function(data){
        $(element).attr('src', data);
      },
      error: function(data){
        $(element).attr('src', 'assets/img/offline.png');
        console.log('No connection - ' + url);
      },
    });
  })
}

$(document).ready(function() {
    reloadPlayerImage();
});

setInterval('reloadPlayerImage();',settingsRefreshRate);

$('.modal').on('shown.bs.modal', function(){
  $(this).find('[autofocus]').focus();
});
