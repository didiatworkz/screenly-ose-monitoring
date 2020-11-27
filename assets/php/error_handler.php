<?php

function somo_error_handler($errno, $error, $file, $line, $context) {
  $id = rand();
  $message = '<div class="col-12"><div class="card"><div class="card-status-top bg-danger"></div><div class="card-body">';
  $message .= '<h3 class="card-title d-inline-block">ERROR: '.$errno.' - '.$error.'</h3> <a class="btn btn-outline-info btn-sm d-inline-block ml-3 mb-2" data-toggle="collapse" href="#content'.$id.'">more</a>';
  $message .= '<p>File: '.print_r($file, 1).' on line: '.print_r($line, 1).'</p>';
  $message .= '<div id="content'.$id.'" class="collapse"><pre><code class="language-php" data-lang="php">'.print_r($context, 1).'</code></pre></div>';
  $message .= '</div></div></div>';
  echo $message;
}
