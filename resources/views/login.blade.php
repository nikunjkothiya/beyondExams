<!DOCTYPE html>
<html class="no-js" lang="{{ app()->getLocale() }}">
	<head>
		<title>Precisely | Discover latest & personalized internships, scholarships and more opportunities! </title>
		<meta charset="UTF-8">
		<meta name="author" content="Precisely">
    	<meta name="description" content="Find the latest internships, scholarships, and many such opportunities on precisely. Personalised scholarships, internships, conferences are manually curated and handpicked only for you.">
    	<meta name="keywords" content="HTML,CSS,XML,JavaScript">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="shortcut icon" href="{{asset('images/fav.png')}}">
		<!--Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700,800">
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	    <!--CSS-->
	    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
	    <link rel="stylesheet" href="{{asset('css/login.css')}}">
	    <!--JS-->
	    <script src="{{asset('js/jquery.min.js')}}"></script>
	    <script src="{{asset('js/popper.min.js')}}"></script>
	    <script src="{{asset('js/bootstrap.min.js')}}"></script>
		<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
		<script>
		!function(){var analytics=window.analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on"];analytics.factory=function(t){return function(){var e=Array.prototype.slice.call(arguments);e.unshift(t);analytics.push(e);return analytics}};for(var t=0;t<analytics.methods.length;t++){var e=analytics.methods[t];analytics[e]=analytics.factory(e)}analytics.load=function(t,e){var n=document.createElement("script");n.type="text/javascript";n.async=!0;n.src="https://cdn.segment.com/analytics.js/v1/"+t+"/analytics.min.js";var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(n,a);analytics._loadOptions=e};analytics.SNIPPET_VERSION="4.1.0";
		  analytics.load("nrD7RqhTZT9qt6ee7JwSkxSCBtLenzqc");
		  analytics.page();
		}}();
		</script>
	</head>
<body>
	<nav class="navbar fixed-top navbar-light navbar-expand-lg" style="background-color:#fff;">
		<div class="container">
		  	<a class="navbar-brand"></a>
		  	<div class="dropdown">
			  <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="lang" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			    <i class="fa fa-font"></i>&nbsp;&nbsp;@lang('login.lang')
			  </button>
			  <div class="dropdown-menu" aria-labelledby="lang" style="overflow-y:scroll;max-height:500px;">
			  	@foreach($languages as $language)
			  		@if( \Config::get('app.locale') == $language->code)
					    <a class="dropdown-item" href="{{url('lang/'.$language->code)}}">{{strtoupper($language->code)}} - {{$language->language}}&nbsp;<i class="fa fa-check-circle" style="color:#5b3495"></i></a>
					@else
			  			<a class="dropdown-item" href="{{url('lang/'.$language->code)}}">{{strtoupper($language->code)}} - {{$language->language}}</a>
			    	@endif
			  	@endforeach
			    <!-- <a class="dropdown-item" href="#">EN - English</a>
			    <a class="dropdown-item" href="#">FR - French</a>
			    <a class="dropdown-item" href="#">DE - German</a> -->
			  </div>
			</div>
		</div>
	</nav>
	<div class="header">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<div class="title">
						<div class="row">
							<div class="col" >
								<div class="logo">
									<img src="{{asset('images/logo.png')}}"><h2>Precisely</h2><br>
									<div class="subtitle"><p>@lang('login.title')<p></div>
									<div class="d-none d-md-block subtext"><p class="text-secondary">@lang('login.subtitle')</p></div>
									<br>
									<div class="d-none d-md-block subtext"><p class="text-secondary">@lang('login.subtitle2')</p>
										<a style="margin:0px;padding: 0px;" href='https://play.google.com/store/apps/details?id=com.wayneventures.precisely&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1'><img alt='Get it on Google Play' width="200px" src='https://play.google.com/intl/en_us/badges/images/generic/en_badge_web_generic.png'/></a>
									</div>

								</div>	
							</div>
						</div>
					</div>
					
				</div>
				<div class="col-md-6">
					<div class="login-card">
						<div class="row">
							<div class="col">
								<div class="login-title">
									@lang('login.cardtitle')
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<hr>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<div class="login-btn" align="center">
									<a class="btn btn-lg btn-facebook" href="{{ url('auth/facebook') }}" role="button"><i class="fa fa-facebook"></i>&nbsp;&nbsp;@lang('login.facebook')</a>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<div class="login-btn" align="center">
									<a class="btn btn-lg btn-google" href="{{ url('auth/google') }}" role="button"><i class="fa fa-google"></i>&nbsp;&nbsp;@lang('login.google')</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>