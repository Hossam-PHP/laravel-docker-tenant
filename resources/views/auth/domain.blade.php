<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Sign in to your account') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group">
                            <div class="row">
                                <label for="account" class="col-md-8 offset-md-2">{{ __('Enter Your') }} <strong>{{ __('Account URL') }}</strong></label>
                            </div>
                            <div class="row">
                                <div class="col-md-6 offset-md-2 text-md-right">
                                    <input id="account" type="text" class="form-control text-right inline @error('account') is-invalid @enderror" name="account" value="{{ old('account') }}" placeholder="your-url" required autofocus>
                                </div>
                                <div class="col-md-4 text-lg-left">
                                    <span class="sign-in-tld">.{{ config('app.url_base') }}</span>
                                </div>
                            </div>
                            <div class="row">
                                @error('account')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-2 text-md-center">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Continue') }} →
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>