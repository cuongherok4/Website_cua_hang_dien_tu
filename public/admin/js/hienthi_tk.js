$(document).ready(function() {
    $('.edit-btn').click(function() {
        $('#user_id').val($(this).data('id'));
        $('#hoten').val($(this).data('hoten'));
        $('#sdt').val($(this).data('sdt'));
        $('#email').val($(this).data('email'));
        $('#taikhoan').val($(this).data('taikhoan'));
        $('#matkhau').val($(this).data('matkhau'));
        $('#quyen').val($(this).data('quyen'));
        $('#modalLabel').text('Chỉnh sửa người dùng');
    });

    $('#btnAddNew').click(function() {
        $('#user_id').val('');
        $('#hoten').val('');
        $('#sdt').val('');
        $('#email').val('');
        $('#taikhoan').val('');
        $('#matkhau').val('');
        $('#quyen').val('User');
        $('#modalLabel').text('Thêm người dùng mới');
    });
});