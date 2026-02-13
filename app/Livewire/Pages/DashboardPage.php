<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class DashboardPage extends Component
{
    #[Layout('components.layouts.main')]
    #[Title('Dashboard')]
    public function render()
    {
        return <<<'HTML'
        <div class="flex h-full min-h-screen flex-col">
        </div>
        HTML;
    }
}
