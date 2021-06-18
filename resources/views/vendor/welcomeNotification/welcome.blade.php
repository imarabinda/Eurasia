
@extends('auth.layout.index')

@section('content')
<div class="container">
    <div class="row justify-content-center text-center">
            <div class="card">
                <div class="card-header">{{ __('Set Password') }}</div>

                <div class="card-body">                    

<form method="POST">
    @csrf

    <input type="hidden" name="email" value="{{ $user->email }}"/>

   
    
    <div class="form-group row">
        <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>
        
        <div class="col-md-6">
             <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
             
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


     <div class="form-group row">
         <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>
         
         <div class="col-md-6">
             <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>


    <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-info">
                                    {{ __('Save password and login') }}
                                </button>
                            </div>
                        </div>

</form>



                </div>
            </div>
        </div>
    </div>
@endsection