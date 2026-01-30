@php
    $folder = session('folder_name');
@endphp

@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Edit Letter
@endsection

@section('table_title')
    Edit Letter
@endsection

@section('style')
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/theme.css">
    <style>
        .letter-preview {
            width: 100%;
            border: 1px solid #000;
            padding: 20px;
            margin-top: 20px;
            background: #fff;
            box-sizing: border-box;
        }

        .preview-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .preview-section img {
            max-height: 80px;
            object-fit: contain;
        }

        .body-content {
            margin-bottom: 15px;
        }

        .flex-left {
            flex-direction: row;
        }

        .flex-right {
            flex-direction: row-reverse;
        }

        .flex-center {
            justify-content: center;
            text-align: center;
            flex-wrap: wrap;
        }

        .flex-center img,
        .flex-center .text {
            margin: 0 auto;
        }

        .text {
            max-width: 70%;
        }
    </style>
@endsection

@section('table-content')
    @if (session('user_permissions.hrmodule.letters.edit') == '1')
        <form id="letterupdateform" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="token" value="{{ session('api_token') }}" />
            <input type="hidden" name="user_id" value="{{ session('user_id') }}" />
            <input type="hidden" name="company_id" value="{{ session('company_id') }}" />

            <div class="form-group">
                <label>Letter Name <span class="text-danger">*</span></label>
                <input type="text" name="letter_name" id="letter_name" class="form-control"
                    placeholder="Enter letter Name">
                <span class="error-msg" id="error-letter_name" style="color:red"></span>
            </div>
            <div class="card mb-3">
                <div class="card-header">Header</div>
                <div class="card-body">
                    <div class="form-row mb-2">
                        <div class="col-md-4">
                            <label>Header Image</label>
                            <input type="file" name="header_image" id="header_image"class="form-control">
                            <span class="error-msg" style="color:red"></span>
                            <img id="headerPreview" src="" style="max-height:80px; display:none;">
                        </div>
                        <div class="col-md-4">
                            <label>Alignment</label>
                            <select name="header_align" id="header_align" class="form-control">
                                <option value="left">Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Width (%)</label>
                            <input type="number" name="header_width" id="header_width" class="form-control" value="30">
                        </div>
                    </div>
                    <label>Header Content</label>
                    <textarea name="header_content" id="header_content" placeholder="Enter header content" class="form-control"></textarea>
                </div>
            </div>


            <div class="card mb-3">
                <div class="card-header">Body</div>
                <div class="card-body">
                    <textarea name="body_content" id="body_content" placeholder="Enter body content" class="form-control"></textarea>
                </div>
            </div>


            <div class="card mb-3">
                <div class="card-header">Footer</div>
                <div class="card-body">
                    <div class="form-row mb-2">
                        <div class="col-md-4">
                            <label>Footer Image</label>
                            <input type="file" name="footer_image" id="footer_image" class="form-control">
                            <img id="footerPreview" src="" style="max-height:80px; display:none;">
                        </div>
                        <div class="col-md-4">
                            <label>Alignment</label>
                            <select name="footer_align" id="footer_align" class="form-control">
                                <option value="left">Left</option>
                                <option value="center">Center</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Width (%)</label>
                            <input type="number" name="footer_width" id="footer_width" class="form-control" value="30">
                        </div>
                    </div>
                    <label>Footer Content</label>
                    <textarea name="footer_content" id="footer_content" placeholder="Enter Footer content" class="form-control"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Letter</button>
            </div>
        </form>
    @endif
@endsection

@push('ajax')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        loaderhide();
        let headerEditor, bodyEditor, footerEditor;

        ClassicEditor.create(document.querySelector('#header_content'))
            .then(editor => headerEditor = editor);

        ClassicEditor.create(document.querySelector('#body_content'))
            .then(editor => bodyEditor = editor);

        ClassicEditor.create(document.querySelector('#footer_content'))
            .then(editor => footerEditor = editor);
        var edit_id = @json($edit_id);
        let url = "{{ route('letter.edit', ['id' => '__id__']) }}".replace('__id__', edit_id);

        function loadletter() {
            $.ajax({
                url: url,
                method: "get",
                data: {
                    user_id: "{{ session()->get('user_id') }}",
                    company_id: "{{ session()->get('company_id') }}",
                    token: "{{ session()->get('api_token') }}"
                },
                success: function(response) {
                    letterdata = response.data;
                    $("#letter_name").val(letterdata.letter_name);
                    $("#header_align").val(letterdata.header_align);
                    $("#header_width").val(letterdata.header_width);
                    headerEditor.setData(letterdata.header_content ?? '');
                    bodyEditor.setData(letterdata.body_content ?? '');
                    footerEditor.setData(letterdata.footer_content ?? '');
                    $("#footer_align").val(letterdata.footer_align);
                    $("#footer_width").val(letterdata.footer_width);
                    if (letterdata.header_image) {
                        $("#headerPreview")
                            .attr("src", "{{ asset('') }}" + letterdata.header_image)
                            .show();
                    }
                    if (letterdata.footer_image) {
                        $("#footerPreview")
                            .attr("src", "{{ asset('') }}" + letterdata.footer_image)
                            .show();
                    }
                }
            })
        }
        loadletter();


        let updateUrl = "{{ route('letter.update', ['id' => '__id__']) }}".replace('__id__', edit_id);

        $("#letterupdateform").on("submit", function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            // CKEditor content
            formData.set('header_content', headerEditor.getData());
            formData.set('body_content', bodyEditor.getData());
            formData.set('footer_content', footerEditor.getData());

            // Laravel PUT method
            formData.append('_method', 'PUT');

            $.ajax({
                url: updateUrl,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,

                success: function(response) {
                    loaderhide();

                    if (response.status === "success") {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                        window.location.href = "{{ route('admin.letter') }}";
                    } else {
                         Toast.fire({
                            icon: "error",
                            title: response.message
                        });
                      
                    }
                },
                error: function(xhr) {
                    loaderhide();

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $("#error-" + key).text(value[0]);
                        });
                    } else {
                        toastr.error("Something went wrong");
                    }
                }
            });
        });
    </script>
@endpush
