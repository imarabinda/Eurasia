<?php

namespace App\Exceptions;

use Exception;

class IsActiveException extends Exception
{
       public function __construct($message = 'Account is not in active state.')
    {
        parent::__construct($message);
    }
    
    public function render($request){
      return $request->expectsJson()
                    ? response()->json(['message' => $this->message], 423)
                    : response()->view('errors.423-is-active',['message'=>$this->message],423);
    }

    public function report()
    {
      return false;
    }

}
