<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $role = auth()->user()->role?->name;

        return match ($role) {
            'reception' => view('dashboard.recepcion'),
            'doctor' => view('dashboard.doctor'),
            'administrator' => view('dashboard.administradora'),
            default => abort(403, 'El usuario no tiene un rol asignado.'),
        };
    }
}