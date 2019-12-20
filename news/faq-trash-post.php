<?php 
session_start();
include('includes/config.php');
$lang = (include 'includes/lang.php');
error_reporting(0);
$lang = (include 'includes/lang.php');
if(strlen($_SESSION['login'])==0)
{ 
header('location:index.php');
}
else{
if($_GET['action']='restore')
{
$uid=intval($_GET['uid']);
$query=mysqli_query($con,"update tblfaq_posts set Is_Active=1 where id='$uid'");
if($query)
{
$msg=$lang['trash_post_restore']; 
}
else{
$error=$lang['manage_post_error']; 
} 
}
// Code for Forever deletionparmdel
if($_GET['presid'])
{
$id=intval($_GET['presid']);
$query=mysqli_query($con,"delete from  tblfaq_posts  where id='$id'");
$delmsg=$lang['manage_post_msg'];;
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
    <title>Kaizen | FAQ |  Trash Posts
    </title>
    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="../plugins/morris/morris.css">
    <!-- jvectormap -->
    <link href="../plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/menu.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="../plugins/switchery/switchery.min.css">
    <link rel="stylesheet" type="text/css" href="assets/DataTables/datatables.min.css"/>
    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
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
                  <h4 class="page-title"> <?php echo  $lang['leftbar_trash_name']?>
                  </h4>
                  <ol class="breadcrumb p-0 m-0">
                    <li>
                      <a href="#"><?php echo  $lang['topheader_admin_name']?>
                      </a>
                    </li>
                    <li>
                      <a href="dashboard.php"> <?php echo  $lang['leftbar_dashboard_name']?>
                      </a>
                    </li>
                    <li class="active">
                    <?php echo  $lang['leftbar_trash_name']?>
                    </li>
                  </ol>
                  </ol>
                  <div class="clearfix">
                  </div>
                </div>
              </div>
            </div>
            <!-- end row -->
            <div class="row">
              <div class="col-sm-6">  
                <?php if($delmsg){ ?>
                <div class="alert alert-danger" role="alert">
                  <strong>Oh snap!
                  </strong> 
                  <?php echo htmlentities($delmsg);?>
                </div>
                <?php } ?>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="card-box">
                    <div class="table-responsive">
                      <table id="example2" class="display table table-striped table-bordered"
                             cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th><?php echo  $lang['common_title']?>
                            </th>
                            <th><?php echo  $lang['common_registerdate']?>
                            </th>
                            <th><?php echo  $lang['common_updatedate']?>
                            </th>
                            <th><?php echo  $lang['common_action']?>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
$query=mysqli_query($con,"select tblfaq_posts.id as uiid,tblfaq_posts.PostTitle as title,tblfaq_posts.CreationDate as creationdate,tblfaq_posts.Updationdate as updationdate from tblfaq_posts where tblfaq_posts.Is_Active=0");
$rowcount=mysqli_num_rows($query);
if($rowcount==0)
{
?>
                          <tr>
                            <td colspan="6" align="center">
                              <h3 style="color:red"><?php echo  $lang['common_no_record']?>
                              </h3>
                            </td>
                          <tr>
                            <?php 
} else {
while($row=mysqli_fetch_array($query))
{
?>
                          <tr>
                            <td>
                               <?php echo htmlentities($row['title']);?>
                              </td>
                            <td>
                              <?php echo htmlentities($row['creationdate'])?>
                            </td>
                            <td>
                              <?php echo htmlentities($row['updationdate'])?>
                            </td>
                            <td>
                              <a href="faq-trash-post.php?uid=<?php echo htmlentities($row['uiid']);?>&&action=restore" onclick="return confirm('<?php echo $lang['trash_post_restore_confirm']?>;')"> 
                                <i class=ion-arrow-return-right title="<?php echo  $lang['trash_post_restore_title']?>">
                                </i>
                              </a>
                              &nbsp;
                              <a href="faq-trash-post.php?presid=<?php echo htmlentities($row['uiid']);?>&&action=perdel" onclick="return confirm('<?php echo $lang['trash_post_delete']?>;')">
                                <i class="fa fa-trash-o" style="color: #f05050" title="<?php echo  $lang['trash_post_delete_title']?>">
                                </i>
                              </a> 
                            </td>
                          </tr>
                          <?php } }?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
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
        <!-- CounterUp  -->
        <script src="assets/plugins/waypoints/jquery.waypoints.min.js"></script>
        <script src="assets/plugins/counterup/jquery.counterup.min.js"></script>
        <!--Morris Chart-->
		<script src="assets/plugins/morris/morris.min.js"></script>
		<script src="assets/plugins/raphael/raphael-min.js"></script>
        <!-- Load page level scripts-->
        <script src="assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
        <script src="assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
        <script src="assets/plugins/jvectormap/gdp-data.js"></script>
        <script src="assets/plugins/jvectormap/jquery-jvectormap-us-aea-en.js"></script>
        <!-- Dashboard Init js -->
		<script src="assets/pages/jquery.blog-dashboard.js"></script>
        <!-- App js -->
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>
 <!-- ========== PAGE JS FILES ========== -->
 <?php
        if($_SESSION['lang'] == 'en') 
{?>
 <script src="assets/DataTables/datatables-eng.js"></script>
  <?php }
else if($_SESSION['lang'] == 'jp')
{?>
 <script src="assets/DataTables/datatables.js"></script>
  <?php } ?>
    
<!-- ========== THEME JS ========== -->
<script src="js/main.js"></script>
<script>
        $(function ($) { 
            $('#example2').DataTable({
              "columnDefs": [
{ "orderable": false, "targets": [0,3] }
]
            });
          });
    </script>
</body>
</html>
<?php } ?>