<?php

if(firstStart() == 1){
  echo '
  <div class="row">
    <div class="col-sm-8 offset-sm-2">
      <div class="card">
        <div class="card-header ">
          <div class="row">
            <div class="col-sm-12 text-left">
              <h2 class="card-title">First Setup Wizard</h2>
              <h4 class="text-right">Step 1</h4>
            </div>
          </div>
        </div>
        <div class="card-body">
          <p class="lead">Thank you for using Screenly OSE Monitoring.<br />
              To get started, you need to change your username and password.
          </p>
          <hr />
          <form id="accountForm" action="'.$_SERVER['REQUEST_URI'].'" method="POST" data-toggle="validator">
            <div class="form-group">
              <label for="InputUsername">Change Username</label>
              <input name="username" type="text" class="form-control" id="InputUsername" placeholder="New Username" autofocus required />
              <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
              <label for="InputPassword1">Change Password</label>
              <input name="password1" type="password" class="form-control" id="InputPassword1" placeholder="New Password" required />
            </div>
            <div class="form-group">
              <input name="password2" type="password" class="form-control" id="InputPassword2" placeholder="Confirm Password" data-match="#InputPassword1" data-match-error="Whoops, these don\'t match" required />
              <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
              <br />
              <input name="mode" type="hidden" value="firstStep"/>
              <button type="submit" name="saveAccount" class="btn btn-primary btn-lg btn-block">Next Step</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  ';
}
else if(firstStart() == 2 && checkAddress($_SERVER['SERVER_ADDR'].'/api/v1.2/assets')){
  echo '
  <div class="row">
    <div class="col-sm-8 offset-sm-2">
      <div class="card">
        <div class="card-header ">
          <div class="row">
            <div class="col-sm-12 text-left">
              <h2 class="card-title">First Setup Wizard</h2>
              <h4 class="text-right">Step 2</h4>
            </div>
          </div>
        </div>
        <div class="card-body">
          <p class="lead">Do you want to add this Screenly OSE Player to your monitoring?</p>
          <hr />
          <form id="playerForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" data-toggle="validator">
            <div class="form-group">
              <label for="InputPlayerName">Enter the Screenly Player name</label>
              <input name="name" type="text" class="form-control" id="InputPlayerName" placeholder="Player-Name" autofocus required />
            </div>
            <div class="form-group">
              <label for="InputLocation">Enter the Player location</label>
              <input name="location" type="text" class="form-control" id="InputLocation" placeholder="Player-Location" required />
            </div>
            <hr />
            <div class="form-group">
              <input name="address" type="hidden" id="InputAdress" value="'.$_SERVER['SERVER_ADDR'].'" />
              <button type="submit" name="saveIP" class="btn btn-primary btn-lg btn-block">Yes</button>
              <a href="index.php?action=startup" class="btn btn-danger btn-lg btn-block">No</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  ';
}
else {
  echo '
<div class="row">
  <div class="col-sm-8 offset-sm-2">
    <div class="card">
      <div class="card-header ">
        <div class="row">
          <div class="col-sm-12 text-left">
            <h2 class="card-title">Welcome</h2>
          </div>
        </div>
      </div>
      <div class="card-body">
        <p class="lead">With Screenly OSE Monitoring you can set up an unlimited number of players and manage them at a single screen. <br />
          Additionally there is the possibility to install addons on the players to get even more information in Screenly OSE Monitoring.<br />
          <br />
          Add your first Screenly OSE Player and discover how easy it can be to work with.
        </p>
        <br />
        <a href="#" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#newPlayer">Add your first Screenly OSE Player</a>
      </div>
    </div>
  </div>
</div>
';
}
