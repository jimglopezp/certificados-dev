<div class="center-form">
  <h1><?php echo lang('forgot_password_heading');?></h1>
  <p><?php echo sprintf(lang('forgot_password_subheading'), $identity_label);?></p>
  <?php if(isset($message) and !empty($message)) : ?>
  <div id="infoMessage"><?php echo $message;?></div>
  <?php endif; ?>
  <?php echo form_open("auth/forgot_password");?>
  <p>
    <label for="email"><?php echo sprintf(lang('forgot_password_email_label'), $identity_label);?></label>
    <br />
    <?php echo form_input($email);?> </p>
  <p>
    <button type="submit" name="submit" value="<?php echo lang('forgot_password_submit_btn') ?>" class="btn btn-success btn-right"> <i class="fa fa-sign-in fa-lg"></i> <?php echo lang('forgot_password_submit_btn') ?> </button>
  </p>
  <?php echo form_close();?> </div>
