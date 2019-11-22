	<nav class="navbar navbar-expand-lg fixed-top navbar-light" style="background-color:#fff;">
		<div class="container">
		  	<a class="navbar-brand" href="{{ url('dashboard') }}">
		  		<img src="{{asset('images/logo.png')}}" width="30" height="30" class="d-inline-block align-top" alt=""> Precisely 
		  		@if($txnflag>0)
		  			<span class="badge badge-primary" style="background: #5b3495">@lang('nav.premium')</span>
		  		@endif
		  	</a>
		  	<button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    			<span class="navbar-toggler-icon"></span>
  			</button>
		  	<div class="collapse navbar-collapse" id="navbarNavDropdown">
			    <ul class="navbar-nav ml-auto">
			    	@auth
			      <li class="nav-item dropdown">

			        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			          <img src="{{ \Auth::user()->avatar }}" width="30" height="30" style="border-radius: 50px;margin-top: -5px;"/>&nbsp;&nbsp;{{ \Auth::user()->name }}
			        </a>
			        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
			          <a class="dropdown-item" href="{{ url('dashboard/profile') }}">@lang('nav.profile')</a>
			          <a class="dropdown-item" href="{{ url('logout') }}">@lang('nav.logout')</a>
			        </div>
			      </li>
			      @else
			      <li class="nav-item">
			        <a class="nav-link" href="{{ url('login') }}">Login</a>
			      </li>
			      @endauth
			      <li class="nav-item dropdown">
			      	<a class="nav-link dropdown-toggle" href="#" id="lang" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					    <i class="fa fa-font"></i>&nbsp;&nbsp;@lang('nav.lang')
					  </a>
					  <div class="dropdown-menu" aria-labelledby="lang" style="overflow-y:scroll;max-height:500px;">
					    @foreach($languages as $language)
					    	@if( \Config::get('app.locale') == $language->code)
					    		<a class="dropdown-item" href="{{url('lang/'.$language->code)}}">{{strtoupper($language->code)}} - {{$language->language}}&nbsp;<i class="fa fa-check-circle" style="color:#5b3495"></i></a>
					    	@else
			  					<a class="dropdown-item" href="{{url('lang/'.$language->code)}}">{{strtoupper($language->code)}} - {{$language->language}}</a>
					    	@endif
			  			@endforeach
					  </div>
			      </li>
			    </ul>
			  </div>
		</div>
	</nav>