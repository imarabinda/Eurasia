<?php

namespace App\Exceptions;

use Exception;

class OneWayLoginException extends Exception
{
   
      public function __construct($message = 'Someone logged in this account.')
    {
        parent::__construct($message);
    }
    
    public function render($request){
      return $request->expectsJson()
                    ? response()->json(['message' => $this->message], 401)
                    : response()->view('errors.401-oneway-login',['message'=>$this->message],401);
    }

    public function report()
    {
      return false;
    }

}
