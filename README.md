# bundler

Simple PHP File Bundler for Combining JS and CSS files.

Example Usage:

  <link rel="stylesheet" href="main_styles.css" >
  <link rel="stylesheet" href="some_other_styles.css" >

To send the above files as a single file to the client (web browser), you can combine the two css files in a php file like so:
  
  <?php include('bundler.php') ?>
  
  <link rel="stylesheet" href="<?= bundle_files(['main_styles.css', 'some_other_styles.css'],'1.0','css') ?>" >
