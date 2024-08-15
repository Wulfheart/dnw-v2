<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

// class DevLogin extends Component
// {
//     #[Validate('required|string')]
//     public string $userId = '';
//
//     /**
//      * @var array<string, string>
//      */
//     public array $users = [];
//
//     public function mount(): void
//     {
//         $this->users = User::all()->pluck('name', 'id')->toArray();
//     }
//
//     public function login(): void
//     {
//         Auth::loginUsingId($this->userId);
//
//         $this->redirect(route('game.create'), true);
//     }
//
//     public function render(): \Illuminate\Contracts\View\View|Application|Factory|View|\Illuminate\Contracts\Foundation\Application
//     {
//         return view('livewire.dev-login');
//     }
// }
