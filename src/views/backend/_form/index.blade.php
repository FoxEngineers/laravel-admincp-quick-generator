@extends('backend.layouts.app')

@section('title', app_name() . ' | '. $title )

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="card-title mb-0">
                    {{ $title }}
                </h4>
            </div><!--col-->
        </div><!--row-->

        <div class="row pt-3">
            <div class="col-sm-8">
                @include('laravel-admincp::backend._form.search', compact('searchFields'))
            </div><!--col-->

            <div class="col-sm-4 pull-right">
                @if ($canExport)
                    <div class="btn-toolbar float-right" role="toolbar" aria-label="@lang('labels.general.toolbar_btn_groups')">
                        <a href="{{ route($route.'.export.get') }}" class="btn btn-success ml-1" data-toggle="tooltip" title="@lang('labels.general.export')">
                            <i class="fas fa-file-export"></i>
                        </a>
                    </div>
                @endif
                @if ($canImport)
                    <div class="btn-toolbar float-right" role="toolbar" aria-label="@lang('labels.general.toolbar_btn_groups')">
                        <a href="{{ route($route.'.import.get') }}" class="btn btn-success ml-1" data-toggle="tooltip" title="@lang('labels.general.import')">
                            <i class="fas fa-file-import"></i>
                        </a>
                    </div>
                @endif
                @if($canCreate)
                    <div class="btn-toolbar float-right" role="toolbar" aria-label="@lang('labels.general.toolbar_btn_groups')">
                        <a href="{{ route($route.'.create') }}" class="btn btn-success ml-1" data-toggle="tooltip" title="@lang('labels.general.create_new')"><i class="fas fa-plus-circle"></i></a>
                    </div>
                @endif
            </div><!--col-->
        </div><!--row-->

        <div class="row mt-4">
            <div class="col">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            @if($columns)
                                @foreach($columns as $column => $params)
                                    <th>{{ $params['label'] }}</th>
                                @endforeach
                            @endif
                            <th>@lang('text.categories.column.created_at')</th>
                            <th>@lang('text.categories.column.updated_at')</th>
                            <th>@lang('labels.general.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $v)
                            <tr>
                                @if($columns)
                                    @foreach($columns as $column => $params)
                                        <td> {!! generate_form_field($v->$column, $params, $v) !!}</td>
                                    @endforeach
                                @endif
                                <td>{{ $v->created_at }}</td>
                                <td>{{ $v->updated_at }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Actions">
                                        @if(!empty($additionalActions))
                                            @foreach ($additionalActions as $action)
                                                <a href="{{ sprintf($action['url'], $v->id)  }}" data-toggle="tooltip"
                                                   data-placement="top"
                                                   title="{{ $action['title'] }}"
                                                   class="btn {{ $action['type'] }}"
                                                   target="_blank"
                                                >
                                                    <i class="fas {{ $action['icon'] }}"></i>
                                                </a>
                                            @endforeach
                                        @endif
                                        @if($canView)
                                            <a href="{{ route($route.'.show',$v->id) }}" data-toggle="tooltip" data-placement="top" title="View" class="btn btn-success"><i class="fas fa-eye"></i></a>
                                        @endif
                                        @if($canUpdate)
                                        <a href="{{ route($route.'.edit',$v->id) }}" data-toggle="tooltip" data-placement="top" title="Edit" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                        @endif
                                        @if($canDelete)
                                        <a href="{{ route($route .'.destroy',$v->id) }}"
                                           data-method="delete"
                                           data-trans-button-cancel="Cancel"
                                           data-trans-button-confirm="Delete"
                                           data-trans-title="Are you sure you want to do this?"
                                           class="btn btn-danger"><i class="fas fa-trash"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div><!--col-->
        </div><!--row-->
        <div class="row">
            <div class="col-7">
                <div class="float-left">
                    Total: {!! $data->total() !!}
                </div>
            </div><!--col-->

            <div class="col-5">
                <div class="float-right">
                    {!! $data->render() !!}
                </div>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-body-->
</div><!--card-->
@endsection
