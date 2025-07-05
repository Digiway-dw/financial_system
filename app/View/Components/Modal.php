<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * The modal ID.
     *
     * @var string
     */
    public $id;

    /**
     * The modal max width.
     *
     * @var string
     */
    public $maxWidth;

    /**
     * Create a new component instance.
     *
     * @param  string|null  $id
     * @param  string|null  $maxWidth
     * @return void
     */
    public function __construct($id = null, $maxWidth = null)
    {
        $this->id = $id;
        $this->maxWidth = $maxWidth;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.modal');
    }
}
