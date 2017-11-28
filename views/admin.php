<?php require('header.php'); ?>

    <h1 class="lti-heading">Log into CLE (Admin)</h1>

    <form class="lti-form" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
      <div class="form-group">
        <label for="exampleInputEmail1">Email address</label>
        <input type="email" 
               class="form-control" 
               id="exampleInputEmail1" 
               aria-describedby="emailHelp" 
               placeholder="Enter email"
               name="email">
      </div>
      <div class="form-group">
        <label for="user_id">LMS User ID</label>
        <input type="text" 
               class="form-control" 
               id="user_id" 
               placeholder="LMS User ID"
               name="user_id">
               <small>This is only required for testing purposes (Your LMS should provide this data)</small>
      </div>      
      <div class="form-group">
        <label for="exampleInputEmail1">CLE Integration</label>
        <select class="form-control" id="exampleFormControlSelect1" name="integration_id">
          <?php foreach($integrations as $i): ?>
            <option value="<?php echo $i->id ?>"><?php echo $i->name; ?></option>
          <?php endforeach; ?>
        </select>
      </div>          
      <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
      </div>   
</form>


<?php require('footer.php'); ?>
