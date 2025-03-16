setTimeout(function() {
    let alertBox = document.querySelector("#alert-container .alert");
    if (alertBox) {
        alertBox.classList.remove("show");
        alertBox.classList.add("fade");
        setTimeout(() => alertBox.remove(), 500); // Xóa phần tử sau khi hiệu ứng kết thúc
    }
}, 5000);