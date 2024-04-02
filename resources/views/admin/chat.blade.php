@extends('layouts.head')
@section('css')
<meta name="csrf-token" content="{{csrf_token()}}" />
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
<style>
    .cb {
        height: 500px !important;
        overflow-y: auto;
    }

    .cb-child {
        height: 480px !important;
    }

    .cb-btn {
        width: 130px !important
    }

    .notif {
        height: 25px;
        width: 25px;
        line-height: 20px;
        font-size: 14px
    }

    .d-flex h6 {
        margin-right: 70px
    }

</style>
@endsection
@section('content')
<div class="row h-100">
    <div class="col-lg-4 col-md-5 col-sm-12">
        <form class="card" action="{{route('chat_update')}}" method="post">
            @csrf
            <div class="card-header font-weight-bold">إعدادات المحادثة:</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">هل تريد تعطيل غرفة الدردشة؟</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item text-no-wrap">
                            <input type="radio" name="chat_disable" value="0" class="form-selectgroup-input" {{ env('CHAT_DISABLED') == '1' ? '' : 'checked' }}>
                            <span class="form-selectgroup-label">لا</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="chat_disable" value="1" class="form-selectgroup-input" {{ env('CHAT_DISABLED') == '1' ? 'checked' : '' }}>
                            <span class="form-selectgroup-label">نعم</span>
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">مرفق الوسائط:</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <input class="form-check-input" value="1" name="attachment_status" type="checkbox" {{ env('CHAT_ATTACHMENT') == '1' ? 'checked' : '' }}>
                        </span>
                        <input type="text" class="form-control" name="attachment_size" placeholder="1024" value="{{env('CHAT_ATTACH_KB')}}">
                        <span class="input-group-text">كيلو بايت</span>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">الكلمات الخاضعة للرقابة:</label>
                    <input type="text" class="form-control" name="censored_words" value="{{$data['censored']}}">
                </div>
                <div>
                    <label class="form-label text-yellow font-weight-bold">رسالة تحذير الدردشة:</label>
                    <textarea class="form-control" name="warning_message" data-toggle="autosize" style="height:150px;">{{$data['warning']}}</textarea>
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">تغيير الاعدادات</button>
            </div>
    </div>
    </form>
    <div class="col-lg-8 col-md-7 col-sm-12">
        <div class="card">
            <div id="userinfo" class="card-header d-flex">
                غرفة الدردشة العالمية
                <div class="ml-auto">
                    <a onclick="getMessage();" class="cursor-pointer text-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"></path>
                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -5v5h5"></path>
                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 5v-5h-5"></path>
                        </svg>
                    </a>
                    <a href="{{route('chat_del_all')}}" class="ml-3 cursor-pointer text-red">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" />
                            <line x1="4" y1="7" x2="20" y2="7" />
                            <line x1="10" y1="11" x2="10" y2="17" />
                            <line x1="14" y1="11" x2="14" y2="17" />
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                    </a>
                </div>
            </div>
            <div id="msg-parent" class="card-body cb align-items-end">
                <div id='msg' class="w-100">
                    <div class="cb-child d-flex align-items-center justify-content-center">
                        انقر على المحادثة من اليسار لرؤية الرسائل هنا.
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex">
                <input type="hidden" id="userid" />
                <input id="msg-input" type="text" class="form-control mr-3" placeholder="اكتب هنا شيئا!">
                <a id='sbtn' class="ml-auto btn btn-success cb-btn text-white" onclick="sendMessage();">ارسال</a>
            </div>
        </div>
    </div>
</div>
<form method="post" action="{{route('support_del_full')}}" class="modal modal-blur fade" id="qs-del" tabindex="-1" role="dialog" aria-hidden="true">
    @csrf
    <input type="hidden" name="id" id="qs-id">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">هل أنت متأكد؟</div>
                <div>أنت على وشك إزالة هذه المحادثة بأكملها.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">يلغي</button>
                <button type="submit" class="btn btn-danger">نعم، احذفه</button>
            </div>
        </div>
    </div>
</form>
@endsection
@section('javascript')
<script>
    var submit = false;
    $(document).on("click", ".btn-del", function (ev) {
        ev.preventDefault();
        $("#qs-id").val($(this).data('id'));
    });
    var element = document.getElementById("msg");
    var scroller = document.getElementById("msg-parent");

    $(document).ready(function () {
        getMessage();
    });

    function ractify(themsg) {
        if (themsg.startsWith("https://")) {
            themsg = themsg.replace("/api/", "/");
            if (themsg.endsWith(".jpeg") || themsg.endsWith(".jpg") ||
                themsg.endsWith(".png") || themsg.endsWith(".gif")) {
                return '<img src="' + themsg + '" width="200px">';
            } else if (themsg.endsWith(".mp3") || themsg.endsWith(".mp4")) {
                return '<audio controls><source src="' + themsg + '">' + themsg + '</audio>';
            }
        }
        return themsg;
    }

    function getMessage() {
        element.innerHTML = '<div class="cb-child d-flex align-items-center justify-content-center">Please wait...</div>';
        $.ajax({
            type: 'POST',
            url: '{{route("chat_quick")}}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                var output = '';
                var length = data.msgs.length;
                for (var i = 0; i < length; i++) {
                    var object = data.msgs[i];
                    object.message = ractify(object.message);
                    if (object.is_staff == 1) {
                        output += '<div class="d-flex ml-6"><div class="ml-auto rounded bg-dark text-white p-2 my-3 ml-6 mr-0"><div class="d-flex"><a class="h5 mr-2 text-yellow font-weight-bold" href="{{route("userinfo")}}?userid=' + object.userid + '" target=_blank>' + object.name + '</a><h6>[' + object.updated_at + ']</h6><a class="cursor-pointer ml-auto mr-2" onclick="delMessage(' + object.id + ');">X</a></div>' + object.message + '</div></div>';
                    } else {
                        output += '<div class="d-flex mr-6"><div class="mr-auto rounded bg-blue-lt p-2 my-3 ml-0"><div class="d-flex"><a class="h5 mr-2 text-blue font-weight-bold" href="{{route("userinfo")}}?userid=' + object.userid + '" target=_blank>' + object.name + '</a><h6 class="text-dark">[' + object.updated_at + ']</h6><a class="cursor-pointer ml-auto mr-2" onclick="delMessage(' + object.id + ');">X</a></div>' + object.message + '</div></div>';
                    }
                }
                element.innerHTML = output;
                $("#userid").val(uid);
                $("#userinfo").html(name + '<a onclick="markMessage();" class="ml-auto btn btn-sm btn-secondary text-white">Mark as read</a>');
                scroller.scrollTop = scroller.scrollHeight;
                submit = false;
            },
            error: function (request, status, error) {
                submit = false;
                alert(request.responseText);
            }
        });
    }

    function sendMessage() {
        var mg = $("#msg-input").val();
        if (mg == null || mg == '') return;
        if (submit == false) {
            submit = true;
            $("#sbtn").text('Sending...');
            $.ajax({
                type: 'POST',
                url: '{{route("chat_send")}}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: JSON.stringify({
                    msg: mg
                }),
                success: function (dta) {
                    var output = '';
                    var length = dta.msgs.length;
                    for (var i = 0; i < length; i++) {
                        var object = dta.msgs[i];
                        object.message = ractify(object.message);
                        if (object.is_staff == 1) {
                            output += '<div class="d-flex ml-6"><div class="ml-auto rounded bg-dark text-white p-2 my-3 ml-6 mr-0"><div class="d-flex"><a class="h5 mr-2 text-yellow font-weight-bold" href="{{route("userinfo")}}?userid=' + object.userid + '" target=_blank>' + object.name + '</a><h6>[' + object.updated_at + ']</h6><a class="cursor-pointer ml-auto mr-2" onclick="delMessage(' + object.id + ');">X</a></div>' + object.message + '</div></div>';
                        } else {
                            output += '<div class="d-flex mr-6"><div class="mr-auto rounded bg-blue-lt p-2 my-3 ml-0"><div class="d-flex"><a class="h5 mr-2 text-blue font-weight-bold" href="{{route("userinfo")}}?userid=' + object.userid + '" target=_blank>' + object.name + '</a><h6 class="text-dark">[' + object.updated_at + ']</h6><a class="cursor-pointer ml-auto mr-2" onclick="delMessage(' + object.id + ');">X</a></div>' + object.message + '</div></div>';
                        }
                    }
                    element.innerHTML = output;
                    $("#msg-input").val('')
                    $("#sbtn").text('Send');
                    scroller.scrollTop = scroller.scrollHeight;
                    submit = false;
                },
                error: function (request, status, error) {
                    submit = false;
                    alert(request.responseText);
                }
            });
        }
    }

    function delMessage(id) {
        if (id == null || id == '') return;
        if (submit == false) {
            submit = true;
            $.ajax({
                type: 'POST',
                url: '{{route("chat_del")}}?id=' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (dta) {
                    var output = '';
                    var length = dta.msgs.length;
                    for (var i = 0; i < length; i++) {
                        var object = dta.msgs[i];
                        object.message = ractify(object.message);
                        if (object.is_staff == 1) {
                            output += '<div class="d-flex ml-6"><div class="ml-auto rounded bg-dark text-white p-2 my-3 ml-6 mr-0"><div class="d-flex"><a class="h5 mr-2 text-yellow font-weight-bold" href="{{route("userinfo")}}?userid=' + object.userid + '" target=_blank>' + object.name + '</a><h6>[' + object.updated_at + ']</h6><a class="cursor-pointer ml-auto mr-2" onclick="delMessage(' + object.id + ');">X</a></div>' + object.message + '</div></div>';
                        } else {
                            output += '<div class="d-flex mr-6"><div class="mr-auto rounded bg-blue-lt p-2 my-3 ml-0"><div class="d-flex"><a class="h5 mr-2 text-blue font-weight-bold" href="{{route("userinfo")}}?userid=' + object.userid + '" target=_blank>' + object.name + '</a><h6 class="text-dark">[' + object.updated_at + ']</h6><a class="cursor-pointer ml-auto mr-2" onclick="delMessage(' + object.id + ');">X</a></div>' + object.message + '</div></div>';
                        }
                    }
                    element.innerHTML = output;
                    scroller.scrollTop = scroller.scrollHeight;
                    submit = false;
                },
                error: function (request, status, error) {
                    submit = false;
                    alert(request.responseText);
                }
            });
        }
    }

</script>
@endsection