<!-- Modal -->
<div class="modal fade" id="checkout" tabindex="-1" role="dialog" aria-labelledby="checkoutLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="checkoutLabel">@lang('subscription.detailstitle')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col">
            <div class="form-group">
              <label for="firstname">@lang('profile.firstname')</label>
                <input type="text" class="form-control @error('firstname') is-invalid @enderror" id="firstname" aria-describedby="firstnameHelp" placeholder="Enter First Name" name="firstname" value="{{ old('firstname',$firstname) }}" required autocomplete="firstname" autofocus form="proceed">
                @error('firstname')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror 
              </div>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <div class="form-group">
              <label for="email">@lang('profile.email')</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" aria-describedby="emailHelp" placeholder="Enter Your Primary Email" name="email" value="{{ old('email',$email) }}" required autocomplete="email" autofocus form="proceed">
              @error('email')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <div class="form-group">
              <label for="phone">@lang('profile.phone')</label>
              <input type="phone" class="form-control @error('phone') is-invalid @enderror" id="phone" aria-describedby="phoneHelp" placeholder="Enter Your Phone Number" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus form="proceed">
              @error('phone')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('subscription.close')</button>
        <button type="submit" form="proceed" class="btn proceed" style="background: #5b3495;color: white;">
          @lang('subscription.proceed')
        </button>
      </div>
    </div>
  </div>
</div>