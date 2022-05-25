<?php

namespace App\Http\Middleware\Supervisor;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RejectSuspendedSupervisorAccountRequests
{
    /**
     * check if the request is issued by a supervisor 
     * 
     * then if the supervisor account status is not 'ACTIVE'
     * reject the request and return in_in_active_account error
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::guard('supervisors')->check()){
            $supervisor = auth('supervisors')->user();
            if(!$supervisor->isActive()){
                $envelope = [
                    'hasError' => true,
                    'messages' => [__('supervisors.in_active_account')]
                ];
        
                return response($envelope, Response::HTTP_LOCKED);

                throw new AuthenticationException();
            }
        }
        return $next($request);
    }
}
