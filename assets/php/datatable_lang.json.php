<?php
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
_______________________________________

       Screenly OSE Monitoring
       DataTable Language File
_______________________________________
*/

// TRANSLATION CLASS
require_once('translation.php');
use Translation\Translation;
Translation::setLocalesDir(__DIR__ . '/../locales');

$data = array();
$data['sEmptyTable'] = Translation::of('datatable.sEmptyTable');
$data['sInfo'] = Translation::of('datatable.sInfo');
$data['sInfoEmpty'] = Translation::of('datatable.sInfoEmpty');
$data['sInfoFiltered'] = Translation::of('datatable.sInfoFiltered');
$data['sInfoPostFix'] = '';
$data['sInfoThousands'] = Translation::of('datatable.sInfoThousands');
$data['sLengthMenu'] = Translation::of('datatable.sLengthMenu');
$data['sLoadingRecords'] = Translation::of('datatable.sLoadingRecords');
$data['sProcessing'] = Translation::of('datatable.sProcessing');
$data['sSearch'] = Translation::of('datatable.sSearch');
$data['sZeroRecords'] = Translation::of('datatable.sZeroRecords');
$data['oPaginate'] = array();
$data['oPaginate']['sFirst'] = Translation::of('datatable.sFirst');
$data['oPaginate']['sLast'] = Translation::of('datatable.sLast');
$data['oPaginate']['sNext'] = Translation::of('datatable.sNext');
$data['oPaginate']['sPrevious'] = Translation::of('datatable.sPrevious');
$data['oAria'] = array();
$data['oAria']['sSortAscending'] = Translation::of('datatable.sSortAscending');
$data['oAria']['sSortDescending'] = Translation::of('datatable.sSortDescending');

header('Content-Type: application/json');
echo json_encode($data);
