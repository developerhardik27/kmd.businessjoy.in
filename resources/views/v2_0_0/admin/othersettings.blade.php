@php
    $folder = session('folder_name');
@endphp
@extends($folder.'.admin.masterpage')

@section('page_title')
    {{ config('app.name') }} - Invoice Other Settings
@endsection
@section('title')
    Other settings
@endsection
@section('style')
    <style>
        .my-custom-scrollbar {
            position: relative;
            height: 200px;
            overflow: auto;
        }

        .table-wrapper-scroll-y {
            display: block;
        }
    </style>
@endsection

@section('page-content')
    <div id="content-page" class="content-page ">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-12">
                    <button title="Edit Other Settings" type="button" id="editoverdueday" class="float-right m-4 btn btn-outline-success btn-rounded btn-sm my-0">
                        <i class="ri-edit-fill"></i>
                    </button>
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Other Settings </h4>

                            </div>
                        </div>
                        <div class="iq-card-body" id="appendform">
                            <form id="overduedaysform" style="display: none">
                                @csrf
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-6">
                                            <input type="hidden" name="token" class="form-control"
                                                value="{{ session('api_token') }}" placeholder="token" required />
                                            <input type="hidden" value="{{ $user_id }}" name="user_id"
                                                class="form-control">
                                            <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                class="form-control">
                                            Invoice Overdue Days : <input type="number" id="overdue_day" name='overdue_day'
                                                class="form-control" placeholder="overdue days" min="1" required />
                                            <span class="error-msg" id="error-overdue_day" style="color: red"></span><br>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <button type="reset" id="overduereset" class="btn iq-bg-danger">Reset</button>
                                        </div>
                                        <div class="col-sm-6">
                                            Year Starting Date : <input type="date" id="year_start_date"
                                                name='year_start_date' class="form-control" placeholder="Year starting date"
                                                required />
                                            <span class="error-msg" id="error-year_start_date" style="color: red"></span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </form>
                            Current Invoice Overdue Days : <span id="overduedays"></span> <br>
                            Year Starting Date : <span id="yearstartdate"></span> <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-12">
                    <div class="iq-card">
                        <div class="iq-card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h4 class="card-title">Invoice Terms & condtions Settings </h4>
                            </div>
                        </div>
                        <div class="iq-card-body">
                            <form id="tcform">
                                @csrf
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-sm-6">
                                            <input type="hidden" name="edit_id" class="form-control" id="edit_id"
                                                required />
                                            <input type="hidden" name="token" class="form-control"
                                                value="{{ session('api_token') }}" required />
                                            <input type="hidden" value="{{ $user_id }}" name="user_id"
                                                class="form-control">
                                            <input type="hidden" value="{{ $company_id }}" name="company_id"
                                                class="form-control">
                                            <textarea class="form-control" name='t_and_c' id="t_and_c" rows="2"
                                                placeholder="Enter your Invoice Terms & Conditions..."></textarea>
                                            <span class="error-msg" id="error-t_and_c" style="color: red"></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <button type="reset" class="btn iq-bg-danger">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div class="table-wrapper-scroll-y my-custom-scrollbar">
                                <table id="data"
                                    class="table  table-bordered display table-responsive-md table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th>Sr</th>
                                            <th>Terms & Conditions</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabledata">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {

            // companyId and userId both are required in every ajax request for all action *************
            // response status == 200 that means response succesfully recieved
            // response status == 500 that means database not found
            // response status == 422 that means api has not got valid or required data

            loaderhide();

            var overdueday = '';

            function getoverduedays() {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('getoverduedays.index') }}',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.overdueday != '') {
                            loaderhide();
                            overdueday = response.overdueday[0]['overdue_day'];
                            year_start = response.overdueday[0]['year_start'];
                            $('#overduedays').html(`<b>${overdueday}</b> `)
                            $('#yearstartdate').html(`<b>${year_start}</b> `)
                            $('#overdue_day').val(overdueday);
                            $('#year_start_date').val(year_start);
                            $('#overdue_day').attr('data-id', response.overdueday[0]['id']);
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            loaderhide();
                            $('#tabledata').append(
                                `<tr><td colspan='3' >No Data Found</td></tr>`)
                        }
                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            }
            getoverduedays();
            $('#editoverdueday').on('click', function() {
                getoverduedays();
                if (overdueday != '') {
                    $('#overduedaysform').show();
                }
            });

            $('#overduedaysform').submit(function(event) {
                event.preventDefault();
                loadershow();
                editid = $('#overdue_day').data('id');
                url = "/api/getoverduedays/update/" + editid;
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            $('#overduedaysform').hide();
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            $('#overduedaysform')[0].reset();
                            getoverduedays();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error('something went wrong !');
                        }

                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
                    }
                });
            })

            // get terms and conditions
            function loaddata() {
                loadershow();
                $('#tabledata').empty();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('termsandconditions.index') }}',
                    data: {
                        user_id: "{{ session()->get('user_id') }}",
                        company_id: "{{ session()->get('company_id') }}",
                        token: "{{ session()->get('api_token') }}"
                    },
                    success: function(response) {
                        if (response.status == 200 && response.termsandconditions != '') {
                            loaderhide();
                            global_response = response;
                            var id = 1;
                            $.each(response.termsandconditions, function(key, value) {
                                $('#tabledata').append(` <tr>
                                                        <td>${id}</td>
                                                        <td class='text-left' style="white-space: pre-line;">${value.t_and_c}</td>
                                                        <td>
                                                            @if (session('user_permissions.invoicemodule.invoicesetting.edit') == '1')
                                                                ${value.is_active == 1 ? '<div id=status_'+value.id+ '> <button data-status='+value.id+' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >active</button></div>'  : '<div id=status_'+value.id+ '><button data-status= '+value.id+' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >InActive</button></div>'}
                                                            @else
                                                              -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span>
                                                                <button type="button" data-id= '${value.id}'
                                                                    class=" del-btn btn iq-bg-danger btn-rounded btn-sm my-0">
                                                                    <i class="ri-delete-bin-fill"></i>
                                                                </button>
                                                            </span>
                                                        </td>
                                                    </tr>`);
                                id++;
                            });
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            loaderhide();
                            $('#tabledata').append(`<tr><td colspan='3' >No Data Found</td></tr>`)
                        }
                        // You can update your HTML with the data here if needed
                    },
                    error: function(error) {
                        loaderhide();
                        console.error('Error:', error);
                    }
                });
            }

            //call function for loaddata
            loaddata();

            //  t & cstatus update deactive              
            $(document).on("click", ".status-active", function() {
                if (confirm('Are you really want to change status to inactive ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    $.ajax({
                        type: 'put',
                        url: '/api/termsandconditions/statusupdate/' + statusid,
                        data: {
                            status: '0',
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                loaderhide();
                                toastr.success(response.message);
                                $('#status_' + statusid).html('<button data-status= ' +
                                    statusid +
                                    ' class="status-deactive btn btn-outline-dark btn-rounded btn-sm my-0" >Inactive</button>'
                                );
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                toastr.error('something went wrong !');
                                loaderhide();
                            }
                        }
                    });
                }
            });

            //  t & c status update  active            
            $(document).on("click", ".status-deactive", function() {
                if (confirm('Are you really want to change status to active ?')) {
                    loadershow();
                    var statusid = $(this).data('status');
                    $.ajax({
                        type: 'put',
                        url: '/api/termsandconditions/statusupdate/' + statusid,
                        data: {
                            status: '1',
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}"
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                loaderhide();
                                toastr.success(response.message);
                                $('#status_' + statusid).html('<button data-status= ' +
                                    statusid +
                                    ' class="status-active btn btn-outline-success btn-rounded btn-sm my-0" >Active</button>'
                                );
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                loaderhide();
                                toastr.error('something went wrong !');
                            }
                        }
                    });
                }
            });

            // delete terms and conditions              
            $(document).on("click", ".del-btn", function() {
                if (confirm('Are you really want to delete this record ?')) {
                    loadershow();
                    var deleteid = $(this).data('id');
                    var row = this;
                    $.ajax({
                        type: 'put',
                        url: '/api/termsandconditions/delete/' + deleteid,
                        data: {
                            token: "{{ session()->get('api_token') }}",
                            company_id: "{{ session()->get('company_id') }}",
                            user_id: "{{ session()->get('user_id') }}",
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                loaderhide();
                                toastr.success(response.message);
                                $(row).closest("tr").fadeOut();
                            } else if (response.status == 500) {
                                toastr.error(response.message);
                                loaderhide();
                            } else {
                                loaderhide();
                                toastr.error('something went wrong !');
                            }
                        }
                    });
                }
            });


            // terms and conditions form submit
            $('#tcform').submit(function(event) {
                event.preventDefault();
                loadershow();
                url = "{{ route('termsandconditions.store') }}";
                $('.error-msg').text('');
                const formdata = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formdata,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.status == 200) {
                            loaderhide();
                            // You can perform additional actions, such as showing a success message or redirecting the user
                            toastr.success(response.message);
                            $('#tcform')[0].reset();
                            loaddata();
                        } else if (response.status == 500) {
                            toastr.error(response.message);
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error('something went wrong !');
                        }

                    },
                    error: function(xhr, status, error) {
                        // Handle error response and display validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-' + key).text(value[0]);
                            });
                            loaderhide();
                        } else {
                            loaderhide();
                            toastr.error(
                                'An error occurred while processing your request. Please try again later.'
                            );
                        }
                    }
                });
            })

            // it will may be usefull (terms and conditions edit and save)
            // $(document).on("click", ".edit-btn", function() {
            //     if (confirm("You want edit this T & C ?")) {
            //         loadershow();
            //         var editid = $(this).data('id');
            //         $.ajax({
            //             type: 'get',
            //             url: '/api/termsandconditions/edit/' + editid,
            //             data: {
            //                 token: "{{ session()->get('api_token') }}",
            //                 company_id: " {{ session()->get('company_id') }}",
            //                 user_id: " {{ session()->get('user_id') }}",
            //             },
            //             success: function(response) {
            //                 if (response.status == 200 && response.termsandcondition != '') {
            //                     var termsandcondition = response.termsandcondition;
            //                     loaderhide();
            //                     $('#edit_id').val(editid);
            //                     $('#t_and_c').val(termsandcondition.t_and_c).focus();

            //                 } else if (response.status == 500) {
            //                     toastr.error(response.message);
            //                     loaderhide();
            //                 } else {
            //                     toastr.error('Something Went Wrong! please try again later ');
            //                 }
            //             },
            //             error: function(error) {
            //                 loaderhide();
            //                 console.error('Error:', error);
            //             }
            //         });
            //     }
            // });
            // $('#tcform').submit(function(event) {
            //     event.preventDefault();
            //     loadershow();
            //     editid =  $('#edit_id').val();

            //     if(editid != ''){
            //        url = "/api/termsandconditions/update/" + editid ;
            //     }else{
            //         url = "{{ route('termsandconditions.store') }}" ;
            //     }
            //     $('.error-msg').text('');
            //     const formdata = $(this).serialize();
            //     $.ajax({
            //         type: 'POST',
            //         url: url  ,
            //         data: formdata,
            //         success: function(response) {
            //             // Handle the response from the server
            //             if (response.status == 200) {
            //                 loaderhide();
            //                 // You can perform additional actions, such as showing a success message or redirecting the user
            //                 toastr.success(response.message);
            //                 $('#tcform')[0].reset();
            //                 $('#edit_id').val(null);
            //                 loaddata();
            //             } else if (response.status == 500) {
            //                 toastr.error(response.message);
            //                 loaderhide();
            //             } else {
            //                 loaderhide();
            //                 toastr.error('something went wrong !');
            //             }

            //         },
            //         error: function(xhr, status, error) {
            //             // Handle error response and display validation errors
            //             if (xhr.status === 422) {
            //                 var errors = xhr.responseJSON.errors;
            //                 $.each(errors, function(key, value) {
            //                     $('#error-' + key).text(value[0]);
            //                 });
            //                 loaderhide();
            //             } else {
            //                 loaderhide();
            //                 toastr.error(
            //                     'An error occurred while processing your request. Please try again later.'
            //                 );
            //             }
            //         }
            //     });
            // })

        });
    </script>
@endpush
