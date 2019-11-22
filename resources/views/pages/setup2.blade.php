@extends('layouts.master')

@section('style')
    <!--Custom Styles-->
    <link rel="stylesheet" href="{{asset('css/setup.css')}}">
@endsection

@section('content')
	<div class="details">
		<div class="container">
			<div class="row">
				<div class="col">
					<h2>@lang('setup.details')</h2>
					<div class="card">
						<div class="row">
							<div class="col">
								<div class="card-title">
									
								</div>	
							</div>
						</div>
						<form method="POST" id="details" action="{{ url('/setup/details') }}">
							@csrf
							<div class="row">
								<div class="col">
									<div class="card-content">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
												    <label for="firstname">@lang('setup.firstname')</label>
												    <input type="text" class="form-control @error('firstname') is-invalid @enderror" id="firstname" aria-describedby="firstnameHelp" placeholder="Enter First Name" name="firstname" value="{{ old('firstname') }}" required autocomplete="firstname" autofocus>
												    @error('firstname')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror 
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
												    <label for="lastname">@lang('setup.lastname')</label>
												    <input type="text" class="form-control @error('lastname') is-invalid @enderror" id="lastname" aria-describedby="lastnameHelp" placeholder="Enter Last Name" name="lastname" value="{{ old('lastname') }}" required autocomplete="lastname" autofocus>
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
												    <label for="email">@lang('setup.email')</label>
												    <input type="email" value="{{ \Auth::user()->email }}" class="form-control @error('email') is-invalid @enderror" id="email" aria-describedby="emailHelp" placeholder="Enter Your Primary Email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus >
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
												    <label for="college">@lang('setup.college')</label>
												    <input type="text" class="form-control @error('college') is-invalid @enderror" id="college" aria-describedby="collegeHelp" placeholder="Enter College or School Name" name="college" value="{{ old('college') }}" required autocomplete="college" autofocus>
												    @error('college')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
												    <label for="gpa">@lang('setup.gpa')</label>
												    <input type="text" class="form-control @error('gpa') is-invalid @enderror" id="gpa" aria-describedby="gpaHelp" placeholder="Enter GPA" name="gpa" value="{{ old('gpa') }}" required autocomplete="gpa" autofocus>
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
													<label for="qualification">@lang('setup.qualification')</label>
						                            <select id="qualification" class="form-control @error('qualification') is-invalid @enderror" name="qualification" required autofocus>
						                            @error('qualification')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror
						                                <option></option>
						                                @foreach($qualifications as $qualification)
						                                    <option value="{{ $qualification->id }}" @if (old('qualification') == $qualification->id) {{ 'selected' }} @endif>{{$qualification->qualification}}</option>
						                                @endforeach
						                            </select>
						                        </div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="discipline">@lang('setup.discipline')</label>
						                            <select id="discipline" class="form-control @error('discipline') is-invalid @enderror" name="discipline" required autofocus>
						                            @error('discipline')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror
						                                <option></option>
						                                @foreach($disciplines as $discipline)
						                                    <option value="{{ $discipline->id }}" @if (old('discipline') == $discipline->id) {{ 'selected' }} @endif>{{$discipline->discipline}}</option>
						                                @endforeach
						                            </select>
						                        </div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
												    <label for="city">@lang('setup.city')</label>
												    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" aria-describedby="cityHelp" placeholder="Enter City" name="city" value="{{ old('city') }}" required autocomplete="city" autofocus>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="country">@lang('setup.country')</label>
						                            <select id="country" class="form-control @error('country') is-invalid @enderror" name="country" required autofocus>
						                            @error('country')
						                                <span class="invalid-feedback" role="alert">
						                                    <strong>{{ $message }}</strong>
						                                </span>
						                            @enderror
						                                <option></option>
						                                @foreach($countries as $country)
						                                    <option value="{{ $country->id }}" @if (old('country') == $country->id) {{ 'selected' }} @endif>{{$country->name}}</option>
						                                @endforeach
						                            </select>
						                        </div>
											</div>
										</div>
										<div class="row">
											<div class="col">
												<div class="card-btn">
													<div class="skip-btn" align="center">
														<a class="btn btn-lg btn-outline-secondary btn-skip" href="{{ url('dashboard') }}" role="button"></i>@lang('setup.skip')</a>
													</div>
												</div>
											</div>
											<div class="col">
												<div class="card-btn">
													<div class="next-btn" align="center">
														<button type="submit" class="btn btn-lg btn-next" ></i>@lang('setup.save')</button>
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
	</div>
@endsection

@section('script')

@endsection