<?php 
session_start();
include('includes/config.php');
error_reporting(0);
$lang = (include 'includes/lang.php');
if(strlen($_SESSION['login'])==0)
{ 
header('location:index.php');
}
else{
if(isset($_POST['update']))
{
date_default_timezone_set('Asia/Tokyo');
$posttitle=$_POST['posttitle'];
$postdetails=$_POST['postdescription'];
$arr = explode(" ",$posttitle);
$url=implode("-",$arr);
$status=1;
$uid=intval($_GET['uid']);
$query=mysqli_query($con,"update tblcms_posts set PostTitle='$posttitle',PostDetails='$postdetails',PostUrl='$url',Is_Active='$status' where id='$uid'");
if($query)
{
$msg=$lang['edit_post_msg'];
}
else{
$error=$lang['edit_post_error'];    
} 
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <!-- App title -->
    <title>Kaizen | News | Edit Update Info
    </title>
    <!-- Summernote css -->
    <link href="assets/css/summernote/summernote.css" type="text/css" rel="stylesheet" />
    <!-- Select2 -->
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <!-- Jquery filer css -->
    <link href="assets/plugins/jquery.filer/css/jquery.filer.css" rel="stylesheet" />
    <link href="assets/plugins/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css" rel="stylesheet" />
    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/menu.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="assets/plugins/switchery/switchery.min.css">
    <script src="assets/js/modernizr.min.js">
    </script>
  </head>
  <body class="fixed-left">
    <!-- Begin page -->
    <div id="wrapper">
      <!-- Top Bar Start -->
      <?php include('includes/topheader.php');?>
      <!-- ========== Left Sidebar Start ========== -->
      <?php include('includes/leftsidebar.php');?>
      <!-- Left Sidebar End -->
      <!-- ============================================================== -->
      <!-- Start right Content here -->
      <!-- ============================================================== -->
      <div class="content-page">
        <!-- Start content -->
        <div class="content">
          <div class="container">
            <div class="row">
              <div class="col-xs-12">
                <div class="page-title-box">
                  <h4 class="page-title"><?php echo $lang['edit_post_name']?>
                  </h4>
                  <ol class="breadcrumb p-0 m-0">
                    <li>
                      <a href="#"><?php echo $lang['topheader_admin_name']?>
                      </a>
                    </li>
                    <li>
                      <a href="dashboard.php"><?php echo $lang['leftbar_dashboard_name']?>
                      </a>
                    </li>
                    <li class="active">
                    <?php echo $lang['edit_post_name']?>
                    </li>
                  </ol>
                  <div class="clearfix">
                  </div>
                </div>
              </div>
            </div>
            <!-- end row -->
            <div class="row">
              <div class="col-sm-6">  
                <!---Success Message--->  
                <?php if($msg){ ?>
                <div class="alert alert-success" role="alert">
                  <strong>Well done!
                  </strong> 
                  <?php echo htmlentities($msg);?>
                </div>
                <?php } ?>
                <!---Error Message--->
                <?php if($error){ ?>
                <div class="alert alert-danger" role="alert">
                  <strong>Oh snap!
                  </strong> 
                  <?php echo htmlentities($error);?>
                </div>
                <?php } ?>
              </div>
            </div>
            <?php
$uid=intval($_GET['uid']);
$query=mysqli_query($con,"select tblcms_posts.id as uiid,tblcms_posts.PostTitle as title,tblcms_posts.PostDetails from tblcms_posts  where tblcms_posts.id='$uid' and tblcms_posts.Is_Active=1 ");
while($row=mysqli_fetch_array($query))
{
?>
            <div class="row">
              <div class="col-md-10 col-md-offset-1">
                <div class="p-6">
                  <div class="">
                    <form name="addpost" method="post">
                      <div class="form-group m-b-20">
                        <label for="exampleInputEmail1"><?php echo $lang['common_title']?>
                        </label>
                        <input type="text" class="form-control" id="posttitle" name="posttitle" value="<?php echo htmlentities($row['title']);?>" required>
                      </div>
                      <div class="form-group m-b-20">
                        <label for="exampleInputEmail1"><?php echo $lang['edit_post_details']?>
                        </label>
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="card-box">
                            <textarea name="postdescription" class="summernote">
                              <?php echo htmlentities($row['PostDetails']);?>
                            </textarea>
                          </div>
                        </div>
                      </div>
                      <button type="submit" name="update" class="btn btn-success waves-effect waves-light"><?php echo $lang['edit_post_save']?>
                      </button>
                      <?php } ?>
                    </form>
                  </div>
                </div> 
                <!-- end p-20 -->
              </div> 
              <!-- end col -->
            </div>
            <!-- end row -->
          </div> 
          <!-- container -->
        </div> 
        <!-- content -->
        <?php include('includes/footer.php');?>
      </div>
      <!-- ============================================================== -->
      <!-- End Right content here -->
      <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->
    <script>
      var resizefunc = [];
    </script>
        <!-- jQuery  -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/detect.js"></script>
        <script src="assets/js/fastclick.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/waves.js"></script>
        <script src="assets/js/jquery.slimscroll.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>
        <script src="assets/plugins/switchery/switchery.min.js"></script>
        <!--Summernote js-->
        <script src="assets/css/summernote/summernote.js"></script>
        <!-- Select 2 -->
        <script src="assets/plugins/select2/js/select2.min.js"></script>
        <!-- Jquery filer js -->
        <script src="assets/plugins/jquery.filer/js/jquery.filer.min.js"></script>
        <!-- page specific js -->
        <script src="assets/pages/jquery.blog-add.init.js"></script>
        <!-- App js -->
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>
        <script src="assets/plugins/switchery/switchery.min.js"></script>
        <script>
      jQuery(document).ready(function(){
        $('.summernote').summernote({
          height: 240,                 // set editor height
          minHeight: null,             // set minimum height of editor
          maxHeight: null,
          lang: 'ja-JP',             // set maximum height of editor
          focus: false                 // set focus to editable area after initializing summernote
        });
        // Select2
        $(".select2").select2();
        $(".select2-limiting").select2({
          maximumSelectionLength: 2
        });
      });
    </script>
    </body>
</html>
<?php } ?>