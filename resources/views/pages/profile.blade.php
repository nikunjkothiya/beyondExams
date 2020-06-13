@extends('layouts.master')

@section('style')
    <!--Custom Styles-->
    <link rel="stylesheet" href="{{asset('css/profile.css')}}">
@endsection

@section('content')
	<div class="profile">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					<h2>@lang('profile.title')</h2>
					<hr>
					<div class="list-group">
					  	<a href="{{ url('dashboard') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					    	@lang('dashboard.option1')
					  	</a>
					  	<a href="{{ url('dashboard/filter') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					  		@lang('dashboard.option2')
					  	</a>
					  	<a href="{{ url('dashboard/profile') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center active">
					  		@lang('dashboard.option3')
					  		@if(!$pcheck)
					  			<i class="fa fa-exclamation-circle"></i>
					  		@endif
					  	</a>
					  	<a href="{{ url('dashboard/saved-opp') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					  		@lang('dashboard.option4')
					  	</a>
					  	<a href="{{ url('dashboard/subscription') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					  		@lang('dashboard.option5')
					  	</a>
					  	<a href="{{ url('dashboard/message') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					  		@lang('dashboard.option6')
					  	</a>
					  	<a href="{{ url('logout') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					  		@lang('dashboard.option7')
					  	</a>
					</div>
				</div>
				<div class="col-md-9">
					@if(session('saved'))
						<div class="alert alert-success alert-dismissible fade show" role="alert">
						  <strong>Saved!</strong> Profile successfully updated.
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						    <span aria-hidden="true">&times;</span>
						  </button>
						</div>
					@endif
					<form method="POST" id="details" action="{{ url('dashboard/profile/save') }}">
							@csrf
							<div class="row">
								<div class="col">
									<div class="card-content">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
												    <label for="firstname">@lang('profile.firstname')</label>
												    <input type="text" class="form-control @error('firstname') is-invalid @enderror" id="firstname" aria-describedby="firstnameHelp" placeholder="Enter First Name" name="firstname" value="{{ old('firstname',$pcheck->firstname) }}" required autocomplete="firstname" autofocus>
												    @error('firstname')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror 
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
												    <label for="lastname">@lang('profile.lastname')</label>
												    <input type="text" class="form-control @error('lastname') is-invalid @enderror" id="lastname" aria-describedby="lastnameHelp" placeholder="Enter Last Name" name="lastname" value="{{ old('lastname',$pcheck->lastname) }}" required autocomplete="lastname" autofocus>
												    @error('lastname')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
												    <label for="email">@lang('profile.email')</label>
												    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" aria-describedby="emailHelp" placeholder="Enter Your Primary Email" name="email" value="{{ old('email',$pcheck->email) }}" required autocomplete="email" autofocus >
												    @error('email')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
												    <label for="college">@lang('profile.college')</label>
												    <input type="text" class="form-control @error('college') is-invalid @enderror" id="college" aria-describedby="collegeHelp" placeholder="Enter College or School Name" name="college" value="{{ old('college',$pcheck->college) }}" required autocomplete="college" autofocus>
												    @error('college')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
												    <label for="gpa">@lang('profile.gpa')</label>
												    <input type="text" class="form-control @error('gpa') is-invalid @enderror" id="gpa" aria-describedby="gpaHelp" placeholder="Enter GPA" name="gpa" value="{{ old('gpa',$pcheck->gpa) }}" required autocomplete="gpa" autofocus>
												    <small id="gpaHelp" class="form-text text-muted">out of 10</small>
												    @error('gpa')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="qualification">@lang('profile.qualification')</label>
						                            <select id="qualification" class="form-control @error('qualification') is-invalid @enderror" name="qualification" required autofocus>
						                            @error('qualification')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror
						                                <option></option>
						                                @foreach($qualifications as $qualification)
						                                    <option value="{{ $qualification->id }}" @if (old('qualification',$pcheck->qualification_id) == $qualification->id) {{ 'selected' }} @endif>{{$qualification->qualification}}</option>
						                                @endforeach
						                            </select>
						                        </div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="discipline">@lang('profile.discipline')</label>
						                            <select id="discipline" class="form-control @error('discipline') is-invalid @enderror" name="discipline" required autofocus>
						                            @error('discipline')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror
						                                <option></option>
						                                @foreach($disciplines as $discipline)
						                                    <option value="{{ $discipline->id }}" @if (old('discipline',$pcheck->discipline_id) == $discipline->id) {{ 'selected' }} @endif>{{$discipline->discipline}}</option>
						                                @endforeach
						                            </select>
						                        </div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
												    <label for="city">@lang('profile.city')</label>
												    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" aria-describedby="cityHelp" placeholder="Enter City" name="city" value="{{ old('city',$pcheck->city) }}" required autocomplete="city" autofocus>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="country">@lang('profile.country')</label>
						                            <select id="country" class="form-control @error('country') is-invalid @enderror" name="country" required autofocus>
						                            @error('country')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror
						                                <option></option>
						                                @foreach($countries as $country)
						                                    <option value="{{ $country->id }}" @if (old('country',$pcheck->country_id) == $country->id) {{ 'selected' }} @endif>{{$country->name}}</option>
						                                @endforeach
						                            </select>
						                        </div>
											</div>
										</div>
										<div class="row">
											
											<div class="col">
												<div class="card-btn">
													<div class="next-btn" align="center">
														<button type="submit" class="btn btn-lg btn-next" ></i>@lang('profile.save')</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('script')

@endsection