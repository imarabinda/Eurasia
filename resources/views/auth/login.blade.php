@extends('auth.layout.index')
@section('content')

            <div class="card">
                <div class="card-body">

                    <h3 class="text-center mt-0">
                        <a href="{{route('login')}}" class="logo logo-admin" style="color:black">Eurasia</a>
                    </h3>

                    <h6 class="text-center">Log In</h6>

                    <div class="p-3">
                        
                        <form class="form-horizontal" method="POST" action="{{route('login')}}">
                          @csrf
                            <div class="form-group row justify-content-center">
                                <div class="col-12">
                                    <input class="form-control" type="text" name="name" value="{{ old('name') }}" required placeholder="Username">
                                    
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                </div>
                            </div>
                            

                            <div class="form-group row justify-content-center">
                                <div class="col-12">
                                    <input class="form-control" type="password" name="password" autocomplete="current-password" required placeholder="Password">
                                
                                    <span class="invalid-feedback" role="alert" style="display:block;">
                                        <strong>{{ $errors->first() }}</strong>
                                    </span>
                                
                                </div>
                            </div>


                            <div class="form-group row justify-content-center text-center">
                            <div class="col-md-6 ">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>


                            <div class="form-group text-center row m-t-20">
                                <div class="col-12">
                                    <button class="btn btn-info waves-effect waves-light" type="submit">Log In</button>
                                </div>
                            </div>

                            <div class="form-group m-t-10 mb-0 row text-center">
                                <div class="col-sm-12 m-t-20">
                                    <a href="{{route('password.request')}}" class="text-muted"><i class="mdi mdi-lock"></i> Forgot your password ?</a>
                                </div>
                            </div>


                            
                        </form>


                    </div>
                </div>
            </div>

            @endsection
            