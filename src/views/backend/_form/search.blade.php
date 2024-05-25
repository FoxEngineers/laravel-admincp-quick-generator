<?php
$sortVal = ['desc', 'asc'];

?>
<form method="get">
<div class="input-group">
    <div class="input-group-prepend">
        <select name="k" class="form-control">
            @foreach($searchFields as $v)
                <option value="{{ $v }}" {{ request()->get('k') == $v ? 'selected' : '' }}>
                    @lang($searchLang . '.' . $v)
                </option>
            @endforeach
        </select>
        <select name="sort" class="form-control">
            @foreach($sortVal as $v)
                <option value="{{ $v }}" {{ request()->get('sort') == $v ? 'selected' : '' }}>{{ ucfirst($v) }}</option>
            @endforeach
        </select>
    </div>
    <input class="form-control" id="s" type="text" name="s" placeholder="Search" value="{{ request()->get('s') }}">
    <div class="input-group-append">
        <button class="btn btn-primary" type="submit"><i class="icon-magnifier"></i></button>
    </div>
</div>

</form>