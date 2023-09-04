<?php

use Illuminate\Support\Str;

if (!function_exists('html_image')) {

    function html_image(string $name, $value = null, string $text = '',
                        bool $isRequired = true, array $options = [], bool $isMultiple = false): string
    {
        if (empty($options)) {
            $options = ['readonly' => true];
        }

        $string = '<div class="form-group row">';

        if ($isRequired) {
            $label = html()->label($text)->class('col-md-2 form-control-label')->for($name)->addChild('<span class="text-danger"> * </span>');
        } else {
            $label = html()->label($text)->class('col-md-2 form-control-label')->for($name);
        }
        $string .= $label;
        $string .= '<div class="col-md-10">';

        $classTrigger = $isMultiple ? 'upload-multiple-images' : 'upload-image';

        $input = '<div class="form-group hidden" id="' . $name . '"><div class="input-group"><div class="input-group-prepend"><a data-input="' . $name . '_input" data-preview="' . $name . '_preview" class="btn btn-success text-white ' . $classTrigger . '"><i class="fa fa-upload"></i> Choose</a></div>';

        $inputValue = is_array($value) ? implode(',', $value) : $value;
        $input .= html()->text($name)->class('form-control')->attributes($options)->id($name . '_input')->value($inputValue);
        $input .= '</div></div>';

        $input .= '<div>';

        $input .= '<div style="display:inline-flex;" class="' . $name . '_preview preview_image" id="' . $name . '_preview">';

        $images = '';
        if ($value) {
            if (is_array($value) && count($value) > 0) {
                foreach ($value as $i) {
                    $images .= '<img src="' . real_path($i) . '" style="height: 5rem;">';
                }
            } else if (is_string($value)) {
                $images .= '<img src="' . real_path($value) . '" style="height: 5rem;">';
            }
        } else $images = '<img src="' . real_path('/img/default.png') . '" style="height: 5rem;">';
        $input .= $images;
        $input .= '</div>';
        $input .= '</div>';


        $string .= $input;
        $string .= '</div></div>';

        return $string;
    }
}

if (!function_exists('generate_form')) {
    function generate_form(array $formData): string
    {
        $content = '';
        $content .= html()->form($formData['method'], $formData['route'])->class('form-horizontal')->open()->toHtml();
        $content .= '<div class="card">';
        $content .= '<div class="card-body">';
        $content .= '<div class="row">';
        $content .= '<div class="col-sm-5">';
        $content .= '<h4 class="card-title mb-0">';
        $content .= $formData['title'];
        $content .= ' ';
        $content .= '<small class="text-muted">';
        $content .= $formData['subTitle'];
        $content .= '</small>';
        $content .= '</h4>';
        $content .= '</div><!--col-->';
        $content .= '</div><!--row-->';
        $content .= '<hr>';
        $content .= '<div class="row mt-4">';
        $content .= '<div class="col">';
        $inputs = $formData['inputs'];
        foreach ($inputs as $key => $input) {
            $options = [];
            if (isset($input['options'])) {
                $options = $input['options'];
            }
            $inputData = [
                'type' => $input['type'] ?? 'text',
                'name' => $input['name'] ?? $key,
                'id' => $input['id'] ?? $key,
                'value' => $input['value'] ?? null,
                'label' => $input['label'] ?? ucfirst($key),
                'placeholder' => $input['placeholder'] ?? ucfirst($key),
                'class' => $input['class'] ?? '',
            ];
            if ($inputData['type'] == 'select') {
                $inputData['selectOptions'] = $input['selectOptions'] ?? [];
                $inputData['multiSelect'] = $input['multiSelect'] ?? false;
            }
            $isRequired = $input['isRequired'] ?? false;
            $content .= input_field($inputData, $isRequired, $options);
        }
        $content .= '</div><!--row-->';
        $content .= '</div><!--card-body-->';
        $content .= '<div class="card-footer">';
        $content .= '<div class="row">';
        $content .= '<div class="col">';
        $content .= form_cancel($formData['cancel'], __('buttons.general.cancel'));
        $content .= '</div><!--col-->';
        $content .= '<div class="col text-right">';
        $content .= form_submit($formData['submit']);
        $content .= '</div><!--col--></div><!--row--></div><!--card-footer--></div><!--card-->';
        $content .= html()->form()->close()->toHtml();
        return $content;
    }
}

if (!function_exists('generate_form_field')) {
    function generate_form_field($value, $params, $data)
    {
        if (!isset($params['type'])) {
            $params['type'] = 'text';
        }
        switch ($params['type']) {
            case 'image':
                $value = '<img src="' . $value . '" class="table-image" />';
                break;
            case 'datetime':
                $value = ($value) ? $value->format('Y-m-d') : '';
                break;
            case 'relation':
                $relation = $params['relation'];
                $relationModel = $relation['model'];
                $relationLabel = $relation['label'];
                $relationValue = '';
                $relationType = 'text';
                if (isset($relation['type'])) {
                    $relationType = $relation['type'];
                }
                if ($data->$relationModel) {
                    $hasRoute = isset($relation['route']) && !is_null($relation['route']);

                    if ($hasRoute) {
                        $relationValue .= '<a href="' . route($relation['route'], $value) . '" target="_blank">';
                    }

                    if (is_array($relationLabel)) {
                        $relationLabelString = [];
                        foreach ($relationLabel as $string) {
                            if (isset($data->$relationModel->$string)) {
                                $relationLabelString[] = $data->$relationModel->$string;
                            }
                        }
                        $relationValue .= collect($relationLabelString)->join(' ');
                    } else $relationValue .= $data->$relationModel->$relationLabel;

                    switch ($relationType) {
                        case 'path':
                            $relationValue = url($relationValue);
                            $hasRoute = false;
                            $link = '<a href="' .$relationValue . '" target="_blank">';
                            $link .= basename($relationValue);
                            $link .= '</a>';
                            // Assign back.
                            $relationValue = $link;
                            break;
                        case 'text':
                        default:
                            // Do nothing.
                    }

                    if ($hasRoute) {
                        $relationValue .= '</a>';
                    }
                }
                $value = $relationValue;
                break;
            default:
                if (isset($params['limit']) && is_numeric($params['limit'])) {
                    $value = Str::limit($value, $params['limit']);
                }
        }
        return $value;
    }
}

if (!function_exists('input_field')) {

    function input_field($data, bool $isRequired = false, array $options = ['maxlength' => 255]): string
    {
        if (is_array($data)) {
            $data = (object)$data;
        }

        $type = $data->type ?? 'text';
        $class = $data->class ?? '';

        if ($type == 'image') {
            return html_image($data->name, $data->value, $data->label, $isRequired, $options);
        }

        if ($type == 'images') {
            return html_image($data->name, $data->value, $data->label, $isRequired, $options, true);
        }

        if ($type == 'file') {
            return html_file($data->name, $data->value, $data->label, $isRequired, $options);
        }

        if ($type == 'files') {
            return html_file($data->name, $data->value, $data->label, $isRequired, $options, true);
        }


        $string = '<div class="form-group row">';

        $label_col = 'col-md-2';
        $input_col = 'col-md-10';

        if ($isRequired) {
            $string .= html()->label($data->label)->class($label_col . ' form-control-label')->for($data->name)->addChild('<span class="text-danger"> * </span>');
        } else {
            $string .= html()->label($data->label)->class($label_col . ' form-control-label')->for($data->name);
        }

        $string .= '<div class="' . $input_col . '">';

        switch ($type) {
            case 'select':
                $multiSelect = $data->multiSelect;
                $selectName = $multiSelect ? $data->name . '[]' : $data->name;
                $selected = $data->value;
                if ($multiSelect) {
                    if (is_object($selected)) {
                        $selected = $selected->pluck('id');
                    }
                    $input = html()->multiselect($selectName, $data->selectOptions, $selected)
                        ->class('form-control ' . $class)
                        ->attributes($options)
                        ->required();
                } else $input = html()->$type($selectName)->id($selectName)
                    ->class('form-control ' . $class)
                    ->required($isRequired)
                    ->placeholder($data->placeholder)
                    ->value($selected)
                    ->options($data->selectOptions)
                    ->attributes($options);
                break;
            case 'datetime':
                $dateTime = ($data->value) ? $data->value->format('Y-m-d') : now()->format('Y-m-d');
                $input = html()->text($data->name)
                    ->placeholder($data->placeholder)
                    ->class('form-control datepicker')
                    ->autofocus()
                    ->value($dateTime);
                break;
            default:
                if ($type == 'textarea' && !$isRequired) {
                    $input = html()->$type($data->name)->class('form-control ' . $class)
                        ->attributes($options)
                        ->placeholder($data->placeholder)
                        ->value($data->value);
                } else {
                    $input = html()->$type($data->name)->class('form-control ' . $class)
                        ->attributes($options)
                        ->placeholder($data->placeholder)
                        ->value($data->value)
                        ->required($isRequired);
                }
        }

        $string .= $input;
        $string .= '</div></div>';

        return $string;
    }
}

if (!function_exists('html_file')) {

    function html_file(string $name, $value = null, string $text = '',
                       bool $isRequired = true, array $options = ['readonly' => true],
                       bool $isMultiple = false): string
    {
        $string = '<div class="form-group row">';

        if ($isRequired) {
            $label = html()->label($text)->class('col-md-2 form-control-label')->for($name)->addChild('<span class="text-danger"> * </span>');
        } else {
            $label = html()->label($text)->class('col-md-2 form-control-label')->for($name);
        }
        $string .= $label;
        $string .= '<div class="col-md-10">';

        $classTrigger = $isMultiple ? 'upload-multiple-files' : 'upload-file';

        $input = '<div class="form-group hidden" id="' . $name . '">';
        $input .= '<div class="input-group">';
        $input .= '<div class="input-group-prepend">
                    <a data-input="' . $name . '_input"
                       data-preview="' . $name . '_preview"
                       class="btn btn-success text-white ' . $classTrigger . '">
                        <i class="fa fa-upload"></i> Choose</a>
                </div>';

        $inputValue = is_array($value) ? implode(',', $value) : $value;

        $input .= html()->text($name)
            ->class('form-control')
            ->attributes($options)
            ->id($name . '_input')
            ->value($inputValue);

        $input .= '</div>';
        $input .= '</div>';
        $input .= '<div>';

        $input .= '<div class="' . $name . '_preview preview_file" id="' . $name . '_preview">';

        $content = '';

        if ($value) {
            if (is_array($value) && count($value) > 0) {
                foreach ($value as $i) {
                    $content .= '<p>' . real_path($i) . '</p>';
                }
            }
            if (is_string($value)) {
                $content .= '<p>' . real_path($value) . '</p>';
            }
        }

        $input .= $content;
        $input .= '</div>';
        $input .= '</div>';

        $string .= $input;
        $string .= '</div></div>';

        return $string;
    }
}
