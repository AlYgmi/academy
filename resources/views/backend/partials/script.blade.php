<script src="{{asset('public/backend/js/jquery-ui.js')}}"></script>

@if(isRtl())
    <script src="{{ asset('public') }}/js/popper.min.js"></script>
    <script src="{{asset('public/js/bootstrap.rtl.min.js') }}"></script>
@else
    <script src="{{asset('public/js/bootstrap.bundle.min.js') }}"></script>
@endif

<script>
    if ($('#main-nav-for-chat').length) {
    } else {
        $('#main-content').append('<div id="main-nav-for-chat" style="visibility: hidden;"></div>');
    }

    if ($('#admin-visitor-area').length) {
    } else {
        $('#main-content').append('<div id="admin-visitor-area" style="visibility: hidden;"></div>');
    }
</script>

@if(isModuleActive('Chat'))
    <script src="{{ asset('public/js/app.js') }}"></script>
@endif
<script src="{{asset('public/backend/js/jquery.data-tables.js')}}"></script>
<script src="{{asset('public/backend/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('public/backend/js/buttons.flash.min.js')}}"></script>
<script src="{{asset('public/backend/js/jszip.min.js')}}"></script>
<script src="{{asset('public/backend/js/pdfmake.min.js')}}"></script>
<script src="{{asset('public/backend/js/vfs_fonts.min.js')}}"></script>
<script src="{{asset('public/backend/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('public/backend/js/buttons.print.min.js')}}"></script>
<script src="{{asset('public/backend/js/dataTables.rowReorder.min.js')}}"></script>
<script src="{{asset('public/backend/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('public/backend/js/buttons.colVis.min.js')}}"></script>
<script src="{{asset('public/backend/js/nice-select.min.js')}}"></script>
<script src="{{asset('public/backend/js/jquery.magnific-popup.min.js')}}"></script>
<script src="{{asset('public/backend/js/fastselect.standalone.min.js')}}"></script>
<script src="{{asset('public/backend/js/moment.min.js')}}"></script>
<script src="{{asset('public/backend/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('public/backend/js/bootstrap-datepicker.min.js')}}"></script>
<script>
    ;(function ($) {
        $.fn.datepicker.dates[LANG] = {!! json_encode(__('calender')) !!}
    }(jQuery));

</script>
<!-- summernote  -->
<script src="{{asset('public/backend/js/summernote-bs4.min.js')}}"></script>
<!-- nestable  -->

<!-- metisMenu js  -->
<script src="{{asset('public/backend/js/metisMenu.min.js')}}"></script>
<!-- progressbar  -->
<script src="{{asset('public/backend/vendors/progressbar/circle-progress.min.js')}}"></script>
<!-- color picker  -->
<script src="{{asset('public/backend/vendors/color_picker/colorpicker.min.js')}}"></script>
<script src="{{asset('public/backend/vendors/color_picker/colorpicker_script.js')}}"></script>
<script src="{{asset('public/backend/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('public/backend/js/main.js')}}"></script>
<script src="{{asset('public/backend/js/custom.js')}}"></script>
<script src="{{asset('public/backend/js/footer.js')}}"></script>
<script src="{{asset('public/backend/js/developer.js')}}"></script>
<script src="{{asset('public/backend/js/treeview.js')}}"></script>
<script src="{{asset('public/js/toastr.min.js')}}"></script>
<script src="{{asset('public/backend/vendors/select2/select2.min.js')}}"></script>

<script src="{{asset('public/backend/js/backend.js')}}"></script>
<script src="{{asset('public/backend/js/search.js')}}"></script>
<script src="{{asset('public/backend/js/filepond.min.js')}}"></script>
<script src="{{asset('public/backend/js/filepond-plugin-image-preview.min.js')}}"></script>
<script src="{{asset('public/backend/js/filepond.jquery.js')}}"></script>


<script>
    if ($('.Crm_table_active3').length) {
        let datatable = $('.Crm_table_active3').DataTable({
            bLengthChange: true,
            "bDestroy": true,
            language: {
                emptyTable: "{{ __("common.No data available in the table") }}",
                search: "<i class='ti-search'></i>",
                searchPlaceholder: '{{ __("common.Quick Search") }}',
                paginate: {
                    next: "<i class='ti-arrow-right'></i>",
                    previous: "<i class='ti-arrow-left'></i>"
                }
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copyHtml5',
                    text: '<i class="far fa-copy"></i>',
                    title: $("#logo_title").val(),
                    titleAttr: '{{__('common.Copy')}}',
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="far fa-file-excel"></i>',
                    titleAttr: '{{__('common.Excel')}}',
                    title: $("#logo_title").val(),
                    margin: [10, 10, 10, 0],
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    },

                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="far fa-file-alt"></i>',
                    titleAttr: '{{__('common.CSV')}}',
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="far fa-file-pdf"></i>',
                    title: $("#logo_title").val(),
                    titleAttr: '{{__('common.PDF')}}',
                    exportOptions: {
                        columns: ':visible',
                        columns: ':not(:last-child)',
                    },
                    orientation: 'landscape',
                    pageSize: 'A4',
                    margin: [0, 0, 0, 12],
                    alignment: 'center',
                    header: true,
                    customize: function (doc) {
                        doc.content[0].table.widths =
                            Array(doc.content[0].table.body[0].length + 1).join('*').split('');
                    }

                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>',
                    titleAttr: '{{__('common.Print')}}',
                    title: $("#logo_title").val(),
                    exportOptions: {
                        columns: ':not(:last-child)',
                    }
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    postfixButtons: ['colvisRestore']
                }
            ],
            columnDefs: [{
                visible: false
            }],
            responsive: true,
            paging:true,
            "lengthChange": true,
            "lengthMenu": [ [10, 25, 50,100, -1], [10, 25, 50,100, "All"] ]
        });

        $(".selectAllQuiz").on("click", function () {
            let totalQuestions = $('#totalQuestions');
            let totalMarks = $('#totalMarks');

            if ($(this).is(':checked')) {
                datatable.$("input[type='checkbox']").prop('checked', true);
            } else {
                datatable.$("input[type='checkbox']").prop('checked', false);
            }
            let online_exam_id = $('#online_exam_id').val();
            let ques_assign = $('.ques_assign').val();
            let token = $('.csrf_token').val();
            var myCheckboxes = [];
            datatable.$("input[type='checkbox']").each(function () {
                if ($(this).is(':checked')) {
                    myCheckboxes.push($(this).val());
                }
            });
            $.ajax({
                type: 'POST',
                url: ques_assign,
                data: {
                    '_token': token,
                    online_exam_id: online_exam_id,
                    questions: myCheckboxes,
                },
                success: function (data) {
                    totalQuestions.html(data.totalQus);
                    totalMarks.html(data.totalMarks);
                    toastr.success('{{__('common.Successfully Assign')}}', '{{__('common.Success')}}');
                },
                error: function (data) {
                    toastr.error('{{__('common.Something Went Wrong')}}', '{{__('common.Error')}}')
                }
            });
        });

    }
</script>
<script>
    setTimeout(function () {
        $('.preloader').fadeOut('slow', function () {
            $(this).remove();
        });
    }, 0);
</script>

<script>
    if ($('#main-nav-for-chat').length) {
    } else {
        $('#main-content').append('<div id="main-nav-for-chat" style="display: none;"></div>');
    }

    if ($('#admin-visitor-area').length) {
        console.log('find')
    } else {
        console.log('miss')
        $('#main-content').append('<div id="admin-visitor-area" style="visibility: hidden;"></div>');
    }
</script>

<script>
    //datatable caching
    $.fn.dataTable.pipeline = function (opts) {
        // Configuration options
        var conf = $.extend({
            pages: 5,     // number of pages to cache
            url: '',      // script url
            data: null,   // function or object with parameters to send to the server
                          // matching how `ajax.data` works in DataTables
            method: 'GET' // Ajax HTTP method
        }, opts);
        // Private variables for storing the cache
        var cacheLower = -1;
        var cacheUpper = null;
        var cacheLastRequest = null;
        var cacheLastJson = null;
        return function (request, drawCallback, settings) {
            var ajax = false;
            var requestStart = request.start;
            var drawStart = request.start;
            var requestLength = request.length;
            var requestEnd = requestStart + requestLength;

            if (settings.clearCache) {
                // API requested that the cache be cleared
                ajax = true;
                settings.clearCache = false;
            } else if (cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper) {
                // outside cached data - need to make a request
                ajax = true;
            } else if (JSON.stringify(request.order) !== JSON.stringify(cacheLastRequest.order) ||
                JSON.stringify(request.columns) !== JSON.stringify(cacheLastRequest.columns) ||
                JSON.stringify(request.search) !== JSON.stringify(cacheLastRequest.search)
            ) {
                // properties changed (ordering, columns, searching)
                ajax = true;
            }

            // Store the request for checking next time around
            cacheLastRequest = $.extend(true, {}, request);

            if (ajax) {
                // Need data from the server
                if (requestStart < cacheLower) {
                    requestStart = requestStart - (requestLength * (conf.pages - 1));

                    if (requestStart < 0) {
                        requestStart = 0;
                    }
                }
                cacheLower = requestStart;
                cacheUpper = requestStart + (requestLength * conf.pages);

                request.start = requestStart;
                request.length = requestLength * conf.pages;

                // Provide the same `data` options as DataTables.
                if (typeof conf.data === 'function') {
                    // As a function it is executed with the data object as an arg
                    // for manipulation. If an object is returned, it is used as the
                    // data object to submit
                    var d = conf.data(request);
                    if (d) {
                        $.extend(request, d);
                    }
                } else if ($.isPlainObject(conf.data)) {
                    // As an object, the data given extends the default
                    $.extend(request, conf.data);
                }

                return $.ajax(
                    {
                        "type": conf.method,
                        "url": conf.url,
                        "data": request,
                        "dataType": "json",
                        "cache": false,
                        "success": function (json) {
                            cacheLastJson = $.extend(true, {}, json);

                            if (cacheLower != drawStart) {
                                json.data.splice(0, drawStart - cacheLower);
                            }
                            if (requestLength >= -1) {
                                json.data.splice(requestLength, json.data.length);
                            }

                            drawCallback(json);
                        }
                    });
            } else {
                var json = $.extend(true, {}, cacheLastJson);
                json.draw = request.draw; // Update the echo for each response
                json.data.splice(0, requestStart - cacheLower);
                json.data.splice(requestLength, json.data.length);

                drawCallback(json);
            }
        }
    };

    // Register an API method that will empty the pipelined data, forcing an Ajax
    // fetch on the next draw (i.e. `table.clearPipeline().draw()`)
    $.fn.dataTable.Api.register('clearPipeline()', function () {
        return this.iterator('table', function (settings) {
            settings.clearCache = true;
        });
    });
</script>

<script>
    $(function () {
        var filepond = $('.filepond');

        filepond.filepond();

        filepond.on('FilePond:addfile', function (e) {
            console.log('file added event', e);
        });
        FilePond.setOptions({
            chunkUploads: true,
            // allowProcess: true,
            'allowMultiple': false,
            server: {
                url: '{{url('/')}}/filepond/api/process',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });

    });
</script>
<script src="{{ asset('public/chat/js/custom.js') }}"></script>
@if(isModuleActive("WhatsappSupport"))
    <script src="{{ asset('public/whatsapp-support/scripts.js') }}"></script>
@endif
@stack('scripts')
