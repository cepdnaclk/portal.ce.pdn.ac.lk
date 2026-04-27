<?php

namespace App\Support\Html;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

class FormBuilder
{
    protected $model;

    public function open(array $options = []): HtmlString
    {
        $method = strtoupper($options['method'] ?? 'POST');
        $action = $options['url'] ?? $options['route'] ?? '';
        $attributes = $this->attributes(array_merge(
            [
                'method' => $method === 'GET' ? 'GET' : 'POST',
                'action' => $action,
                'enctype' => Arr::get($options, 'enctype'),
                'class' => Arr::get($options, 'class'),
            ],
            Arr::get($options, 'files') ? ['enctype' => 'multipart/form-data'] : []
        ));

        $spoof = '';
        if (! in_array($method, ['GET', 'POST'], true)) {
            $spoof = $this->hidden('_method', $method);
        }

        $token = $method !== 'GET' ? csrf_field() : '';

        return new HtmlString("<form{$attributes}>{$token}{$spoof}");
    }

    public function model($model, array $options = []): HtmlString
    {
        $this->model = $model;

        return $this->open($options);
    }

    public function close(): HtmlString
    {
        $this->model = null;

        return new HtmlString('</form>');
    }

    public function label(string $name, string $value, array $attributes = []): HtmlString
    {
        $attributes = $this->attributes(array_merge(['for' => $name], $attributes));

        return new HtmlString("<label{$attributes}>{$value}</label>");
    }

    public function text(string $name, $value = null, array $attributes = []): HtmlString
    {
        return $this->input('text', $name, $value, $attributes);
    }

    public function email(string $name, $value = null, array $attributes = []): HtmlString
    {
        return $this->input('email', $name, $value, $attributes);
    }

    public function number(string $name, $value = null, array $attributes = []): HtmlString
    {
        return $this->input('number', $name, $value, $attributes);
    }

    public function date(string $name, $value = null, array $attributes = []): HtmlString
    {
        return $this->input('date', $name, $value, $attributes);
    }

    public function datetimeLocal(string $name, $value = null, array $attributes = []): HtmlString
    {
        return $this->input('datetime-local', $name, $value, $attributes);
    }

    public function file(string $name, array $attributes = []): HtmlString
    {
        return $this->input('file', $name, null, $attributes);
    }

    public function checkbox(string $name, $value = 1, $checked = null, array $attributes = []): HtmlString
    {
        $isChecked = $checked ?? old($name, false);
        if ($this->model !== null) {
            $isChecked = old($name, (bool) data_get($this->model, $name, $isChecked));
        }

        $attributes = $this->attributes(array_merge(
            ['type' => 'checkbox', 'name' => $name, 'value' => $value],
            $attributes,
            $isChecked ? ['checked' => 'checked'] : []
        ));

        return new HtmlString("<input{$attributes}>");
    }

    public function textarea(string $name, $value = null, array $attributes = []): HtmlString
    {
        $value = $this->fieldValue($name, $value);
        $attributes = $this->attributes(array_merge(['name' => $name], $attributes));

        return new HtmlString("<textarea{$attributes}>".e($value)."</textarea>");
    }

    public function select(string $name, $list = [], $selected = null, array $attributes = []): HtmlString
    {
        if ($list instanceof Arrayable) {
            $list = $list->toArray();
        }

        $selectedValue = $this->fieldValue($name, $selected);
        $attrArray = array_merge(['name' => $name], $attributes);

        $options = '';

        if (array_key_exists('placeholder', $attrArray)) {
            $options .= '<option value="">'.e($attrArray['placeholder']).'</option>';
            unset($attrArray['placeholder']);
        }

        foreach ($list as $key => $display) {
            $isSelected = (string) $selectedValue === (string) $key;
            $options .= '<option value="'.e($key).'"'.($isSelected ? ' selected' : '').'>'.e($display).'</option>';
        }

        return new HtmlString("<select".$this->attributes($attrArray).">{$options}</select>");
    }

    public function hidden(string $name, $value = null, array $attributes = []): HtmlString
    {
        return $this->input('hidden', $name, $value, $attributes);
    }

    public function submit(string $value = 'Submit', array $attributes = []): HtmlString
    {
        $attributes = $this->attributes(array_merge(['type' => 'submit', 'value' => $value], $attributes));

        return new HtmlString("<input{$attributes}>");
    }

    protected function input(string $type, string $name, $value = null, array $attributes = []): HtmlString
    {
        $value = $this->fieldValue($name, $value);
        $attributes = $this->attributes(array_merge(['type' => $type, 'name' => $name, 'value' => $value], $attributes));

        return new HtmlString("<input{$attributes}>");
    }

    protected function attributes(array $attributes): string
    {
        $clean = [];
        foreach ($attributes as $key => $value) {
            if ($value === null || $value === false) {
                continue;
            }

            if (is_bool($value) && $value === true) {
                $clean[] = $key;
                continue;
            }

            $clean[] = $key.'="'.e($value).'"';
        }

        return count($clean) ? ' '.implode(' ', $clean) : '';
    }

    protected function fieldValue(string $name, $value)
    {
        $value = old($name, $value);

        if ($this->model !== null) {
            $value = old($name, data_get($this->model, $name, $value));
        }

        if (is_array($value)) {
            $value = json_encode($value);
        }

        return $value;
    }
}
