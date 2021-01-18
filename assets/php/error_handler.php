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
       Error Handler Function
_______________________________________
*/

function somo_error_handler($errno, $error, $file, $line, $context) {
  $id = rand();
  $message = '
  <div class="col-12">
    <div class="alert_handler alert_handler-danger">
      <div class="alert_handler-icon">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
      </div>
      <div class="alert_handler-text">
        <h5>ERROR: '.$errno.' - '.$error.'</h5>
        File: '.print_r($file, 1).' on line: '.print_r($line, 1).'<br />
        <button type="button" class="btn btn-secondary btn-sm pl-3 pr-3" data-toggle="collapse" href="#content'.$id.'">Details</button>
        <div id="content'.$id.'" class="collapse"><pre><code class="language-php" data-lang="php">'.print_r($context, 1).'</code></pre></div>
      </div>
    </div>
  </div>
';
  echo $message;
}
