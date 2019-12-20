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
    ?>
<!DOCTYPE html>
<html lang="jp">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CMS, etc.">
        <meta name="author" content="Coderthemes">
        <!-- App title -->
        <title>CMS | Dashboard</title>
		<link rel="stylesheet" href="../plugins/morris/morris.css">

        <!-- App css -->
        <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/menu.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="../plugins/switchery/switchery.min.css">
        <script src="assets/js/modernizr.min.js"></script>
    </head>
    <body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            <!-- Top Bar Start -->
            <div class="topbar">
                <!-- LOGO -->
                <div class="topbar-left">
                    <a href="index.html" class="logo"><span>Kaizen2.0<span>CMS</span></span><i class="mdi mdi-layers"></i></a>
                    <!-- Image logo -->
                    <!--<a href="index.html" class="logo">-->
                        <!--<span>-->
                            <!--<img src="assets/images/logo.png" alt="" height="30">-->
                        <!--</span>-->
                        <!--<i>-->
                            <!--<img src="assets/images/logo_sm.png" alt="" height="28">-->
                        <!--</i>-->
                    <!--</a>-->
                </div>
                <!-- Button mobile view to collapse sidebar menu -->
            <?php include('includes/topheader.php');?>
            </div>
            <!-- Top Bar End -->
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
                                    <h4 class="page-title"><?php echo  $lang['leftbar_dashboard_name']?></h4>
                                    <ol class="breadcrumb p-0 m-0">
                                        <li>
                                            <a href="#"><?php echo  $lang['topheader_admin_name']?></a>
                                        </li>
                                        <li class="active">
                                        <?php echo  $lang['leftbar_dashboard_name']?>
                                        </li>
                                    </ol>
                                    <div class="clearfix"></div>
                                </div>
							</div>
						</div>
                        <!-- end row -->
                        <div class="row">
<a href="news-manage-post.php">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="card-box widget-box-one">
                                    <i class="mdi mdi-chart-areaspline widget-one-icon"></i>
                                    <div class="wigdet-one-content">
                                        <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Statistics"><?php echo  $lang['leftbar_news_name']?></p>
                                        <?php $query=mysqli_query($con,"select * from tblcms_posts where Is_Active=1");
$countcat=mysqli_num_rows($query);
?>

                                        <h2><?php echo htmlentities($countcat);?> <small></small></h2>
                                    
                                    </div>
                                </div>
                            </div></a><!-- end col -->
     
                         
                            <div class="row">
<a href="faq-manage-post.php">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="card-box widget-box-one">
                                    <i class="mdi mdi-chart-areaspline widget-one-icon"></i>
                                    <div class="wigdet-one-content">
                                        <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Statistics"><?php echo  $lang['leftbar_news_faq']?></p>
                                        <?php $query=mysqli_query($con,"select * from tblfaq_posts where Is_Active=1");
$countcat=mysqli_num_rows($query);
?>

                                        <h2><?php echo htmlentities($countcat);?> <small></small></h2>
                                    
                                    </div>
                                </div>
                            </div></a><!-- end col -->
   
<br>
<br>
<div class="row-xs-9">
							<div class="col-xs-12">
								<div class="page-title-box">
                                    <h2 class="page-title"> <?php echo  $lang['dashboared_latest_news']?> </h2>
                                     <div class="clearfix"></div>
                                </div>
							</div>
						</div>
                       <div class="row-xs-5">
                            <div class="col-sm-12">
                                                    
                                    <div class="table-responsive">
<table class="table table-colored table-centered table-inverse m-0">
<thead>
<tr>                                          
<th> <?php echo  $lang['common_title']?></th>
<th> <?php echo  $lang['common_registerdate']?></th>
<th> <?php echo  $lang['common_updatedate']?></th>
</tr>
</thead>
<tbody>
<?php
$query=mysqli_query($con,"SELECT tblcms_posts.id as postid,tblcms_posts.PostTitle as title,tblcms_posts.CreationDate as creationdate,tblcms_posts.Updationdate as updationdate FROM tblcms_posts WHERE CreationDate=(SELECT MAX(CreationDate) FROM tblcms_posts WHERE tblcms_posts.Is_Active=1)");
$rowcount=mysqli_num_rows($query);
if($rowcount==0)
{
?>
<tr>

<td colspan="4" align="center"><h3 style="color:red"> <?php echo  $lang['common_no_record']?></h3></td>
<tr>
<?php 
} else {
while($row=mysqli_fetch_array($query))
{
?>
 <tr>
 <td><?php echo htmlentities($row['title']);?></td>
<td><?php echo htmlentities($row['creationdate'])?></td>
<td><?php echo htmlentities($row['updationdate'])?></td>
 </tr>
<?php } }?>
                                               
                                            </tbody>
                                        </table>
                                    </div>
                               
                        

                    </div> <!-- container -->
                </div> <!-- content -->
<?php include('includes/footer.php');?>
            </div>
          
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
        <script src="../plugins/switchery/switchery.min.js"></script>

        <!-- Counter js  -->
        <script src="../plugins/waypoints/jquery.waypoints.min.js"></script>
        <script src="../plugins/counterup/jquery.counterup.min.js"></script>

        <!--Morris Chart-->
		<script src="../plugins/morris/morris.min.js"></script>
		<script src="../plugins/raphael/raphael-min.js"></script>

        <!-- Dashboard init -->
        <script src="assets/pages/jquery.dashboard.js"></script>

        <!-- App js -->
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>

    </body>
</html>
<?php } ?>