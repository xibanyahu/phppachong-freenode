@extends('errors::minimal')

@section('title', __('请求过于频繁，休息一会。'))
@section('code', '429')
@section('message', __('请求过于频繁，休息一会。'))
