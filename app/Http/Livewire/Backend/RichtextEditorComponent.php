<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

class RichtextEditorComponent extends Component
{
  public $name;
  public $value;
  public $style;
  public $uploadUrl;
  public $contentImagesInput;

  public function mount($name, $value = '', $style = '', $uploadUrl = null, $contentImagesInput = null)
  {
    $this->name = $name;
    $this->value = $value;
    $this->style = $style;
    $this->uploadUrl = $uploadUrl;
    $this->contentImagesInput = $contentImagesInput;
  }

  public function render()
  {
    return view('livewire.backend.richtext-editor-component');
  }
}
