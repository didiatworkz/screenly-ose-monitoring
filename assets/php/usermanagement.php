<?php
if(getGroupID($loginUserID) == 1){
  if(isset($_GET['action']) && $_GET['action'] == 'new'){
    echo 'Addon';
  }
  else{
    $userSQL 		= $db->query("SELECT * FROM users ORDER BY userID");
    echo '
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-md-10">
                <h5 class="title">User Management</h5>
              </div>
              <div class="col-md-2 float-right">
                <a href="index.php?site=usermanagement&action=new" class="btn btn-success btn-sm btn-block"><i class="tim-icons icon-simple-add"></i> New User</a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table class="table" id="users">
              <thead class="text-primary">
                <tr>
                  <th>Username</th>
                  <th>Name</th>
                  <th>Group</th>
                  <th>Active</th>
                  <th>Last Login</th>
                  <th><span class="d-none d-sm-block">Options</span></th>
                </tr>
              </thead>
              <tbody>';
    while($user = $userSQL->fetchArray(SQLITE3_ASSOC)){
        echo '
        <tr>
          <td>'.$user['username'].'</td>
          <td>'.getFullname($user['userID']).'</td>
          <td>'.getGroupName($user['userID']).'</td>
          <td>'.isActive($user['userID']).'</td>
          <td>'.date( "Y-m-d H:m", lastLogin($user['userID'])).'</td>
          <td>xx</td>
        </tr>';
      }

      echo '</tbody>
      </table>
      </div>
      </div>
      </div>
      </div>';


  }
}
else echo ' No access';
