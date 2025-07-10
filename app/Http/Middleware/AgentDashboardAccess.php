<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Constants\Roles;
use Illuminate\Support\Facades\Gate;

class AgentDashboardAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // If the user is an agent, they can only access the agent dashboard
        if ($user && Gate::forUser($user)->allows('view-agent-dashboard')) {
            // If trying to access a restricted dashboard, redirect to agent dashboard
            if ($request->is('*/dashboard/*') && !$request->is('*/dashboard/agent*')) {
                return redirect()->route('dashboard.agent');
            }

            // Allow access to their own transactions but not others
            if ($request->is('*/transactions*') && $request->route('transactionId')) {
                $transactionId = $request->route('transactionId');
                $transaction = \App\Models\Domain\Entities\Transaction::find($transactionId);

                if ($transaction && $transaction->agent_id !== $user->id) {
                    return redirect()->route('transactions.index')
                        ->with('error', 'You can only view your own transactions.');
                }
            }

            // Prevent access to company financial reports
            if ($request->is('*/reports/financial*') || $request->is('*/reports/profit*')) {
                return redirect()->route('dashboard.agent')
                    ->with('error', 'You do not have permission to access financial reports.');
            }

            // Prevent access to safe or line balance modification
            if ($request->is('*/safes/*/edit*') || $request->is('*/lines/*/balance*')) {
                return redirect()->route('dashboard.agent')
                    ->with('error', 'You do not have permission to modify balances.');
            }

            // Prevent access to user management
            if ($request->is('*/users*') || $request->is('*/user*') || $request->is('*/staff*')) {
                // Allow access to their own profile
                $userId = $request->route('userId');
                if ($userId && (int)$userId === $user->id && $request->is('*/users/*/view')) {
                    // Let them access only their own profile view
                    return $next($request);
                }

                // Redirect with error message
                return redirect()->route('dashboard.agent')
                    ->with('error', 'You do not have permission to access user management.');
            }
        }

        return $next($request);
    }
}
