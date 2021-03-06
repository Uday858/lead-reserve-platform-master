@extends('layouts.app')
@section('title','Create New Advertiser')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <form method="POST" action="{{route("advertisers.store")}}">
                    <div class="panel panel-default">

                        <div class="panel-heading">
                            Create an Advertiser
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h5>
                                        <strong>
                                            Advertiser Name/Email
                                        </strong>
                                    </h5>
                                    <p>
                                        This is quite self-explanatory.
                                    </p>
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Company Name"
                                               name="name" required/>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control" placeholder="Contact Email"
                                               name="email" required/>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <h5>
                                        <strong>
                                            Main Contact
                                        </strong>
                                    </h5>
                                    <p>
                                        This is the main advertiser contact that we're interacting with. Be as
                                        descriptive
                                        as possible when filling out these fields.
                                    </p>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="attributes[poc_main_name]"
                                               placeholder="Full Name">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="attributes[poc_main_email]"
                                               placeholder="Email Address">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="attributes[poc_main_phone]"
                                               placeholder="Phone Number">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h5>Finance Contact</h5>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="attributes[poc_finance_name]"
                                               placeholder="Full Name">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="attributes[poc_finance_email]"
                                               placeholder="Email Address">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="attributes[poc_finance_phone]"
                                               placeholder="Phone Number">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h5>Technical Contact</h5>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="attributes[poc_tech_name]"
                                               placeholder="Full Name">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="attributes[poc_tech_email]"
                                               placeholder="Email Address">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="attributes[poc_tech_phone]"
                                               placeholder="Phone Number">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h5>Advertiser Notes</h5>
                                    <div class="form-group">
                                    <textarea class="form-control" placeholder="Notes..."
                                              name="attributes[advertiser_notes]"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <p>
                                        By clicking create advertiser, the system will redirect you to a certain page.
                                    </p>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">Create Advertiser</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection