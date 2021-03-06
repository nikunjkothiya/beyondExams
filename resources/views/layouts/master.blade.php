<!DOCTYPE html>
<html class="no-js" lang="{{ app()->getLocale() }}">
	<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{asset('images/fav.png')}}">
	@include('partials.head')
	@yield('style')
	@include('partials.analytics')
	</head>
	<body>
		@include('partials.nav')
		@yield('content')
		@include('partials.footer')	
		@yield('script')
	</body>
</html>