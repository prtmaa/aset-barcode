@extends('layouts.master')

@section('tittle')
    Dashboard
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"> <a href="{{ url('/') }}">Dashboard</a></li>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">

            <section class="col-lg-12 connectedSortable">

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">

                            </div>

                            <div class="card-body table-responsive">

                            </div>

                        </div>

                    </div>

                </div>

            </section>
        </div>
    @endsection
