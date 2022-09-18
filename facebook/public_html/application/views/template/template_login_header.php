<!DOCTYPE html>
  <html>
    <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title><?php echo $title;?></title>
      <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
      <meta name="theme-color" content="#ffffff">
      <?php
      #Bootstrap 3.3.2
      $link = array(
                'href' => PROJECTJSPATH.'assets/images/logo/favicon.png',
                'rel' => 'shortcut icon'
      );
      echo link_tag($link);

      $link = array(
          'href' => PROJECTJSPATH.'assets/css/vendor.min.css',
          'rel' => 'stylesheet',
          'type' => 'text/css'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/elephant.min.css',
          'rel' => 'stylesheet',
          'type' => 'text/css'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/login-2.min.css',
          'rel' => 'stylesheet',
          'type' => 'text/css'
      );
      echo link_tag($link);
      $link = array(
          'href' => PROJECTJSPATH.'assets/css/custom-login.css',
          'rel' => 'stylesheet',
          'type' => 'text/css'
      );
      echo link_tag($link);


      ?>
    </head>
    <body>
      <div class="wrapper">
