<?php
error_reporting(0);
include('news/includes/config.php');
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

		<link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="assets/fonts/ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="assets/fonts/law-icons/font/flaticon.css">

    <link rel="stylesheet" href="assets/fonts/fontawesome/css/font-awesome.min.css">


    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/slick-theme.css">

    <link rel="stylesheet" href="assets/css/helpers.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/landing-2.css">
	</head>
	<body data-spy="scroll" data-target="#pb-navbar" data-offset="200">

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
            <!-- <li class="nav-item cta-btn ml-xl-2 ml-lg-2 ml-md-0 ml-sm-0 ml-0"><a class="nav-link" href="https://www.e-kjs.jp//" target="_blank"><span class="pb_rounded-4 px-4">Get Started</span></a></li> -->
          </ul>
        </div>
      </div>
    </nav>
    <!-- END nav -->


       
    <section class="pb_cover_v3 overflow-hidden cover-bg-indigo cover-bg-opacity-8 text-left pb_gradient_v1 pb_slant-light" id="section-home" style="background-image: url(assets/images/home1.jpg)">
      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-6">
            <h3 class="heading mb-3 pb_font-59"><b>Digital KAIZEN<br> AI-powered Microlearning Platform</b> </h3>
            <div class="sub-heading">
              <p class="mb-4">To improve productivity by<span class="price b"> recognizing</span> the existing situation and <span class="price"> solving</span>  the problems of the organization <span class="price">continuously.</span> </p>
            </div>
          </div>
          <div class="col-md-1">
          </div>
          <div class="col-md-5 relative align-self-center">

            <form action="#" class="bg-white rounded pb_form_v1">
              <h2 class="mb-4 mt-0 text-center">ThinkBoard LMS</h2>
              <h3 class="mb-4 mt-0 text-center">Login</h3>
              <div class="form-group">
                <input type="text" class="form-control pb_height-50 reverse" placeholder="Employee ID">
              </div>
              <div class="form-group">
                <input type="text" class="form-control pb_height-50 reverse" placeholder="Password">
              </div>
              <div class="form-group">
                <input type="submit" class="btn btn-primary btn-lg btn-block pb_btn-pill  btn-shadow-blue" value="Login">
              </div>
            </form>

          </div>
        </div>
      </div>
    </section>
    <!-- END section -->

    <section class="pb_section bg-light pb_slant-white pb_pb-250" id="section-introduction">
      <div class="container">
      <div class="row justify-content-center mb-2">
          <div class="col-md-6 text-center mb-2">
            <h2>What is Kaizen?</h2>
          </div>
        </div>
        <div class="row align-items-center justify-content-center">
        <div class="col-lg-5 col-md-6 col-sm-12">
            
              <!-- <img src="assets/images/what.png" alt="Image placeholder" class="img-fluid">--> 
            <!-- <video width="320" height="240" controls>
              <source src="assets/images/video.mp4" type="video/mp4">
              <source src="assets/images/video.ogg" type="video/ogg">
            </video> --><br><br>
            <img data-toggle="modal" data-target="#homeVideo" class="img-fluid" alt="Image placeholder" src="assets/images/thumbnail.png" onclick="playVid()" />
            <div class="modal fade" id="homeVideo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <button type="button" class="btn btn-default" data-dismiss="modal" onclick="pauseVid()">Close</button>
                    <div class="embed-responsive embed-responsive-16by9">
                      <video id="gossVideo" class="embed-responsive-item" controls="controls" poster="http://www.gossettmktg.com/video/dangot.png">
                        <source src="assets/images/video.mp4" type="video/mp4">
                        <source src="assets/images/video.webm" type="video/webm">
                        <source src="assets/images/video.ogv" type="video/ogg">
                        <object type="application/x-shockwave-flash" data="https://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" width="353" height="190">
                          <param name="movie" value="https://releases.flowplayer.org/swf/flowplayer-3.2.1.swf">
                          <param name="allowFullScreen" value="true">		<param name="wmode" value="transparent">
                          <param name="flashVars" value="config={'playlist':['http%3A%2F%2Fwww.gossettmktg.com%2Fvideo%2Fdangot.png',{'url':'http%3A%2F%2Fwww.gossettmktg.com%2Fvideo%2Fdangot.mp4','autoPlay':false}]}">
                          <img alt="Image placeholder" src="assets/images/thumbnail.png" class="img-fluid"  title="No video playback capabilities, please download the video below">
                        </object>
                      </video>
                    </div>
                  </div>
                </div>
              </div><br><br><br> 
              <img src="assets/images/kaizen.png" alt="Image placeholder" class="img-fluid">
          </div>
          <div class="col-lg-1">
          </div>
          <div class="col-lg-6 pl-md-6 pl-sm-12"> <div class="media pb_feature-v2 text-left mb-1 mt-5">
                      <div class="media-body">
                      <h2>“Kaizen” is a Japanese word which means <span class="price">‘change for the better’.</span>  Kaizen is a Japanese methodology which has a proven track-record in improving productivity and quality. Kaizen has been increasingly adopted by organization around the world. Although Kaizen has started from industries, it is now spread out to all kinds of public and private sectors.</h2><br>
                         
                  </div>
                </div>
              </div>
              <!-- <div class="col-lg">

                <div class="media pb_feature-v2 text-left mb-1 mt-5">
                  <div class="pb_icon d-flex mr-3 align-self-start pb_w-15"><i class="ion-ios-speedometer-outline pb_icon-gradient"></i></div>
                  <div class="media-body">
                    <h3 class="mt-2 mb-2 heading">Fast Loading</h3>
                    <p class="text-sans-serif pb_font-16">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia.</p>
                  </div>
                </div>

                <div class="media pb_feature-v2 text-left mb-1 mt-5">
                  <div class="pb_icon d-flex mr-3 align-self-start pb_w-15"><i class="ion-ios-color-filter-outline  pb_icon-gradient"></i></div>
                  <div class="media-body">
                    <h3 class="mt-2 mb-2 heading">Component Based Design</h3>
                    <p class="text-sans-serif pb_font-16">Far far away, behind the word mountains, far from the countries Vokalia and Consonantia.</p>
                  </div>
                </div> -->

              </div>
            </div>

          </div>
        </div>
      </div>
    </section>
    <!-- END section -->

    
    <section class="pb_section pb_slant-light">
      <div class="container">
      <div class="row justify-content-center mb-3">
          <div class="col-md-6 text-center mb-3">
            <!-- <h5 class="text-uppercase pb_font-15 mb-2 pb_color-dark-opacity-3 pb_letter-spacing-2"><strong>Features</strong></h5> -->
            <h2>What is the Benefit of Kaizen</h2>
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
            
              <img src="assets/images/benefit-diagram.png" alt="Image placeholder" class="img-fluid">
        </div>
          </div>
        </div>
      </div>
    </section>
 <!-- END section -->
    <section class="pb_section bg-light pb_slant-white">
      <div class="container">
      <div class="row justify-content-center mb-3">
          <div class="col-md-8 text-center mb-">
            <h2>How does the `Kaizen2.0`project work?</h2><br>
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
            
            <img src="assets/images/work1.png" alt="Image placeholder" class="img-fluid">
            </div>
        
      <!-- <div class="col-md-1">
          </div> -->
          <!-- <div class="col-md-6">
          
                <h2><i class="ion-fork-repo pb_icon-gradient"></i>  Troubleshooting</h2>
                <p class="pb_font-20">This is the project for any SMEs in Kenya that the lessons of Japanese KAIZEN methodology for improvement of their productivity can be learned in anywhere and anytime through its online micro-learning platform by your internet connected devises such as smartphone, tablet and PC. </p>
                <br><br><br><img src="assets/images/work2.png" alt="Image placeholder" class="img-fluid">
           
                <h2><i class="ion-arrow-graph-up-right pb_icon-gradient"></i>  Desired results</h2>
                <p class="pb_font-20">“Kaizen” methodology can help a company to create “continuous improvement culture” to meet in/external customers’ satisfaction and expectation.</p>
               -->
        
          </div> 
 
      </div>
    </section>
    <!-- END section -->

    <section class="pb_section pb_slant-light">
      <div class="container">
      <div class="row justify-content-center mb-4">
          <div class="col-md-6 text-center mb-4">
      
            <h5 class="text-uppercase pb_font-15 mb-2 pb_color-dark-opacity-3 pb_letter-spacing-2"><strong>Micro-learning environment</strong></h5>
           
          </div>
        </div>
     
      <div class="row">
      <div class="col-lg-7 col-md-12 col-sm-12">
            
            <img src="assets/images/work2.png" alt="Image placeholder" class="img-fluid">
            </div>
             
            <div class="col-lg-1">
          </div>
        
        <div class="col-lg-4 col-md-12 col-sm-12">
          
               
            
                <p class="pb_font-24">You create your own account for the micro-learning platform. Then you watch and learn about Kaizen from the movie　lessons created by Japanese experts. Afterwards, you conduct Kaizen activity by yourself and report its results to Japanese experts. They will provide feedbacks and suggestions to you.<br><br><br><br> </p>
          </div>
         
          </div>
        </div>
      </div>
    </section>

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
              <h3>Kaizen Member Registration</h3><br>
              <span class="price">Kaizen micro-learning program</span><br><br>
              <p class="pb_font-15">Please click the button to  fill The Short Assesment form. We will contact you soon</p>
              <p class="mb-0"><a href="https://docs.google.com/forms/d/e/1FAIpQLSci8dNssfkZ59aYltDatjGCoTm6F2Fyn6mFBhbd47WPyAWJtg/viewform" target="_blank" role="button" class="btn btn-primary btn-shadow-blue">Registration</a></p>
            </div>
          </div>
          <div class="col-md">
            <div class="pb_pricing_v1 p-5 border  text-center bg-white">
              <h3>Learning Management System</h3><br>
              <span class="price">ThinkBoard</span><br><br>
              <p class="pb_font-15">Click the below button to login ThinkBoard LMS and start to learn</p>
              <p class="mb-0"><a  href="#section-home" role="button" class="btn btn-primary btn-shadow-blue">Login Page</a></p>
            </div>
          </div>
    
        </div>
      </div>
    </section>
    <!-- ENDs ection -->


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
?>                <div class="item">
                <a data-toggle="collapse" data-parent="#pb_faq" href="#pb_faq<?php echo$row['id'];?>" aria-expanded="false" aria-controls="pb_faq<?php echo$row['id'];?>" class="pb_font-22 py-4"><p class="day"><?php echo $row['PostingDate'];?></p><p class="title"><?php echo $row['PostTitle'];?> <img src="assets/images/new02.png" style="height: 22px;"></p></a>
                  <div id="pb_faq<?php echo$row['id'];?>" class="collapse" role="tabpanel">
                    <div class="py-3">
                    <?php echo (substr($newsdet,0));?>
                    </div>
                  </div>
                </div>
<?php  }else{ ?>
                   <div class="item">
                <a data-toggle="collapse" data-parent="#pb_faq" href="#pb_faq<?php echo$row['id'];?>" aria-expanded="false" aria-controls="pb_faq<?php echo$row['id'];?>" class="pb_font-22 py-4"><p class="day"><?php echo $row['PostingDate'];?></p><p class="title"><?php echo $row['PostTitle'];?> <img src="assets/images/new02.png" style="height: 22px;"></p></a>
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

    <!-- <section class="pb_section bg-light pb_slant-white" id="section-registration">
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
              <h3>Kaizen Member Registration</h3><br>
              <span class="price">Kaizen micro-learning program</span><br><br>
              <p class="pb_font-15">Please click the button to  fill The Short Assesment form. We will contact you soon</p>
              <p class="mb-0"><a href="https://docs.google.com/forms/d/e/1FAIpQLSci8dNssfkZ59aYltDatjGCoTm6F2Fyn6mFBhbd47WPyAWJtg/viewform" target="_blank" role="button" class="btn btn-primary btn-shadow-blue">Registration</a></p>
            </div>
          </div>
          <div class="col-md">
            <div class="pb_pricing_v1 p-5 border  text-center bg-white">
              <h3>Learning Management System</h3><br>
              <span class="price">ThinkBoard</span><br><br>
              <p class="pb_font-15">Click the below button to login ThinkBoard LMS and start to learn</p>
              <p class="mb-0"><a  href="#section-home" role="button" class="btn btn-primary btn-shadow-blue">Login Page</a></p>
            </div>
          </div>
    
        </div>
      </div>
    </section> -->
    <!-- ENDs ection -->
<!-- 
    <section class="pb_section pb_slant-white" id="section-faq">
      <div class="container">
        <div class="row justify-content-center mb-5">
          <div class="col-md-6 text-center mb-5">
            <h5 class="text-uppercase pb_font-15 mb-2 pb_color-dark-opacity-3 pb_letter-spacing-2"><strong>FAQ</strong></h5>
            <h2>Frequently Ask Questions</h2>
          </div>
        </div>
        <div class="row">
          <div class="col-md">
            <div id="pb_faq" class="pb_accordion" data-children=".item">
              <div class="item">
                <a data-toggle="collapse" data-parent="#pb_faq" href="#pb_faq1" aria-expanded="true" aria-controls="pb_faq1" class="pb_font-22 py-4">What is Instant?</a>
                <div id="pb_faq1" class="collapse show" role="tabpanel">
                  <div class="py-3">
                  <p>Pityful a rethoric question ran over her cheek, then she continued her way.</p>
                  <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
                  </div>
                </div>
              </div>
              <div class="item">
                <a data-toggle="collapse" data-parent="#pb_faq" href="#pb_faq2" aria-expanded="false" aria-controls="pb_faq2" class="pb_font-22 py-4">Is this available to my country?</a>
                <div id="pb_faq2" class="collapse" role="tabpanel">
                  <div class="py-3">
                    <p>A small river named Duden flows by their place and supplies it with the necessary regelialia. It is a paradisematic country, in which roasted parts of sentences fly into your mouth.</p>
                  </div>
                </div>
              </div>
              <div class="item">
                <a data-toggle="collapse" data-parent="#pb_faq" href="#pb_faq3" aria-expanded="false" aria-controls="pb_faq3" class="pb_font-22 py-4">How do I use the features of Instant App?</a>
                <div id="pb_faq3" class="collapse" role="tabpanel">
                  <div class="py-3">
                    <p>Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small line of blind text by the name of Lorem Ipsum decided to leave for the far World of Grammar.</p>
                  </div>
                </div>
              </div>
              <div class="item">
                <a data-toggle="collapse" data-parent="#pb_faq" href="#pb_faq4" aria-expanded="false" aria-controls="pb_faq4" class="pb_font-22 py-4">How much do the Instant App cost?</a>
                <div id="pb_faq4" class="collapse" role="tabpanel">
                  <div class="py-3">
                    <p>The Big Oxmox advised her not to do so, because there were thousands of bad Commas, wild Question Marks and devious Semikoli, but the Little Blind Text didn’t listen. She packed her seven versalia, put her initial into the belt and made herself on the way.</p>
                  </div>
                </div>
              </div>

              <div class="item">
                <a data-toggle="collapse" data-parent="#pb_faq" href="#pb_faq5" aria-expanded="false" aria-controls="pb_faq5" class="pb_font-22 py-4">I have technical problem, who do I email?</a>
                <div id="pb_faq5" class="collapse" role="tabpanel">
                  <div class="py-3">
                    <p>On her way she met a copy. The copy warned the Little Blind Text, that where it came from it would have been rewritten a thousand times and everything that was left from its origin would be the word "and" and the Little Blind Text should turn around and return to its own, safe country. </p>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section> -->

    <section class="pb_xl_py_cover overflow-hidden  pb_slant-light pb_gradient_v1 cover-bg-opacity-8" id="section-enquiery" style="background-image: url(assets/images/1900x1200_img_5.jpg)">
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
             <form action="#" class="bg-white rounded pb_form_v1">
              <h2 class="mb-4 mt-0 text-center">Enquiry</h2>
              <div class="form-group">
                <input type="text" class="form-control py-3 reverse" placeholder="Full name">
              </div>
              <div class="form-group">
                <input type="text" class="form-control py-3 reverse" placeholder="Company Name">
              </div>
              <div class="form-group">
                <input type="text" class="form-control py-3 reverse" placeholder="Email">
              </div>
              <div class="form-group">
                  <textarea class="form-control pb_height-150 reverse"></textarea>
                </div>

                
              <div class="form-group">
                <input type="submit" class="btn btn-primary btn-lg btn-block pb_btn-pill  btn-shadow-blue" value="Register">
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
    <!-- END section -->

    <footer class="pb_footer bg-light" role="contentinfo">
      <!-- <div class="container">
        <div class="row text-center">
          <div class="col-sm"> -->
          <div class="container">
			<div class="row text-center">
				<div class="col-sm mt-4">
					<a href="https://www.thegpsc.org/sites/gpsc/files/partnerdocs/program_book_-_world_bank_learning_event_-_disruptive_technologies_for_development_20180627.pdf" target="blank"><img src="assets/images/DT4D11.png" alt="Image placeholder" height="175"></a>
				</div>
				<div class="col-sm mt-4">
					<a href="https://www.worldbank.org" target="blank"><img src="assets/images/worldbank1.png" alt="Image placeholder" height="175"></a>
				</div>
				<div class="col-sm mt-4">
					<a href="https://www.jpc-net.jp/eng/" target="blank"><img src="assets/images/jpc1.png" alt="Image placeholder" height="175"></a>
        </div>
        <div class="col-sm mt-4">
					<a href="https://www.avivatechnologies.com/home.html" target="blank"><img src="assets/images/aviva.png" alt="Image placeholder" height="175"></a>
        </div>
        <div class="col-sm mt-4">
					<a href="https://www.e-kjs.jp/" target="blank"><img src="assets/images/kjs1.png" alt="Image placeholder" height="175"></a>
				</div>
			</div>
		</div>
           
        <br><br>
        <div class="row">
          <div class="col text-center">
            <p class="pb_font-14">&copy; All Rights Reserved by <a href="index.html" target="_blank" rel="nofollow">Kaizen 2.0</a></p>
            <p class="pb_font-14">Design and Developed By <a href="https://www.e-kjs.jp/" target="_blank" rel="nofollow">KJS</a></p>
          </div>
        </div>
      </div>
    </footer>

    <!-- loader -->
    <div id="pb_loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#1d82ff"/></svg></div>



    <script src="assets/js/jquery.min.js"></script>

    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/jquery.mb.YTPlayer.min.js"></script>

    <script src="assets/js/jquery.waypoints.min.js"></script>
    <script src="assets/js/jquery.easing.1.3.js"></script>

    <script src="assets/js/main.js"></script>

    <script>
    var vid = document.getElementById("gossVideo"); 

function playVid() { 
    vid.play(); 
} 

function pauseVid() { 
    vid.pause(); 
} </script>
	</body>
</html>
