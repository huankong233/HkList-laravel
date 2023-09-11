<!DOCTYPE html>
<html lang="{{ str_replace('_','-',strtolower(app()->getLocale())) }}">
@include('layouts.head')
<body>
<div id="app">
    @yield('template')
</div>
@yield('scripts')
</body>
