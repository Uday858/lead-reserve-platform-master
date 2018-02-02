@extends('layouts.app')
@section('title','Editing' . $publisher->name)
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <form method="POST" action="{{route("publishers.update",["id"=>$publisher->id])}}">
                    <input name="_method" type="hidden" value="PUT">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Edit Publisher
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h5>
                                        <strong>
                                            Publisher Name/Email
                                        </strong>
                                    </h5>
                                    <p>
                                        This is quite self-explanatory.
                                    </p>
                                    {{csrf_field()}}
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Company Name"
                                               name="name" value="{{$publisher->name}}" required/>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control" placeholder="Contact Email"
                                               name="email" value="{{$publisher->email}}" required/>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <h5>
                                        <strong>
                                            Main Contact
                                        </strong>
                                    </h5>
                                    <p>
                                        This is the main publisher contact that we're interacting with. Be as
                                        descriptive
                                        as possible when filling out these fields.
                                    </p>
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               name="attributes[publisher_poc_main_name]"
                                               value="{{$publisher->hasAttributeOrEmpty("publisher_poc_main_name")}}"
                                               placeholder="Full Name">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               name="attributes[publisher_poc_main_email]"
                                               value="{{$publisher->hasAttributeOrEmpty("publisher_poc_main_email")}}"
                                               placeholder="Email Address">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               name="attributes[publisher_poc_main_phone]"
                                               value="{{$publisher->hasAttributeOrEmpty("publisher_poc_main_phone")}}"
                                               placeholder="Phone Number">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h5>Finance Contact</h5>
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               name="attributes[publisher_poc_finance_name]"
                                               value="{{$publisher->hasAttributeOrEmpty("publisher_poc_finance_name")}}"
                                               placeholder="Full Name">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               name="attributes[publisher_poc_finance_email]"
                                               value="{{$publisher->hasAttributeOrEmpty("publisher_poc_finance_email")}}"
                                               placeholder="Email Address">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               name="attributes[publisher_poc_finance_phone]"
                                               value="{{$publisher->hasAttributeOrEmpty("publisher_poc_finance_phone")}}"
                                               placeholder="Phone Number">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h5>Technical Contact</h5>
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               name="attributes[publisher_poc_tech_name]"
                                               value="{{$publisher->hasAttributeOrEmpty("publisher_poc_tech_name")}}"
                                               placeholder="Full Name">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               name="attributes[publisher_poc_tech_email]"
                                               value="{{$publisher->hasAttributeOrEmpty("publisher_poc_tech_email")}}"
                                               placeholder="Email Address">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               name="attributes[publisher_poc_tech_phone]"
                                               value="{{$publisher->hasAttributeOrEmpty("publisher_poc_tech_phone")}}"
                                               placeholder="Phone Number">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h5>Publisher Notes</h5>
                                    <div class="form-group">
                                    <textarea class="form-control" placeholder="Notes..."
                                              name="attributes[publisher_notes]">{{$publisher->hasAttributeOrEmpty("publisher_notes")}}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">Edit Publisher</button>
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