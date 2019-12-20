<?php
error_reporting(0);
include('../../news/includes/config.php');
require_once "../../config.php";
$call_sign = "kai";

if (isset($_POST['register'])) {

    $fullname=$_POST['fullname'];
    $company=$_POST['company'];
    $email=$_POST['email'];
    $enquiery=$_POST['enquiery'];

    $to = "$email";
    $to1 = "arivu@e-kjs.jp";
    $subject = mb_encode_mimeheader("Thanks For Your Inquiry. Please do not reply to this E-mail, it is auto-generated");
    $subject1 = mb_encode_mimeheader("Kaizen2.0- New inquiry from the customer"); 
    $headers = 'From: info@e-kjs.jp' . "\n";
    $headers .= 'MIME-Version: 1.0' . "\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\n";
    $message = "<html><body style=font-family:メイリオ leftmargin=10 topmargin=0 marginheight=0 marginwidth=0 height=100%>
                    <table width=100% margin=0>  <tbody>  <!---outer table -->
                    <tr><td>Dear $fullname </td></tr><br>
                    <tr><td></td></tr>
                    <tr><td>Thank you for your inquiry to us, Kaizen2.0 project! </td></tr> 
                    <tr><td></td></tr>                              
                    <tr><td>Now, we are checking your inquiry,so please wait a few days for our reply to you.Thank you. </td></tr><br>
                    <tr><td></td></tr>
                    <tr><td>Best Regards,</td></tr>
                    <tr><td>------------------------------------------------------------------------------------------------------------</td></tr><br>
                    <tr><td>KAIZEN2.0: MICROLEARNING WITH A DIGITAL BRAIN project </td></tr>
                    <tr><td>email:〇〇〇〇 </td></tr>
                    <tr><td>HP:〇〇〇〇 </td></tr>
                    <tr><td>------------------------------------------------------------------------------------------------------------</td></tr><br>
                    </table>
                    </body>
                    </html>";
    mail($to,$subject,$message, $headers);

    $message1 = "<html><body style=font-family:メイリオ leftmargin=10 topmargin=0 marginheight=0 marginwidth=0 height=100%>
                    <table width=100% margin=0>  <tbody>  <!---outer table -->
                    <tr><td>Dear Kaizen2.0 Admin,</td></tr><br>
                    <tr><td></td></tr>
                    <tr><td>We have a new inquiry from the below mentioned customer</td></tr><br>
                    <tr><td></td></tr>
                    <tr><td>------------------------------------------------------------------------------------------------------------</td></tr><br>
                    <tr><td><strong>Name：</strong>$fullname</td></tr>
                    <tr><td><strong>Company：</strong>$company</td></tr>
                    <tr><td><strong>E-mail：</strong>$email</td></tr>
                    <tr><td><strong>Message：</strong>$enquiery</td></tr>
                    <tr><td>------------------------------------------------------------------------------------------------------------</td></tr><br>
                    <tr><td>Please respond to the inquiry as soon as possible.</td></tr>
                    <tr><td><hr></td></tr>
                    </table>
                    </body>
                    </html>";
    mail($to1,$subject1,$message1, $headers);
    session_destroy();
  }

  // セッションがあればメニューへ
  if (isset($_SESSION['auth']['student_id'])) {
      $base = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
      //header('Location: ' . $base . '/student/menu/');
      header('Location: ' . $base . '/kaizen/student/info.php');
      exit();
  }

  // post データより
  $id = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
  $pw = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

  if ($id != '' && $pw != '') {
      $curl = new Curl($url);
      $student = new StudentAuth($call_sign . $id, $pw);
      $data = $student->authCheck();
      $result = $curl->send($data);

      if ($result['enable'] == 1 && $result['joining'] == 1) {
          $curl->send($student->updateAccessDate($result["student_id"]));
          //debug($result);
          $_SESSION['auth'] = $result;
          //$_SESSION['auth']['level'] = 'student';
          $base = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
          //header('Location: ' . $base . '/student/menu/');
          header('Location: ' . $base . '/kaizen/student/info.php');
          exit();
      }
  }

  

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kaizen2.0</title>
    <meta name="description" content="Microlearning Project">
    <meta name="keywords" content="Microlearning Project">
    <link href="https://fonts.googleapis.com/css?family=Crimson+Text:400,400i,600|Montserrat:200,300,400" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="../../assets/fonts/ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="../../assets/fonts/law-icons/font/flaticon.css">
    <link rel="stylesheet" href="../../assets/fonts/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/css/slick.css">
    <link rel="stylesheet" href="../../assets/css/slick-theme.css">
    <link rel="stylesheet" href="../../assets/css/helpers.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/landing-2.css">
</head>
<body data-spy="scroll" data-target="#pb-navbar" data-offset="200">
     <!-- START nav -->
    <nav class="navbar navbar-expand-lg navbar-dark pb_navbar pb_scrolled-light" id="pb-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.html"><b>Kaizen 2.0</b></a>
            <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#probootstrap-navbar" aria-controls="probootstrap-navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span><i class="ion-navicon"></i></span>
            </button>
            <div class="collapse navbar-collapse" id="probootstrap-navbar">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="#section-home"><b>Home</b></a></li>
                    <li class="nav-item"><a class="nav-link" href="#section-introduction"><b>Introduction</b></a></li>
                    <li class="nav-item"><a class="nav-link" href="#section-registration"><b>Registration</b></a></li>
                    <li class="nav-item"><a class="nav-link" href="#section-news"><b>News</b></a></li>
                    <li class="nav-item"><a class="nav-link" href="#section-enquiery"><b>Enquiry</b></a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- END nav -->


    <!--START Section -->
      <section class="pb_cover_v3 overflow-hidden cover-bg-indigo cover-bg-opacity-8 text-left pb_gradient_v1 pb_slant-light" id="section-home" style="background-image: url(../../assets/images/home1.jpg)">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-6">
                    <h3 class="heading mb-3 pb_font-59"><b>Digital<br> AI-powered Microlearning Platform</b> </h3>
                    <div class="sub-heading">
                        <p class="mb-4">To improve productivity by<span class="price"> recognizing</span> the existing situation and <span class="price"> solving</span> the problems of the organization <span class="price">continuously.</span> </p>
                    </div>
                </div>
                <div class="col-md-1">
                </div>
                <div class="col-md-5 relative align-self-center">
                    <form action=<?php echo $_SERVER[ 'REQUEST_URI'] ?> method="POST" class="bg-white rounded pb_form_v1">
                        <h3 class="mb-4 mt-0 text-center">KAIZEN Microlearning Platform</h3>
                        <h4 class="mb-4 mt-0 text-center">Login</h4>
                        <div class="form-group">
                            <input type="text" class="form-control pb_height-50 reverse" placeholder="ID" name="username" value="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control pb_height-50 reverse" placeholder="Password" name="password" value="<?php echo htmlspecialchars($pw, ENT_QUOTES); ?>">
                        </div>
                        <div class="form-group">
                            <input type="submit" name="submit" class="btn btn-primary btn-lg btn-block pb_btn-pill  btn-shadow-blue" value="login">
                        </div>
                    </form>
             <?php
                if ($id != '' && $pw != '' && ($result['enable'] == '' && $result['joining'] == '')) {
                    print "<script language=javascript>alertMessage('I was unable to log in. Please confirm the student ID and password and re-enter.')</script>";
                } else if ($id != '' && $pw != '' && ($result['enable'] == 0 || $result['joining'] == 0)) {
                    print "<script language=javascript>alertMessage('The student ID and password you entered are currently unavailable. Please contact the administrator.')</script>";
                } else if ($id != '' && $pw == '') {
                    print "<script language=javascript>alertMessage('Password has not been entered')</script>";
                } else if ($id == '' && $pw != '') {
                    print "<script language=javascript>alertMessage('The student ID has not been entered.')</script>";
                }
            ?>
                </div>
            </div>
        </div>
      </section>
    <!-- END Section -->


    <!--START Section -->
    <section class="pb_section bg-light pb_slant-white pb_pb-250" id="section-introduction">
        <div class="container">
            <div class="row justify-content-center mb-2">
                <div class="col-md-6 text-center mb-2">
                    <h2>What is Kaizen?</h2>
                    <br>
                </div>
                <h4>“Kaizen” is a Japanese word which means <span class="price">‘change for the better’.</span></h4>
                <img src="../../assets/images/kaizen.png" alt="Image placeholder" class="img-fluid">
            </div>
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-6 pl-md-12 pl-sm-12">
                    <div class="media pb_feature-v2 text-left mb-1 mt-5">
                        <div class="media-body">
                            <h2>Kaizen is a Japanese methodology which has a proven track-record in improving productivity and quality. Kaizen has been increasingly adopted by organization around the world. Although Kaizen has started from industries, it is now spread out to all kinds of public and private sectors.</h2>
                            <br>
                        </div>
                    </div>
                </div>
                <div class="col-lg-1">
                </div>
                <div class="col-lg-5 col-md-12 col-sm-12">
                    <img data-toggle="modal" data-target="#homeVideo" class="img-fluid" alt="Image placeholder" src="../../assets/images/thumbnail.png" width="490" height="300" onclick="playVid()" />
                    <div class="modal fade" id="homeVideo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="pauseVid()">Close</button>
                                <div class="embed-responsive embed-responsive-16by9">
                                    <video id="gossVideo" class="embed-responsive-item" controls="controls" poster="https://www.gossettmktg.com/video/dangot.png">
                                        <source src="../../assets/images/video.mp4" type="video/mp4">
                                        <source src="../../assets/images/video.webm" type="video/webm">
                                        <source src="../../assets/images/video.ogv" type="video/ogg">
                                        <object type="application/x-shockwave-flash" data="https://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" width="690" height="300">
                                            <param name="movie" value="https://releases.flowplayer.org/swf/flowplayer-3.2.1.swf">
                                            <param name="allowFullScreen" value="true">
                                            <param name="wmode" value="transparent">
                                            <param name="flashVars" value="config={'playlist':['http%3A%2F%2Fwww.gossettmktg.com%2Fvideo%2Fdangot.png',{'url':'http%3A%2F%2Fwww.gossettmktg.com%2Fvideo%2Fdangot.mp4','autoPlay':false}]}">
                                            <img alt="Image placeholder" src="../../assets/images/thumbnail.png" class="img-fluid" title="No video playback capabilities, please download the video below">
                                        </object>
                                    </video>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
              </div>
        </div>
      </section>
    <!-- END Section -->


    <!--START Section -->
    <section class="pb_section pb_slant-light">
        <div class="container">
            <div class="row justify-content-center mb-3">
                <div class="col-md-6 text-center mb-3">
                    <!-- <h5 class="text-uppercase pb_font-15 mb-2 pb_color-dark-opacity-3 pb_letter-spacing-2"><strong>Features</strong></h5> -->
                    <h2>What is the Benefit of Kaizen?</h2>
                </div>
            </div>
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-4">
                    <h2><i class="ion-fork-repo pb_icon-gradient"></i>  Objectives</h2>
                    <p class="pb_font-20">It contributes to improve not only productivity but also quality and delivery as well as reducing cost by recognizing the existing situation and solving the problems of the organization continuously and gradually.</p>
                    <h2><i class="ion-arrow-graph-up-right pb_icon-gradient"></i>  Benefits </h2>
                    <p class="pb_font-20">“Kaizen” methodology can help a company to create “continuous improvement culture” to meet in/external customers’ satisfaction and expectation.Kaizen also contributes to increase income and profit by raising productivity and reducing cost.</p>
                </div>
                <div class="col-lg-1">
                </div>
                <div class="col-lg-7">
                    <img src="../../assets/images/benefit-diagram.png" alt="Image placeholder" class="img-fluid">
                </div>
            </div>
        </div>
    </section>
    <!-- END section -->


    <!--START Section -->
    <section class="pb_section bg-light pb_slant-white">
        <div class="container">
            <div class="row justify-content-center mb-3">
                <div class="col-md-8 text-center mb-">
                    <h2>How does the `Kaizen2.0`project work?</h2>
                    <br>
                    <h5 class="text-uppercase pb_font-15 mb-2 pb_color-dark-opacity-3 pb_letter-spacing-2"><strong>Micro-learning environment</strong></h5>
                </div>
            </div>
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-4 pl-md-4 pl-sm-4">
                    <p class="pb_font-24">This project is provided for any SMEs in Kenya to join. By your internet connected devices such as smartphone, tablet and PC, you can learn about Kaizen by yourself with micro-learning platform. </p>
                </div>
                <div class="col-md-1">
                </div>
                <div class="col-lg-7 pl-md-7 pl-sm-7">
                    <img src="../../assets/images/work1.png" alt="Image placeholder" class="img-fluid">
                </div>
            </div>
        </div>
    </section>
    <!-- END section -->


    <!--START Section -->
    <section class="pb_section pb_slant-light">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-md-6 text-center mb-4">
                    <h5 class="text-uppercase pb_font-15 mb-2 pb_color-dark-opacity-3 pb_letter-spacing-2"><strong>Micro-learning environment</strong></h5>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-7 col-md-12 col-sm-12">
                    <img src="../../assets/images/work2.png" alt="Image placeholder" class="img-fluid">
                </div>
                <div class="col-lg-1">
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <p class="pb_font-24">You create your own account for the micro-learning platform. Then you watch and learn about Kaizen from the video lessons created by Japanese experts. Afterwards, you conduct Kaizen activity by yourself and report its results to Japanese experts. They will provide feedbacks and suggestions to you.
                        <br>
                        <br>
                        <br>
                        <br> </p>
                </div>
            </div>
        </div>
    </section>
    <!-- END section -->


    <!--START Section -->
    <section class="pb_section bg-light pb_slant-white" id="section-registration">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-md-6 text-center mb-5">
                    <h5 class="text-uppercase pb_font-15 mb-2 pb_color-dark-opacity-3 pb_letter-spacing-2"><strong>Kaizen Member</strong></h5>
                    <h2>Registration And Login</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md">
                    <div class="pb_pricing_v1 p-5 border text-center bg-white">
                        <h3 class="price">Registration for KAIZEN Member</h3>
                        <br>
                        <!-- <span class="price">Kaizen micro-learning program</span><br><br> -->
                        <p class="pb_font-18">Please click the below button and fill The Assesment form for registration</p>
                        <p class="mb-0"><a href="https://docs.google.com/forms/d/e/1FAIpQLSci8dNssfkZ59aYltDatjGCoTm6F2Fyn6mFBhbd47WPyAWJtg/viewform" target="_blank" role="button" class="btn btn-primary btn-shadow-blue">Registration</a></p>
                    </div>
                </div>
                <div class="col-md">
                    <div class="pb_pricing_v1 p-5 border  text-center bg-white">
                        <h3 class="price">Login for the Microlearning platform</h3>
                        <br>
                        <!-- <span class="price">ThinkBoard</span><br><br> -->
                        <p class="pb_font-18">Click the below button to login the KAIZEN Microlearning platform</p>
                        <p class="mb-0"><a href="#section-home" role="button" class="btn btn-primary btn-shadow-blue">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ENDs ection -->


    <!--START Section -->
    <section class="pb_section pb_slant-white" id="section-news">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-md-6 text-center mb-5">
                    <h5 class="text-uppercase pb_font-15 mb-2 pb_color-dark-opacity-3 pb_letter-spacing-2"><strong></strong></h5>
                    <h2>News & Updates</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-md">
                    <div id="pb_faq" class="pb_accordion" data-children=".item">

                        <?php
$query=mysqli_query($con,"SELECT tblcms_posts.id,tblcms_posts.PostTitle,tblcms_posts.PostDetails,tblcms_posts.PostingDate from tblcms_posts WHERE tblcms_posts.Is_Active=1 ORDER BY tblcms_posts.id DESC ");
while ($row=mysqli_fetch_array($query))
{
$newsdet=$row['PostDetails'];
$findQuery=mysqli_query($con,"SELECT MAX(PostingDate) as last_date FROM tblcms_posts WHERE tblcms_posts.Is_Active=1");
$findQuery =mysqli_fetch_array($findQuery);
$datetime1 = new DateTime($row['PostingDate']);
$datetime2 = new DateTime();
$interval = $datetime1->diff($datetime2);
if($findQuery['last_date']==$row['PostingDate'] &&  $interval->format('%a') < 30)
{
?>
                            <div class="item">
                                <a data-toggle="collapse" data-parent="#pb_faq" href="#pb_faq<?php echo$row['id'];?>" aria-expanded="false" aria-controls="pb_faq<?php echo$row['id'];?>" class="pb_font-22 py-4">
                                    <p class="day">
                                        <?php echo $row['PostingDate'];?>
                                    </p>
                                    <p class="title">
                                        <?php echo $row['PostTitle'];?> <img src="../../assets/images/new02.png" style="height: 22px;"></p>
                                </a>
                                <div id="pb_faq<?php echo$row['id'];?>" class="collapse" role="tabpanel">
                                    <div class="py-3">
                                        <?php echo (substr($newsdet,0));?>
                                    </div>
                                </div>
                            </div>
                            <?php  }else{ ?>
                                <div class="item">
                                    <a data-toggle="collapse" data-parent="#pb_faq" href="#pb_faq<?php echo$row['id'];?>" aria-expanded="false" aria-controls="pb_faq<?php echo$row['id'];?>" class="pb_font-22 py-4">
                                        <p class="day">
                                            <?php echo $row['PostingDate'];?>
                                        </p>
                                        <p class="title">
                                            <?php echo $row['PostTitle'];?> <img src="../../assets/images/new02.png" style="height: 22px;"></p>
                                    </a>
                                    <div id="pb_faq<?php echo$row['id'];?>" class="collapse" role="tabpanel">
                                        <div class="py-3">
                                            <?php echo (substr($newsdet,0));?>
                                        </div>
                                    </div>
                                </div>
                                <?php }}?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ENDs ection -->


    <!--START Section -->
    <section class="pb_xl_py_cover overflow-hidden  pb_slant-light pb_gradient_v1 cover-bg-opacity-8" id="section-enquiery" style="background-image: url(../../assets/images/1900x1200_img_5.jpg)">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-5 justify-content-center">
                    <h3 class="heading mb-5 pb_font-40">“Learning experiences are like journeys. The journey starts where the learning is now, and ends when the learner is successful. The end of the journey isn’t knowing more, it’s doing more.”</h3>
                    <div class="sub-heading text-indent">
                        <p class="pl-5"> – Julie Dirksen</p>
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-5">
                    <form class="bg-white rounded pb_form_v1" method="POST" id="enquiery" autocomplete="off">
                        <h2 class="mb-4 mt-0 text-center">Enquiry</h2>
                        <div class="form-group">
                            <input type="text" class="form-control py-3 reverse" name="fullname" placeholder="Your Name" required/>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control py-3 reverse" name="company" placeholder="Your Company Name" required/>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control py-3 reverse" name="email" placeholder="Your Email" required/>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">Enquiery Details </label>
                            <textarea class="form-control pb_height-150 reverse" name="enquiery" required></textarea>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary btn-lg btn-block pb_btn-pill  btn-shadow-blue" name="register" value="Send" onclick="return confirm('Are you sure to register all information you entered');">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- ENDs ection -->


    <!--START modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="myModalLabel">Thank you for contacting us!</h4>
                </div>
                <div class="modal-body">
                    We have received your message and would like to thank you for writing to us.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" name="close">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- ENDs model -->


    <!--START Footer -->

    <footer class="pb_footer bg-light" role="contentinfo">
        <div class="container">
            <div class="row text-center">
                <div class="col-sm mt-4">
                    <a href="https://www.thegpsc.org/sites/gpsc/files/partnerdocs/program_book_-_world_bank_learning_event_-_disruptive_technologies_for_development_20180627.pdf" target="blank"><img src="../../assets/images/DT4D11.png" alt="Image placeholder" height="175"></a>
                </div>
                <div class="col-sm mt-4">
                    <a href="https://www.worldbank.org" target="blank"><img src="../../assets/images/worldbank1.png" alt="Image placeholder" height="175"></a>
                </div>
                <div class="col-sm mt-4">
                    <a href="https://www.jpc-net.jp/eng/" target="blank"><img src="../../assets/images/jpc1.png" alt="Image placeholder" height="175"></a>
                </div>
                <div class="col-sm mt-4">
                    <a href="https://www.avivatechnologies.com/home.html" target="blank"><img src="../../assets/images/aviva.png" alt="Image placeholder" height="175"></a>
                </div>
                <div class="col-sm mt-4">
                    <a href="https://www.e-kjs.jp/" target="blank"><img src="../../assets/images/kjs1.png" alt="Image placeholder" height="175"></a>
                </div>
            </div>
        </div>

        <br>
        <br>
        <div class="row">
            <div class="col text-center">
                <p class="pb_font-14">&copy; All Rights Reserved by <a href="https://www.worldbank.org" target="_blank" rel="nofollow">The World Bank</a></p>
                <p class="pb_font-14">Design and Developed By <a href="https://www.e-kjs.jp/" target="_blank" rel="nofollow">KJS</a></p>
            </div>
        </div>
        </div>
    </footer>
    <!-- END Footer -->


    <!-- loader -->
    <div id="pb_loader" class="show fullscreen">
        <svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#1d82ff" />
        </svg>
    </div>

    <script src="../../assets/js/jquery.min.js"></script>

    <script src="../../assets/js/popper.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/slick.min.js"></script>
    <script src="../../assets/js/jquery.mb.YTPlayer.min.js"></script>

    <script src="../../assets/js/jquery.waypoints.min.js"></script>
    <script src="../../assets/js/jquery.easing.1.3.js"></script>

    <script src="../../assets/js/main.js"></script>
    <?php if (isset($_POST['register'])) { ?>
        <script>
            $('#myModal').modal('show')
        </script>
        <?php } ?>
            <script>
                var vid = document.getElementById("gossVideo");

                function playVid() {
                    vid.play();
                }

                function pauseVid() {
                    vid.pause();
                }
            </script>
</body>

</html>
