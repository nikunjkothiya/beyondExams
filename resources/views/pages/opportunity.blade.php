@extends('layouts.master')

@section('style')
    <!--Custom Styles-->
    <link rel="stylesheet" href="{{asset('css/opportunity.css')}}">
    <style type="text/css">
    	#header{
			background: url('{{ $opportunity->image }}') center;
			-webkit-background-size: cover;
  			-moz-background-size: cover;
  			-o-background-size: cover;
  			background-size: cover;
		}

    </style>
@endsection

@section('content')
	<div class="opportunity">
		<div class="container">
			<div class="row">
				<div class="col">
					<h2>@lang('opportunity.title')</h2>
					<div class="card">
						<div class="row">
							<div class="col">
								<div id="header">
									<div class="overlay">
										<div class="text">{{ $opportunity->title }}</div>	
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<div class="content">
									<div class="row">
										<div class="col">
											<div class="deadline">
												@lang('opportunity.deadline') : {{ $opportunity->deadline }}
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col">
											<div class="description">
												{{ $opportunity->description }}
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col">
											<div class="apply">
												@auth
													
												@else

												@endauth
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('script')

@endsection