@extends('layouts.app')
@section('title', 'Главная')
@section('css')
    <link rel="stylesheet" href="@vasset('css/home.css')">
@endsection
@section('content')
    <x-search.form/>
    <x-destinations/>
    <section class="container mt-5 text-white fw-bold">
        <div class="row">
            <div class="bg-primary col-lg-10 offset-lg-1 rounded-3 p-4 text-center">
                <h3 class="">Летайте чаще - получайте больше</h3>
                <p class="mt-5">копите мили за каждый перелет и обменивайте на бесплатные билеты</p>
                <button type="submit" class='btn btn-outline-light col-lg-3 mt-3 fw-bold fs-5'>стать участником </button>
            </div>
        </div>
    </section>
    <x-faq/>
@endsection