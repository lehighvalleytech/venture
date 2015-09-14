<?php
$stripe_public = getenv('STRIPE_PUBLIC_KEY');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Venture Lehigh Valley</title>

	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@lvtech">
	<meta name="twitter:creator" content="@lvtech">
	<meta name="twitter:title" content="LVTech: Venture Lehigh Valley - One Weekend - Endless Possibilities">
	<meta name="twitter:description" content="Venture Weekend is open to individuals, teams, startup ideas, new products, pivots - in a word, Ventures. They become a reality over the weekend. Individuals collaborate on teams, teams challenge each other, friendships and connections form that outlast the weekend.">
	<meta name="twitter:image" content="http://<?php echo $_SERVER['HTTP_HOST'] ?>/images/wide.jpg">

	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700' rel='stylesheet' type='text/css'>
	
	<!--CSS-->
	<link rel="stylesheet" href="css/rwdgrid.css">
	<link rel="stylesheet" href="css/skew.css">
	<link rel="stylesheet" href="css/font-awesome.css">
	<link rel="stylesheet" href="css/cbp-fwslider.css">
	<link rel="stylesheet" href="css/responsive-nav.css">
	<link rel="stylesheet" href="css/owl.carousel.css">
	<link rel="stylesheet" href="css/owl.theme.css">
	<link rel="stylesheet" href="css/owl.transitions.css">
	<link rel="stylesheet" href="css/animate.css">
	<link rel="stylesheet" href="css/prettyPhoto.css" />
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/mediaqueries.css">
		
	<!--Javascript-->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="js/modernizr.custom.js"></script>
	<script src="js/jquery.cbpFWSlider.js"></script>
	<script src="js/responsive-nav.js"></script>
	<script src="js/owl.carousel.js"></script>
	<script src="js/wow.js"></script><script> new WOW().init();</script>
	<script src="js/jquery.counterup.js"></script>
	<script src="js/jquery.waypoints.js"></script>
	<script src="js/jquery.prettyPhoto.js"></script>
	
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<!--[if lt IE 9]>
		<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
	<![endif]-->

	<script src="https://checkout.stripe.com/checkout.js"></script>
	<script>
		var type = 'standard';

		var handler = StripeCheckout.configure({
			key: '<?php echo $stripe_public ?>',
			image: '/images/square.png',
			locale: 'auto',
			token: function(token) {
				console.log(token);
				$.ajax('/charge.php', {
					data: {
						stripe_email:  token.email,
						token:  token.id,
						name:   $('#name').val(),
						phone:  $('#phone').val(),
						email:  $('#email').val(),
						amount: amount
					},
					type: 'POST'
				});
			}
		});

		$(function(){
			$('.payment').on('click', function(e) {
				amount = $(this).attr('data-amount');
				// Open Checkout with further options
				handler.open({
					name: 'LVTech',
					description: 'Venture Lehigh Valley',
					amount: amount,
				});
				e.preventDefault();
			});

			// Close Checkout on page navigation
			$(window).on('popstate', function() {
				handler.close();
			});
		});

	</script>
</head>
<body>
	<div id="cbp-fwslider" class="cbp-fwslider skew-bottom-ccw">

	</div>
	<header class="header">
		<div class="top-bar">
			<div class="container">
				<div class="site-title">
					<img src="images/site-title.png" alt="site-title" />
					<!--<a href="#">Skewed</a>-->
				</div>
				<nav class="nav-collapse">
					<ul class="top-nav">
						<li><a href="#story">The Story</a></li>
						<li><a href="#weekend">Venture Weekend</a></li>
						<li><a href="#runway">The Runway</a></li>
						<li><a href="#tickets">Tickets</a></li>
					</ul>
				</nav>
				<script>
				  var navigation = responsiveNav(".nav-collapse");
				</script>
			</div>
		</div>
	</header>
	
	<div class="container header-inner">
		<h1 class="headline"><span>Venture Lehigh Valley</span> <br/> One Weekend <br/> Endless Possibilities</h1>
	</div>

	<a name="weekend"></a>
	<section >
		<div class="container-12">
			<div class="grid-8">
				<div class="text-block-left">
					<h2>Venture Weekend</h2>
					<p class="subheading">Oct 23-25 - Ben Franklin Tech Ventures</p>
					<p>
						Venture Weekend is open to individuals, teams, startup ideas, new products, pivots - in a word, Ventures.
					</p>
					<p>
						They become a reality over the weekend. Individuals collaborate on teams, teams challenge
						each other, friendships and connections form that outlast the weekend.
					</p>
					<p>
						Friday night teams form around ideas pitched in sixty seconds or less. Existing teams are
						welcome, even encouraged. All day Saturday teams work on their ventures. Perfecting a prototype,
						validating a market, determining a revenue model.
					</p>
					<p>
						Sunday afternoon each team presents to a panel of judges who evaluate their idea, everything
						accomplished that weekend, and pick the best Venture.
					</p>
					<a href="#tickets" class="button">Get Tickets</a>
				</div>
			</div>

			<div class="grid-4">
				<img class="responsive-img round-corners" src="images/thought.jpg" alt="" />
			</div>
			<div class="clear"></div>
		</div>
	</section>

	<a name="story"></a>
	<section class="alt-background skew-top-ccw skew-bottom-ccw">
		<div class="container-12">
			<div class="grid-4">
				<img class="responsive-img round-corners" src="images/table.jpg" alt="" />
			</div>
			<div class="grid-8">
				<div class="text-block-left">
					<h2>The Story</h2>
					<p class="subheading">A Fresh Take on the Weekend Startup Event</p>
					<p>
						<b>Venture Lehigh Valley</b> is built on the successful weekend events LVTech has organized for years now.
					</p>
					<p>
						You might say it's a <i>pivot</i>, but we'd call it the next <i>iteration</i>.
					</p>
					<p>
						Venture recognizes that creativity, innovation, and the entrepreneurial spirit are not confined to startups.<br/>
						That open collaboration is key, that technology isn't just online, and that a weekend is only part of the process.
					</p>
					<a href="#tickets" class="button">Get Tickets</a>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</section>
	
	<a name="runway"></a>
	<section>
		<div class="container-12">
			<div class="text-block-center">
				<h2>The Runway</h2>
				<p class="subheading">Because it's More Than a Weekend</p>
				<p>
					To make your weekend as successful as possible, we’re partnering with organizations and leaders
					across the Lehigh Valley to provide a runway of education and events leading up to <span class="nowrap">Venture Weekend</span>.
				</p>
				<p><a href="#tickets" class="button">Get Tickets</a></p>

			</div>
		</div>
		<div id="owl-team">
			<div class="team-member" data-wow-delay="500ms">
				<div class="team-photo" >
					<img src="images/anthony.jpg" alt="" />
				</div>
				<p class="job-role">Anthony Durante</p>
				<h4>So You Have An Idea</h4>
				<p class="job-role">October 5th</p>
				<p>
					An idea is just the start. You need to get critical about your thought process, learn how to structure a venture, and avoid common mistakes.
				</p>
				<p>
					Get a head start, and the tools you need to effectively plan and execute your venture.
				</p>

			</div>

			<div class="team-member" data-wow-delay="500ms">
				<div class="team-photo">
					<img src="images/jeff.jpg" alt="" />
				</div>
				<p class="job-role">Jeffrey Boerner</p>
				<h4>From Mind to Material</h4>
				<p class="job-role">October 12th</p>
				<p>
					Are you venturing into the real world? Get an overview of the tools available for modern prototyping.
				</p>
				<p>
					It’s not just 3D printers, wood and metal can be milled, laser cutters can be used for rapidly
					iteration, and cardboard and a knife can be a great starting place.
				</p>
			</div>

			<div class="team-member" data-wow-delay="500ms">
				<div class="team-photo">
					<img src="images/wayne.jpg" alt="" />
				</div>
				<p class="job-role">Wayne Barz</p>
				<h4>Success in 60 Seconds</h4>
				<p class="job-role">October 19th</p>
				<p>
					Communicating your idea is key to a successful team, a successful demo,	and success after the weekend.
				</p>
				<p>
					Learn how to pitch effectively, and get 60 seconds to win over someone who gets pitched every day.
					Then socialize your idea with the rest of the group over some great food.
				</p>
			</div>
		</div>
		<script>
			$(document).ready(function() {
			  $("#owl-team").owlCarousel({
			  	items: 3
			  	});
			});
		</script>
		<div class="clear"></div>
	</section>
	
	<a name="tickets"></a>
	<section class="alt-background skew-top-cw" style="padding-bottom: 80px;">
		<div class="container-12">
			<div class="text-block-center">
				<h2>Tickets</h2>
				<p class="subheading">For Venture Weekend and Runway Events</p>
				<p><strike>Super Early: $35</strike> <br/> Early (Sep): $40 &nbsp;|&nbsp;  Standard (Oct) $50  &nbsp;|&nbsp;  Late (Oct 19th) $75</p>
			</div>

			<form class="mailing-list" method="post" action="/">
				<div class="grid-3">
					&nbsp;
				</div>
				<div class="grid-6">
					<div class="round-corners" style="background-color: #59554A; padding: 15px; text-align: center;">
						<p class="subheading" style="margin: 0px; color: #fff;">Attendee Information</p>

						<p>
							Please add the contact information for the person attending.
							Billing contact information is gathered once you select a ticket.
						</p>

						<p><label>Name</label>  <input type="text" name="name"  id="name"  value=""></p>
						<p><label>Phone</label> <input type="text" name="phone" id="phone" value=""></p>
						<p><label>Email</label> <input type="text" name="email" id="email" value=""></p>


						<a href="#tickets" class="button payment" data-amount="4000">Buy Early Ticket ($40)</a>
						<a href="#tickets" class="button payment" data-amount="3500">Buy Student Ticket ($35)</a>
					</div>
				</div>
				<div class="grid-3">
					&nbsp;
				</div>
				<div class="clear"></div>
			</form>

		</div>
	</section>

	<footer class="footer">
		<div class="container-12">
			<ul class="social">
				<li><a href="https://twitter.com/lvtech" title="Twitter" class="fa fa-twitter"></a></li>
				<div class="clear"></div>
			</ul>
			<p>Copyright 2015 <em>LVTech</em>&trade; All Rights Reserved</p>
		</div>
	</footer>
		
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-29148247-3', 'auto');
		ga('send', 'pageview');

	</script>

</body>
</html>